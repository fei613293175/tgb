#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
SITE="/www/staging/tg-h5-ui-r08/site"
PRIVATE="/www/staging/tg-h5-ui-r08/private"
STATE_DIR="${PRIVATE}/r09-member-chat-fixture-active"
HISTORY_ROOT="${PRIVATE}/r09-member-chat-fixture-history"
META="${STATE_DIR}/fixture.json"
PRIMARY_USERNAME="tgb_r02_visual"
PEER_USERNAME="tgb_r09_peer"
MARKER="R09_MEMBER_CHAT_VISUAL"

fail() { printf '[R09-MEMBER-CHAT-FIXTURE] ABORT: %s\n' "$1" >&2; exit 1; }
[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${SITE}/source/plugin" ] || fail "R08 staging site is absent"
[ "$(stat -c '%a' "${PRIVATE}")" = "700" ] || fail "R08 private directory mode is not 700"
grep -Fq 'tgb_stage_r08_main' "${SITE}/config/config_global.php" || fail "not the R08 main database"
grep -Fq 'tgb_stage_r08_uc' "${SITE}/config/config_ucenter.php" || fail "not the R08 UCenter database"

export R09_MODE="${MODE}" R09_META="${META}" R09_PRIMARY="${PRIMARY_USERNAME}" R09_PEER="${PEER_USERNAME}" R09_MARKER="${MARKER}"

case "${MODE}" in
  on) [ ! -e "${STATE_DIR}" ] || fail "fixture is already active"; install -d -m 0700 "${STATE_DIR}" ;;
  off|status) ;;
  *) fail "usage: r09_member_chat_fixture.sh on|status|off" ;;
esac

cd "${SITE}"
if ! php <<'PHP'
<?php
define('CURSCRIPT', 'r09memberchatfixture');
$_SERVER['PHP_SELF'] = '/r09memberchatfixture.php';
$_SERVER['SCRIPT_NAME'] = '/r09memberchatfixture.php';
$_SERVER['SCRIPT_FILENAME'] = '/www/staging/tg-h5-ui-r08/site/r09memberchatfixture.php';
$_SERVER['REQUEST_URI'] = '/r09memberchatfixture.php';
$_SERVER['HTTP_HOST'] = 'tg-h5-ui-r08.local';
$_SERVER['SERVER_NAME'] = 'tg-h5-ui-r08.local';
$_SERVER['SERVER_PORT'] = '18088';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
chdir('/www/staging/tg-h5-ui-r08/site');
require './source/class/class_core.php';
$discuz = C::app();
$discuz->init();
loaducenter();

$mode = getenv('R09_MODE');
$metaPath = getenv('R09_META');
$primaryName = getenv('R09_PRIMARY');
$peerName = getenv('R09_PEER');
$marker = getenv('R09_MARKER');
$primary = C::t('common_member')->fetch_by_username($primaryName);
if (!$primary || empty($primary['uid'])) {
    fwrite(STDERR, "[R09-MEMBER-CHAT-FIXTURE] ABORT: primary fixture absent\n");
    exit(31);
}
$primaryUid = (int) $primary['uid'];

