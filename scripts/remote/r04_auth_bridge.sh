#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
STAGING_SITE="/www/staging/tg-h5-ui-r04/site"
STAGING_PRIVATE="/www/staging/tg-h5-ui-r04/private"
BRIDGE_DIR="${STAGING_SITE}/__r04_auth__"
BRIDGE_HISTORY="${STAGING_PRIVATE}/auth-bridge-history"

fail() {
  printf '[R04-AUTH-BRIDGE] ABORT: %s\n' "$1" >&2
  exit 1
}

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${STAGING_SITE}/source/plugin" ] || fail "R04 staging site is absent"
grep -Fq "tgb_stage_r04_main" "${STAGING_SITE}/config/config_global.php" ||
  fail "main database is not the R04 clone"
grep -Fq "tgb_stage_r04_uc" "${STAGING_SITE}/config/config_ucenter.php" ||
  fail "UCenter database is not the R04 clone"

verify_fixture() {
  cd "${STAGING_SITE}"
  php <<'PHP'
<?php
define('CURSCRIPT', 'r04fixturestatus');
$_SERVER['PHP_SELF'] = '/r04fixturestatus.php';
$_SERVER['SCRIPT_NAME'] = '/r04fixturestatus.php';
$_SERVER['SCRIPT_FILENAME'] = '/www/staging/tg-h5-ui-r04/site/r04fixturestatus.php';
$_SERVER['REQUEST_URI'] = '/r04fixturestatus.php';
$_SERVER['HTTP_HOST'] = 'tg-h5-ui-r04.local';
$_SERVER['SERVER_NAME'] = 'tg-h5-ui-r04.local';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
chdir('/www/staging/tg-h5-ui-r04/site');
require '/www/staging/tg-h5-ui-r04/site/source/class/class_core.php';
$discuz = C::app();
$discuz->init();
loaducenter();
$member = C::t('common_member')->fetch_by_username('tgb_r02_visual');
$ucenter = uc_get_user('tgb_r02_visual');
if (!$member || !$ucenter || (int) $member['uid'] !== (int) $ucenter[0]) {
    fwrite(STDERR, "[R04-AUTH-BRIDGE] ABORT: fixture parity failed\n");
    exit(31);
}
echo "[R04-AUTH-BRIDGE] FIXTURE_PARITY=PASS\n";
PHP
}

case "${MODE}" in
  bridge-on)
    verify_fixture
    [ ! -e "${BRIDGE_DIR}" ] || fail "login bridge already exists"
    install -d -o www -g www -m 0755 "${BRIDGE_DIR}"
    BRIDGE_TMP="$(mktemp "${STAGING_PRIVATE}/r04-auth-bridge.XXXXXX.php")"
    trap 'rm -f "${BRIDGE_TMP:-}"' EXIT
    cat >"${BRIDGE_TMP}" <<'PHP'
<?php
define('CURSCRIPT', 'r04authbridge');
$remote = $_SERVER['REMOTE_ADDR'] ?? '';
if ($remote !== '127.0.0.1' && $remote !== '::1') {
    http_response_code(403);
    exit('loopback only');
}
chdir('/www/staging/tg-h5-ui-r04/site');
require '/www/staging/tg-h5-ui-r04/site/source/class/class_core.php';
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
      -H 'Host: tg-h5-ui-r04.local' \
      -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
      'http://127.0.0.1:18084/__r04_auth__/')"
    [ "${CODE}" = "302" ] || fail "login bridge probe HTTP ${CODE}"
    printf '[R04-AUTH-BRIDGE] ACTIVE HTTP=%s\n' "${CODE}"
    ;;
  bridge-off)
    [ -f "${BRIDGE_DIR}/index.php" ] || fail "login bridge is absent"
    RESOLVED_BRIDGE="$(readlink -f "${BRIDGE_DIR}")"
    [ "${RESOLVED_BRIDGE}" = "${BRIDGE_DIR}" ] || fail "unexpected bridge path"
    install -d -m 0700 "${BRIDGE_HISTORY}"
    HISTORY_ID="$(date '+%Y%m%dT%H%M%S%z')"
    HISTORY_DIR="${BRIDGE_HISTORY}/${HISTORY_ID}"
    [ ! -e "${HISTORY_DIR}" ] || fail "bridge history path exists"
    mv -- "${RESOLVED_BRIDGE}" "${HISTORY_DIR}"
    chmod -R a-w "${HISTORY_DIR}"
    printf '[R04-AUTH-BRIDGE] REMOVED history=%s\n' "${HISTORY_DIR}"
    ;;
  status)
    verify_fixture
    if [ -e "${BRIDGE_DIR}" ]; then
      printf '[R04-AUTH-BRIDGE] BRIDGE=ACTIVE\n'
    else
      printf '[R04-AUTH-BRIDGE] BRIDGE=OFF\n'
    fi
    ;;
  *)
    fail "usage: r04_auth_bridge.sh bridge-on|bridge-off|status"
    ;;
esac
