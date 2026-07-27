#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
STAGING_SITE="/www/staging/tg-h5-ui-r08/site"
MARKER="r09_headline_visual"

fail() { printf '[R09-HEADLINE-FIXTURE] ABORT: %s\n' "$1" >&2; exit 1; }
[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${STAGING_SITE}/source/plugin" ] || fail "R08 staging site is absent"
grep -Fq "tgb_stage_r08_main" "${STAGING_SITE}/config/config_global.php" || fail "main database is not the R08 clone"

run_php() {
    cd "${STAGING_SITE}"
    local bootstrap='define("CURSCRIPT", "r09headlinefixture"); $_SERVER["PHP_SELF"]="/r09headlinefixture.php"; $_SERVER["SCRIPT_NAME"]="/r09headlinefixture.php"; $_SERVER["SCRIPT_FILENAME"]="/www/staging/tg-h5-ui-r08/site/r09headlinefixture.php"; $_SERVER["REQUEST_URI"]="/r09headlinefixture.php"; $_SERVER["HTTP_HOST"]="tg-h5-ui-r08.local"; $_SERVER["SERVER_NAME"]="tg-h5-ui-r08.local"; $_SERVER["SERVER_PORT"]="18088"; $_SERVER["REMOTE_ADDR"]="127.0.0.1"; $_SERVER["REQUEST_METHOD"]="GET"; require "./source/class/class_core.php"; $discuz=C::app(); $discuz->init(); '
    php -d display_errors=stderr -r "${bootstrap}${1}"
}

count_fixture() {
    run_php 'echo DB::result_first("SELECT COUNT(*) FROM ".DB::table("tb_toutiao")." WHERE username IN (\"r09_headline_visual\",\"tgb_r09_headline_vis\")");'
}

case "${MODE}" in
    fixture-on)
        [ "$(count_fixture)" = "0" ] || fail "fixture already exists"
        run_php '$now=time(); $pub=DB::fetch_first("SELECT id,uid FROM ".DB::table("xigua_hb_pub")." WHERE display=1 AND endts>".$now." ORDER BY id DESC LIMIT 1"); if (!$pub) { fwrite(STDERR, "eligible publication is absent\n"); exit(31); } DB::insert("tb_toutiao", array("uid"=>(int)$pub["uid"], "username"=>"r09_headline_visual", "pubid"=>(int)$pub["id"], "endtime"=>$now+7200, "dateline"=>$now)); echo "[R09-HEADLINE-FIXTURE] ACTIVE pubid=", (int)$pub["id"], "\n";'
        [ "$(count_fixture)" = "1" ] || fail "fixture insert verification failed"
        ;;
    fixture-off)
        [ "$(count_fixture)" = "1" ] || fail "fixture is absent or duplicated"
        run_php 'DB::query("DELETE FROM ".DB::table("tb_toutiao")." WHERE username IN (\"r09_headline_visual\",\"tgb_r09_headline_vis\")"); echo "[R09-HEADLINE-FIXTURE] REMOVED\n";'
        [ "$(count_fixture)" = "0" ] || fail "fixture cleanup verification failed"
        ;;
    status)
        printf '[R09-HEADLINE-FIXTURE] COUNT=%s\n' "$(count_fixture)"
        ;;
    *) fail "usage: r09_headline_visual_fixture.sh fixture-on|fixture-off|status" ;;
esac