if ($mode === 'on') {
    if (C::t('common_member')->fetch_by_username($peerName) || uc_get_user($peerName)) {
        fwrite(STDERR, "[R09-MEMBER-CHAT-FIXTURE] ABORT: peer already exists\n");
        exit(32);
    }
    $existingChatRows = (int) DB::result_first(
        'SELECT COUNT(*) FROM %t WHERE authorid=%d OR touid=%d',
        array('xigua_hb_comment', $primaryUid, $primaryUid)
    );
    if ($existingChatRows !== 0) {
        fwrite(STDERR, "[R09-MEMBER-CHAT-FIXTURE] ABORT: primary fixture has existing chat rows\n");
        exit(33);
    }
    $password = bin2hex(random_bytes(24));
    $email = $peerName . '@example.com';
    $peerUid = uc_user_register(addslashes($peerName), $password, $email, 0, '', '127.0.0.1');
    if ($peerUid <= 0) {
        fwrite(STDERR, "[R09-MEMBER-CHAT-FIXTURE] ABORT: UCenter peer create failed\n");
        exit(34);
    }
    try {
        $groupId = !empty($_G['setting']['regverify']) ? 8 : (int) $_G['setting']['newusergroupid'];
        C::t('common_member')->insert(
            $peerUid,
            $peerName,
            md5(random(10)),
            $email,
            '127.0.0.1',
            $groupId,
            array('credits' => explode(',', (string) $_G['setting']['initcredits']), 'profile' => array(), 'emailstatus' => 0)
        );
        $peer = C::t('common_member')->fetch($peerUid);
        if (!$peer || $peer['username'] !== $peerName) {
            throw new RuntimeException('main peer verification failed');
        }
        $cid = DB::insert('xigua_hb_comment', array(
            'authorid' => (int) $peerUid,
            'author' => $peerName,
            'touid' => $primaryUid,
            'touser' => $primary['username'],
            'comment' => $marker,
            'pubid' => 0,
            'crts' => TIMESTAMP,
            'pubuid' => 0,
            'shid' => 0,
            'star' => 5,
            'imglist' => serialize(array()),
            'type' => 1,
            'new' => 1,
            'isnew' => 0,
            'status' => 0,
            'hidefor' => '',
            'og_pubid' => 0,
            'ip' => '127.0.0.1',
            'ipaddr' => '',
        ), true);
        if (!$cid) {
            throw new RuntimeException('synthetic chat insert failed');
        }
        $metadata = array(
            'schema_version' => 1,
            'fixture' => $marker,
            'primary_uid' => $primaryUid,
            'peer_uid' => (int) $peerUid,
            'chat_cid' => (int) $cid,
            'created_at' => gmdate('c'),
            'status' => 'ACTIVE',
        );
        if (file_put_contents($metaPath, json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n") === false) {
            throw new RuntimeException('metadata write failed');
        }
        chmod($metaPath, 0600);
        echo "[R09-MEMBER-CHAT-FIXTURE] ACTIVE peer_uid={$peerUid} chat_rows=1\n";
    } catch (Throwable $error) {
        if (!empty($cid)) DB::delete('xigua_hb_comment', array('cid' => (int) $cid));
        if (C::t('common_member')->fetch($peerUid)) C::t('common_member')->delete_no_validate($peerUid);
        uc_user_delete($peerUid);
        fwrite(STDERR, "[R09-MEMBER-CHAT-FIXTURE] ABORT: create rolled back\n");
        exit(35);
    }
} elseif ($mode === 'status') {
    if (!is_file($metaPath)) {
        echo "[R09-MEMBER-CHAT-FIXTURE] OFF\n";
        exit(0);
    }
    $metadata = json_decode(file_get_contents($metaPath), true);
    $peer = C::t('common_member')->fetch((int) $metadata['peer_uid']);
    $ucPeer = uc_get_user($peerName);
    $chat = DB::fetch_first('SELECT cid FROM %t WHERE cid=%d AND comment=%s', array('xigua_hb_comment', (int) $metadata['chat_cid'], $marker));
    if (!$peer || !$ucPeer || (int) $ucPeer[0] !== (int) $metadata['peer_uid'] || !$chat) {
        fwrite(STDERR, "[R09-MEMBER-CHAT-FIXTURE] ABORT: active fixture drift\n");
        exit(36);
    }
    echo "[R09-MEMBER-CHAT-FIXTURE] ACTIVE peer_uid={$metadata['peer_uid']} chat_rows=1\n";
} elseif ($mode === 'off') {
    if (!is_file($metaPath)) {
        fwrite(STDERR, "[R09-MEMBER-CHAT-FIXTURE] ABORT: active metadata absent\n");
        exit(37);
    }
    $metadata = json_decode(file_get_contents($metaPath), true);
    $peerUid = (int) $metadata['peer_uid'];
    $cid = (int) $metadata['chat_cid'];
    DB::delete('xigua_hb_comment', array('cid' => $cid));
    DB::delete('xigua_hb_viewlog', array('uid' => $peerUid, 'visiter' => $primaryUid));
    if (C::t('common_member')->fetch($peerUid)) C::t('common_member')->delete_no_validate($peerUid);
    if (uc_get_user($peerName)) uc_user_delete($peerUid);
    $leftChat = (int) DB::result_first('SELECT COUNT(*) FROM %t WHERE cid=%d OR authorid=%d OR touid=%d', array('xigua_hb_comment', $cid, $peerUid, $peerUid));
    $leftView = (int) DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d AND visiter=%d', array('xigua_hb_viewlog', $peerUid, $primaryUid));
    if ($leftChat || $leftView || C::t('common_member')->fetch($peerUid) || uc_get_user($peerName)) {
        fwrite(STDERR, "[R09-MEMBER-CHAT-FIXTURE] ABORT: cleanup verification failed\n");
        exit(38);
    }
    echo "[R09-MEMBER-CHAT-FIXTURE] OFF cleanup=PASS\n";
}
PHP
then
  if [ "${MODE}" = "on" ]; then rm -rf -- "${STATE_DIR}"; fi
  exit 1
fi

if [ "${MODE}" = "off" ]; then
  install -d -m 0700 "${HISTORY_ROOT}"
  history_dir="${HISTORY_ROOT}/$(date '+%Y%m%dT%H%M%S%z')"
  [ ! -e "${history_dir}" ] || fail "history path exists"
  mv -- "${STATE_DIR}" "${history_dir}"
  chmod -R a-w "${history_dir}"
  printf '[R09-MEMBER-CHAT-FIXTURE] HISTORY=%s\n' "${history_dir}"
fi
