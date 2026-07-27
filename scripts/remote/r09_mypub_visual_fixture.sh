#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
SITE="/www/staging/tg-h5-ui-r08/site"
MARKER_PREFIX="R09_MY_PUBLICATION_VISUAL_FIXTURE_"

fail() { printf '[R09-MYPUB-FIXTURE] ABORT: %s\n' "$1" >&2; exit 1; }
[ "$(id -u)" -eq 0 ] || fail "root is required"
grep -Fq "tgb_stage_r08_main" "${SITE}/config/config_global.php" || fail "main database is not the R08 clone"

run_php() {
  cd "${SITE}"
  php -d display_errors=stderr -r 'define("CURSCRIPT", "r09mypubfixture"); $_SERVER["PHP_SELF"]="/r09mypubfixture.php"; $_SERVER["SCRIPT_NAME"]="/r09mypubfixture.php"; $_SERVER["SCRIPT_FILENAME"]="/www/staging/tg-h5-ui-r08/site/r09mypubfixture.php"; $_SERVER["REQUEST_URI"]="/r09mypubfixture.php"; $_SERVER["HTTP_HOST"]="tg-h5-ui-r08.local"; $_SERVER["SERVER_NAME"]="tg-h5-ui-r08.local"; $_SERVER["SERVER_PORT"]="18088"; $_SERVER["REMOTE_ADDR"]="127.0.0.1"; $_SERVER["REQUEST_METHOD"]="GET"; require "./source/class/class_core.php"; $discuz=C::app(); $discuz->init(); '"$1"
}

count_fixture() {
  run_php 'echo DB::result_first("SELECT COUNT(*) FROM ".DB::table("xigua_hb_pub")." WHERE title LIKE \"R09\\_MY\\_PUBLICATION\\_VISUAL\\_FIXTURE\\_%\"");'
}

case "${MODE}" in
  fixture-on)
    [ "$(count_fixture)" = "0" ] || fail "fixture already exists"
    run_php '$member=C::t("common_member")->fetch_by_username("tgb_r02_visual"); if (!$member) { fwrite(STDERR,"visual member absent\n"); exit(31); } $base=DB::fetch_first("SELECT * FROM ".DB::table("xigua_hb_pub")." WHERE display=1 AND recycle=0 AND title NOT LIKE \"R09\\_MY\\_PUBLICATION\\_VISUAL\\_FIXTURE\\_%\" ORDER BY id DESC LIMIT 1"); if (!$base) { fwrite(STDERR,"source publication absent\n"); exit(32); } unset($base["id"]); for($i=1;$i<=8;$i++){ $row=$base; $row["uid"]=(int)$member["uid"]; if (array_key_exists("username",$row)) $row["username"]="tgb_r02_visual"; if (array_key_exists("realname",$row)) $row["realname"]="推广宝测试账号"; $row["title"]="R09_MY_PUBLICATION_VISUAL_FIXTURE_".$i; if (array_key_exists("description",$row)) $row["description"]="推广宝头条项目选择滚动验证条目 ".$i."，仅存在于隔离预发布环境。"; if (array_key_exists("dateline",$row)) $row["dateline"]=time()-86400-$i; if (array_key_exists("lastupdate",$row)) $row["lastupdate"]=time()-3600-$i; if (array_key_exists("endts",$row)) $row["endts"]=time()+864000; $row["display"]=1; $row["recycle"]=0; $id=DB::insert("xigua_hb_pub",$row,true); echo "[R09-MYPUB-FIXTURE] ACTIVE pubid=",(int)$id,"\n"; }'
    [ "$(count_fixture)" = "8" ] || fail "fixture insert verification failed"
    ;;
  fixture-off)
    [ "$(count_fixture)" = "8" ] || fail "fixture is absent or incomplete"
    run_php 'DB::query("DELETE FROM ".DB::table("xigua_hb_pub")." WHERE title LIKE \"R09\\_MY\\_PUBLICATION\\_VISUAL\\_FIXTURE\\_%\""); echo "[R09-MYPUB-FIXTURE] REMOVED\n";'
    [ "$(count_fixture)" = "0" ] || fail "fixture cleanup verification failed"
    ;;
  status) printf '[R09-MYPUB-FIXTURE] COUNT=%s\n' "$(count_fixture)" ;;
  *) fail "usage: r09_mypub_visual_fixture.sh fixture-on|fixture-off|status" ;;
esac
