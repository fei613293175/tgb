#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
STAGING_SITE="/www/staging/tg-h5-ui-r02/site"
STAGING_PRIVATE="/www/staging/tg-h5-ui-r02/private"
FIXTURE_DIR="${STAGING_PRIVATE}/fixtures"
FIXTURE_META="${FIXTURE_DIR}/r02-auth-visual.json"
BRIDGE_DIR="${STAGING_SITE}/__r02_auth__"
BRIDGE_HISTORY="${STAGING_PRIVATE}/auth-bridge-history"
FIXTURE_USERNAME="tgb_r02_visual"
export TGB_R02_FIXTURE_META="${FIXTURE_META}"
export TGB_R02_FIXTURE_USERNAME="${FIXTURE_USERNAME}"

fail() {
  printf '[R02-AUTH-FIXTURE] ABORT: %s\n' "$1" >&2
  exit 1
}

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${STAGING_SITE}/source/plugin" ] || fail "R02 staging site is absent"
[ -d "${STAGING_PRIVATE}" ] || fail "R02 private directory is absent"
grep -Fq "tgb_stage_r02_main" "${STAGING_SITE}/config/config_global.php" ||
  fail "main database is not the R02 clone"
grep -Fq "tgb_stage_r02_uc" "${STAGING_SITE}/config/config_ucenter.php" ||
  fail "UCenter database is not the R02 clone"

case "${MODE}" in
  create)
    [ ! -e "${FIXTURE_META}" ] || fail "fixture metadata already exists"
    [ ! -e "${BRIDGE_DIR}" ] || fail "login bridge already exists"
    install -d -m 0700 "${FIXTURE_DIR}"

    cd "${STAGING_SITE}"
    php <<'PHP'
<?php
define('CURSCRIPT', 'r02fixture');
$_SERVER['PHP_SELF'] = '/r02fixture.php';
$_SERVER['SCRIPT_NAME'] = '/r02fixture.php';
$_SERVER['SCRIPT_FILENAME'] = '/www/staging/tg-h5-ui-r02/site/r02fixture.php';
$_SERVER['REQUEST_URI'] = '/r02fixture.php';
$_SERVER['HTTP_HOST'] = 'tg-h5-ui-r02.local';
$_SERVER['SERVER_NAME'] = 'tg-h5-ui-r02.local';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
chdir('/www/staging/tg-h5-ui-r02/site');
require '/www/staging/tg-h5-ui-r02/site/source/class/class_core.php';
$discuz = C::app();
$discuz->init();
loaducenter();

$metadataPath = getenv('TGB_R02_FIXTURE_META');
$username = getenv('TGB_R02_FIXTURE_USERNAME');
$email = $username . '@example.com';

if (C::t('common_member')->fetch_by_username($username)) {
    fwrite(STDERR, "[R02-AUTH-FIXTURE] ABORT: staging main fixture already exists\n");
    exit(21);
}
if (uc_get_user($username)) {
    fwrite(STDERR, "[R02-AUTH-FIXTURE] ABORT: staging UCenter fixture already exists\n");
    exit(22);
}

$generatedPassword = bin2hex(random_bytes(24));
$uid = uc_user_register(
    addslashes($username),
    $generatedPassword,
    $email,
    0,
    '',
    '127.0.0.1'
);
if ($uid <= 0) {
    fwrite(STDERR, "[R02-AUTH-FIXTURE] ABORT: UCenter create failed\n");
    exit(23);
}

