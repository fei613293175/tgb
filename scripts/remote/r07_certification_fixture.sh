#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
SITE="/www/staging/tg-h5-ui-r07/site"
MARKER="R07_VISUAL_ONLY"

fail() { printf '[R07-CERT-FIXTURE] ABORT: %s\n' "$1" >&2; exit 1; }
[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${SITE}/source/plugin" ] || fail "R07 staging is absent"
grep -Fq 'tgb_stage_r07_main' "${SITE}/config/config_global.php" || fail "not the R07 database"

cd "${SITE}"
MODE="${MODE}" MARKER="${MARKER}" runuser -u www --preserve-environment -- php <<'PHP'
<?php
define('CURSCRIPT', 'r07certfixture');
$_SERVER['PHP_SELF'] = '/r07certfixture.php';
$_SERVER['SCRIPT_NAME'] = '/r07certfixture.php';
$_SERVER['SCRIPT_FILENAME'] = '/www/staging/tg-h5-ui-r07/site/r07certfixture.php';
$_SERVER['REQUEST_URI'] = '/r07certfixture.php';
$_SERVER['HTTP_HOST'] = 'tg-h5-ui-r07.local';
$_SERVER['SERVER_NAME'] = 'tg-h5-ui-r07.local';
$_SERVER['SERVER_PORT'] = '18087';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
chdir('/www/staging/tg-h5-ui-r07/site');
require './source/class/class_core.php';
$discuz = C::app();
$discuz->init();

$mode = getenv('MODE');
$marker = getenv('MARKER');
$member = C::t('common_member')->fetch_by_username('tgb_r02_visual');
if (!$member || empty($member['uid'])) {
    fwrite(STDERR, "[R07-CERT-FIXTURE] ABORT: visual member absent\n");
    exit(31);
}
$uid = (int) $member['uid'];
$existing = DB::fetch_first('SELECT id FROM %t WHERE uid=%d AND orderNo=%s', array('xiaomy_certification', $uid, $marker));

if ($mode === 'on') {
    $real = DB::fetch_first('SELECT id FROM %t WHERE uid=%d AND rescodebdres=1', array('xiaomy_certification', $uid));
    if ($real && !$existing) {
        fwrite(STDERR, "[R07-CERT-FIXTURE] ABORT: fixture already has a non-test certification\n");
        exit(32);
    }
    if (!$existing) {
        DB::insert('xiaomy_certification', array(
            'uid' => $uid,
            'username' => $member['username'],
            'name' => 'R07 Visual User',
            'sfzno' => '',
            'orderNo' => $marker,
            'certifyId' => $marker,
            'rescode' => '',
            'rescodebd' => '',
            'rescodebdres' => 1,
            'facepic' => '',
            'shdateline' => TIMESTAMP,
            'dateline' => TIMESTAMP,
        ));
    }
    echo "[R07-CERT-FIXTURE] ACTIVE uid={$uid}\n";
} elseif ($mode === 'off') {
    if ($existing) {
        DB::delete('xiaomy_certification', array('id' => (int) $existing['id'], 'uid' => $uid, 'orderNo' => $marker));
    }
    $remaining = DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d AND orderNo=%s', array('xiaomy_certification', $uid, $marker));
    if ($remaining) {
        fwrite(STDERR, "[R07-CERT-FIXTURE] ABORT: marked row remains\n");
        exit(33);
    }
    echo "[R07-CERT-FIXTURE] OFF uid={$uid}\n";
} elseif ($mode === 'status') {
    echo $existing ? "[R07-CERT-FIXTURE] ACTIVE uid={$uid}\n" : "[R07-CERT-FIXTURE] OFF uid={$uid}\n";
} else {
    fwrite(STDERR, "[R07-CERT-FIXTURE] ABORT: usage on|off|status\n");
    exit(34);
}
PHP