try {
    $groupId = !empty($_G['setting']['regverify'])
        ? 8
        : (int) $_G['setting']['newusergroupid'];
    $memberPassword = md5(random(10));
    $initial = array(
        'credits' => explode(',', (string) $_G['setting']['initcredits']),
        'profile' => array(),
        'emailstatus' => 0,
    );
    C::t('common_member')->insert(
        $uid,
        $username,
        $memberPassword,
        $email,
        '127.0.0.1',
        $groupId,
        $initial
    );
    $member = C::t('common_member')->fetch($uid);
    if (!$member || $member['username'] !== $username) {
        throw new RuntimeException('main member verification failed');
    }
    $tables = array(
        'common_member',
        'common_member_status',
        'common_member_count',
        'common_member_profile',
        'common_member_field_forum',
        'common_member_field_home',
    );
    $present = 0;
    foreach ($tables as $table) {
        if (C::t($table)->fetch($uid)) {
            $present++;
        }
    }
    if ($present !== count($tables)) {
        throw new RuntimeException('associated row verification failed');
    }
    $metadata = array(
        'schema_version' => 1,
        'fixture' => 'R02_AUTH_VISUAL',
        'username' => $username,
        'uid' => (int) $uid,
        'group_id' => $groupId,
        'created_at' => gmdate('c'),
        'core_rows' => $present,
        'status' => 'ACTIVE',
    );
    if (file_put_contents(
        $metadataPath,
        json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
    ) === false) {
        throw new RuntimeException('metadata write failed');
    }
    chmod($metadataPath, 0600);
    echo "[R02-AUTH-FIXTURE] CREATED uid={$uid} core_rows={$present}\n";
} catch (Throwable $error) {
    if (C::t('common_member')->fetch($uid)) {
        C::t('common_member')->delete_no_validate($uid);
    }
    uc_user_delete($uid);
    fwrite(STDERR, "[R02-AUTH-FIXTURE] ABORT: fixture create rolled back\n");
    exit(24);
}
PHP
    [ -f "${FIXTURE_META}" ] ||
      fail "fixture create did not produce verified metadata"
    ;;

  bridge-on)
    [ -f "${FIXTURE_META}" ] || fail "fixture metadata is absent"
    [ ! -e "${BRIDGE_DIR}" ] || fail "login bridge already exists"
    install -d -o www -g www -m 0755 "${BRIDGE_DIR}"
    BRIDGE_TMP="$(mktemp "${STAGING_PRIVATE}/r02-auth-bridge.XXXXXX.php")"
    trap 'rm -f "${BRIDGE_TMP:-}"' EXIT
    cat > "${BRIDGE_TMP}" <<'PHP'
<?php
define('CURSCRIPT', 'r02authbridge');

$remote = $_SERVER['REMOTE_ADDR'] ?? '';
if ($remote !== '127.0.0.1' && $remote !== '::1') {
    http_response_code(403);
    exit('loopback only');
}

chdir('/www/staging/tg-h5-ui-r02/site');
require '/www/staging/tg-h5-ui-r02/site/source/class/class_core.php';
$discuz = C::app();
$discuz->init();

$member = C::t('common_member')->fetch_by_username('tgb_r02_visual');
if (!$member || empty($member['uid'])) {
    http_response_code(404);
    exit('fixture absent');
}

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
dsetcookie(
    'auth',
    authcode($member['password'] . "\t" . $member['uid'], 'ENCODE'),
    3600,
    1,
    true
);
dsetcookie('mobile', '2', 3600, 1, true);
dheader('Location: /plugin.php?id=xigua_hb&mobile=2');
PHP
    php -l "${BRIDGE_TMP}" >/dev/null
    install -o www -g www -m 0644 "${BRIDGE_TMP}" "${BRIDGE_DIR}/index.php"
    rm -f "${BRIDGE_TMP}"
    trap - EXIT

    CODE="$(curl -sS -o /dev/null -w '%{http_code}' \
      -H 'Host: tg-h5-ui-r02.local' \
      -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
      "http://127.0.0.1:18082/__r02_auth__/")"
    [ "${CODE}" = "302" ] || fail "login bridge probe HTTP ${CODE}"
    printf '[R02-AUTH-FIXTURE] BRIDGE_ACTIVE HTTP=%s\n' "${CODE}"
    ;;

  bridge-off)
    [ -f "${BRIDGE_DIR}/index.php" ] || fail "login bridge is absent"
    RESOLVED_BRIDGE="$(readlink -f "${BRIDGE_DIR}")"
    [ "${RESOLVED_BRIDGE}" = "${BRIDGE_DIR}" ] ||
      fail "unexpected login bridge path"
    install -d -m 0700 "${BRIDGE_HISTORY}"
    HISTORY_ID="$(date '+%Y%m%dT%H%M%S%z')"
    HISTORY_DIR="${BRIDGE_HISTORY}/${HISTORY_ID}"
    [ ! -e "${HISTORY_DIR}" ] || fail "bridge history path exists"
    mv -- "${RESOLVED_BRIDGE}" "${HISTORY_DIR}"
    chmod -R a-w "${HISTORY_DIR}"
    printf '[R02-AUTH-FIXTURE] BRIDGE_REMOVED history=%s\n' "${HISTORY_DIR}"
    ;;

  status)
    [ -f "${FIXTURE_META}" ] || fail "fixture metadata is absent"
    cd "${STAGING_SITE}"
    php <<'PHP'
<?php
define('CURSCRIPT', 'r02fixturestatus');
$_SERVER['PHP_SELF'] = '/r02fixturestatus.php';
$_SERVER['SCRIPT_NAME'] = '/r02fixturestatus.php';
$_SERVER['SCRIPT_FILENAME'] = '/www/staging/tg-h5-ui-r02/site/r02fixturestatus.php';
$_SERVER['REQUEST_URI'] = '/r02fixturestatus.php';
$_SERVER['HTTP_HOST'] = 'tg-h5-ui-r02.local';
$_SERVER['SERVER_NAME'] = 'tg-h5-ui-r02.local';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
chdir('/www/staging/tg-h5-ui-r02/site');
require '/www/staging/tg-h5-ui-r02/site/source/class/class_core.php';
$discuz = C::app();
$discuz->init();
loaducenter();
$username = getenv('TGB_R02_FIXTURE_USERNAME');
$member = C::t('common_member')->fetch_by_username($username);
$ucenter = uc_get_user($username);
if (!$member || !$ucenter || (int) $member['uid'] !== (int) $ucenter[0]) {
    fwrite(STDERR, "[R02-AUTH-FIXTURE] ABORT: fixture parity failed\n");
    exit(31);
}
echo "[R02-AUTH-FIXTURE] STATUS=ACTIVE uid={$member['uid']} parity=PASS\n";
PHP
    if [ -e "${BRIDGE_DIR}" ]; then
      printf '[R02-AUTH-FIXTURE] BRIDGE=ACTIVE\n'
    else
      printf '[R02-AUTH-FIXTURE] BRIDGE=OFF\n'
    fi
    ;;

  destroy)
    [ -f "${FIXTURE_META}" ] || fail "fixture metadata is absent"
    [ ! -e "${BRIDGE_DIR}" ] || fail "disable login bridge before destroy"
    cd "${STAGING_SITE}"
    php <<'PHP'
<?php
define('CURSCRIPT', 'r02fixturedestroy');
$_SERVER['PHP_SELF'] = '/r02fixturedestroy.php';
$_SERVER['SCRIPT_NAME'] = '/r02fixturedestroy.php';
$_SERVER['SCRIPT_FILENAME'] = '/www/staging/tg-h5-ui-r02/site/r02fixturedestroy.php';
$_SERVER['REQUEST_URI'] = '/r02fixturedestroy.php';
$_SERVER['HTTP_HOST'] = 'tg-h5-ui-r02.local';
$_SERVER['SERVER_NAME'] = 'tg-h5-ui-r02.local';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
chdir('/www/staging/tg-h5-ui-r02/site');
require '/www/staging/tg-h5-ui-r02/site/source/class/class_core.php';
$discuz = C::app();
$discuz->init();
loaducenter();
$metadataPath = getenv('TGB_R02_FIXTURE_META');
$username = getenv('TGB_R02_FIXTURE_USERNAME');
$member = C::t('common_member')->fetch_by_username($username);
if (!$member || empty($member['uid'])) {
    fwrite(STDERR, "[R02-AUTH-FIXTURE] ABORT: main fixture is absent\n");
    exit(41);
}
$uid = (int) $member['uid'];
C::t('common_member')->delete_no_validate($uid);
uc_user_delete($uid);
if (C::t('common_member')->fetch($uid) || uc_get_user($username)) {
    fwrite(STDERR, "[R02-AUTH-FIXTURE] ABORT: fixture destroy verification failed\n");
    exit(42);
}
$historyRoot = dirname($metadataPath) . '/history';
if (!is_dir($historyRoot) && !mkdir($historyRoot, 0700, true)) {
    throw new RuntimeException('metadata history create failed');
}
$historyPath = $historyRoot . '/' . gmdate('Ymd\THis\Z') . '.json';
$metadata = json_decode(file_get_contents($metadataPath), true);
$metadata['status'] = 'DESTROYED';
$metadata['destroyed_at'] = gmdate('c');
file_put_contents(
    $historyPath,
    json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
);
chmod($historyPath, 0400);
unlink($metadataPath);
echo "[R02-AUTH-FIXTURE] DESTROYED uid={$uid}\n";
PHP
    ;;

  *)
    fail "usage: r02_auth_fixture.sh create|bridge-on|bridge-off|status|destroy"
    ;;
esac
