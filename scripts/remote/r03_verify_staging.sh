#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

PRODUCTION_ROOT="/www/wwwroot/tg.suewammes.com"
SOURCE_STAGING_SITE="/www/staging/tg-h5-ui-r02/site"
STAGING_BASE="/www/staging/tg-h5-ui-r03"
STAGING_SITE="${STAGING_BASE}/site"
STAGING_PRIVATE="${STAGING_BASE}/private"
BACKUP_ROOT="/www/backup/tg-h5-ui-r03"
NGINX_CONFIG="/www/server/panel/vhost/nginx/tg-h5-ui-r03-loopback.conf"
LOOPBACK_PORT="18083"
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"
STAGE_MAIN_DB="tgb_stage_r03_main"
STAGE_UC_DB="tgb_stage_r03_uc"

fail() {
    printf '[R03-VERIFY] FAIL: %s\n' "$1" >&2
    exit 61
}

test "$(id -u)" -eq 0 || fail "root is required"
test -d "${STAGING_SITE}/source/plugin" || fail "staging plugin root missing"
test -d "${SOURCE_STAGING_SITE}/source/plugin" || fail "R02 source staging missing"
test -d "${STAGING_SITE}/data/template" || fail "Discuz template cache directory missing"
test -w "${STAGING_SITE}/data/template" || fail "Discuz template cache directory is not writable"
test -f "${NGINX_CONFIG}" || fail "staging Nginx config missing"
grep -Fq 'Light Grid R02' \
    "${STAGING_SITE}/source/plugin/xigua_hb/static/tgb-r02/light-grid-r02.css" ||
    fail "R02 Light Grid inheritance missing"
test "$(stat -c '%a' /www/staging)" = "711" || fail "/www/staging mode"
test "$(stat -c '%a' "${STAGING_BASE}")" = "711" || fail "staging base mode"
test "$(stat -c '%a' "${STAGING_PRIVATE}")" = "700" || fail "private mode"

if ! ss -ltnp | grep -E "127\\.0\\.0\\.1:${LOOPBACK_PORT}.*nginx" >/dev/null; then
    fail "host Nginx listener missing"
fi

mapfile -t SNAPSHOT_IDS < <(
    find "${BACKUP_ROOT}" -mindepth 1 -maxdepth 1 -type d \
        ! -name '*.failed-*' -printf '%f\n' | sort
)
[ "${#SNAPSHOT_IDS[@]}" -eq 1 ] || fail "resumable snapshot count"
SNAPSHOT_ID="${SNAPSHOT_IDS[0]}"
BACKUP_DIR="${BACKUP_ROOT}/${SNAPSHOT_ID}"

(cd "${BACKUP_DIR}" && sha256sum -c ARTIFACTS_SHA256.txt >/dev/null) ||
    fail "snapshot artifact hash"
gzip -t "${BACKUP_DIR}/main-database.sql.gz" ||
    fail "main database gzip"
gzip -t "${BACKUP_DIR}/ucenter-database.sql.gz" ||
    fail "UCenter database gzip"
tar -tzf "${BACKUP_DIR}/site-code.tar.gz" >/dev/null ||
    fail "code archive"
tar -tzf "${BACKUP_DIR}/site-uploads.tar.gz" >/dev/null ||
    fail "upload archive"

ROOT_CNF="$(mktemp /tmp/tgb-r03-verify.XXXXXX.cnf)"
cleanup() {
    rm -f "${ROOT_CNF}"
}
trap cleanup EXIT

cd /www/server/panel
"${PANEL_PYTHON}" - "${ROOT_CNF}" <<'PY'
import os
import sys

sys.path.insert(0, "/www/server/panel/class")
os.chdir("/www/server/panel")
import public

target = sys.argv[1]
password_value = public.M("config").where("id=?", (1,)).getField("mysql_root")
if not password_value:
    raise SystemExit("mysql root credential is unavailable")
password = str(password_value).replace("\\", "\\\\").replace('"', '\\"')
with open(target, "w", encoding="utf-8", newline="\n") as fh:
    fh.write('[client]\n')
    fh.write('host="127.0.0.1"\n')
    fh.write('port=3306\n')
    fh.write('user="root"\n')
    fh.write(f'password="{password}"\n')
PY
chmod 600 "${ROOT_CNF}"
mysql --defaults-extra-file="${ROOT_CNF}" -NBe "SELECT 1" >/dev/null ||
    fail "MySQL root preflight"

MAIN_TABLES="$(mysql --defaults-extra-file="${ROOT_CNF}" -NBe \
    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${STAGE_MAIN_DB}'")"
UC_TABLES="$(mysql --defaults-extra-file="${ROOT_CNF}" -NBe \
    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${STAGE_UC_DB}'")"
[ "${MAIN_TABLES}" -gt 0 ] || fail "main staging database empty"
[ "${UC_TABLES}" -gt 0 ] || fail "UCenter staging database empty"

CONFIG_ISOLATION="$(php <<'PHP'
<?php
include '/www/wwwroot/tg.suewammes.com/config/config_global.php';
$prodMain = $_config['db'][1]['dbname'];
unset($_config);
include '/www/staging/tg-h5-ui-r03/site/config/config_global.php';
$stageMain = $_config['db'][1]['dbname'];
function defineValue($path, $name) {
    $text = file_get_contents($path);
    $pattern = "/define\\('" . preg_quote($name, "/") . "',\\s*'([^']*)'\\);/";
    if (!preg_match($pattern, $text, $match)) {
        return null;
    }
    return $match[1];
}
$prodUc = defineValue(
    '/www/wwwroot/tg.suewammes.com/uc_server/data/config.inc.php',
    'UC_DBNAME'
);
$stageUc = defineValue(
    '/www/staging/tg-h5-ui-r03/site/uc_server/data/config.inc.php',
    'UC_DBNAME'
);
echo ($prodMain !== $stageMain && $prodUc !== null &&
    $stageUc !== null && $prodUc !== $stageUc) ? 'PASS' : 'FAIL';
?>
PHP
)"
[ "${CONFIG_ISOLATION}" = "PASS" ] || fail "staging database isolation"

for forbidden in \
    bbb.suewammes.com_DAz6e.zip \
    tools.php \
    phpinfo.php \
    log.txt; do
    test ! -e "${STAGING_SITE}/${forbidden}" ||
        fail "forbidden staging artifact ${forbidden}"
done

FIRST_HTTP_CODE="$(curl -sS -D "${STAGING_PRIVATE}/home.headers" \
    -o /dev/null \
    -w '%{http_code}' \
    -H 'Host: tg-h5-ui-r03.local' \
    -A 'Mozilla/5.0 (Linux; Android 14; Pixel 8 Pro) AppleWebKit/537.36 Chrome/138.0 Mobile Safari/537.36' \
    "http://127.0.0.1:${LOOPBACK_PORT}/plugin.php?id=xigua_hb")"
case "${FIRST_HTTP_CODE}" in
    200|302) ;;
    *) fail "first-hop HTTP ${FIRST_HTTP_CODE}" ;;
esac

FOLLOW_RESULT="$(curl -sS -L --max-redirs 5 \
    --resolve "tg-h5-ui-r03.local:${LOOPBACK_PORT}:127.0.0.1" \
    -D "${STAGING_PRIVATE}/home-follow.headers" \
    -o "${STAGING_PRIVATE}/home-follow.html" \
    -w '%{http_code}|%{url_effective}|%{num_redirects}' \
    -A 'Mozilla/5.0 (Linux; Android 14; Pixel 8 Pro) AppleWebKit/537.36 Chrome/138.0 Mobile Safari/537.36' \
    "http://tg-h5-ui-r03.local:${LOOPBACK_PORT}/plugin.php?id=xigua_hb")"
FINAL_HTTP_CODE="${FOLLOW_RESULT%%|*}"
FOLLOW_REST="${FOLLOW_RESULT#*|}"
FINAL_URL="${FOLLOW_REST%%|*}"
REDIRECT_COUNT="${FOLLOW_RESULT##*|}"
[ "${FINAL_HTTP_CODE}" = "200" ] || fail "final HTTP ${FINAL_HTTP_CODE}"
case "${FINAL_URL}" in
    "http://tg-h5-ui-r03.local:${LOOPBACK_PORT}/"*) ;;
    *) fail "redirect escaped staging host" ;;
esac
grep -qiE '<html|<!doctype' "${STAGING_PRIVATE}/home-follow.html" ||
    fail "final response is not HTML"
grep -qi '^X-TGB-Staging: R03' "${STAGING_PRIVATE}/home.headers" ||
    fail "staging marker header"

POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' \
    -X POST \
    -H 'Host: tg-h5-ui-r03.local' \
    "http://127.0.0.1:${LOOPBACK_PORT}/plugin.php?id=xigua_hb")"
[ "${POST_CODE}" = "405" ] || fail "write guard HTTP ${POST_CODE}"

find "${PRODUCTION_ROOT}" -xdev -type f \
    ! -path "${PRODUCTION_ROOT}/data/attachment/*" \
    ! -path "${PRODUCTION_ROOT}/data/cache/*" \
    ! -path "${PRODUCTION_ROOT}/data/log/*" \
    ! -path "${PRODUCTION_ROOT}/data/sysdata/*" \
    ! -path "${PRODUCTION_ROOT}/data/template/*" \
    ! -path "${PRODUCTION_ROOT}/source/plugin/xigua_hb/pics/*" \
    ! -path "${PRODUCTION_ROOT}/uc_server/data/avatar/*" \
    ! -path "${PRODUCTION_ROOT}/uc_server/data/cache/*" \
    ! -path "${PRODUCTION_ROOT}/uc_server/data/logs/*" \
    ! -name 'operation.log' \
    -print0 |
    sort -z |
    xargs -0 sha256sum >"${BACKUP_DIR}/production-code-after.sha256"
STABLE_BEFORE="${STAGING_PRIVATE}/production-code-before-stable.sha256"
grep -vE '/data/sysdata/|/source/plugin/xigua_hb/pics/' \
    "${BACKUP_DIR}/production-code-before.sha256" >"${STABLE_BEFORE}"
cmp "${STABLE_BEFORE}" \
    "${BACKUP_DIR}/production-code-after.sha256" ||
    fail "production stable code changed"

PRODUCTION_MANIFEST_SHA="$(sha256sum "${STABLE_BEFORE}" | cut -d ' ' -f 1)"
ARTIFACT_MANIFEST_SHA="$(sha256sum "${BACKUP_DIR}/ARTIFACTS_SHA256.txt" | cut -d ' ' -f 1)"

cat >"${STAGING_PRIVATE}/R03_FACTS.txt" <<FACTS
snapshot_id=${SNAPSHOT_ID}
source_release=R02
source_staging_root=${SOURCE_STAGING_SITE}
production_root=${PRODUCTION_ROOT}
staging_root=${STAGING_SITE}
staging_listener=127.0.0.1:${LOOPBACK_PORT}
staging_access=SSH tunnel only
staging_write_guard=GET_HEAD_ONLY
main_table_count=${MAIN_TABLES}
ucenter_table_count=${UC_TABLES}
http_first=${FIRST_HTTP_CODE}
http_final=${FINAL_HTTP_CODE}
redirect_count=${REDIRECT_COUNT}
http_post=${POST_CODE}
production_code_unchanged=PASS
production_manifest_sha256=${PRODUCTION_MANIFEST_SHA}
artifact_manifest_sha256=${ARTIFACT_MANIFEST_SHA}
dangerous_public_files_copied=NO
r02_light_grid_inherited=PASS
discuz_template_cache_directory=READY
FACTS
chmod 600 "${STAGING_PRIVATE}/R03_FACTS.txt"

cat >"${BACKUP_DIR}/RESTORE_README.md" <<RESTORE
# R03 snapshot restore

Snapshot: ${SNAPSHOT_ID}

Artifacts are verified by ARTIFACTS_SHA256.txt.

Dry-run proof completed:
- the closed R02 staging code and databases were the only R03 source baseline;
- site-code.tar.gz extracted into the isolated staging root;
- site-uploads.tar.gz extracted into the isolated staging root;
- main-database.sql.gz imported into an isolated database;
- ucenter-database.sql.gz imported into an isolated database;
- loopback staging ended on real HTML with HTTP 200;
- non-GET requests returned HTTP 405;
- production stable-code manifest matched before and after.

Never restore directly over production while Nginx/PHP workers are serving it.
Use a new recovery directory and new recovery databases, verify there, then
switch paths during an approved maintenance window. Database credentials must
be supplied from the protected server source and must not be written here.
RESTORE
chmod 600 "${BACKUP_DIR}/RESTORE_README.md"

printf '[R03-VERIFY] PASS snapshot_id=%s\n' "${SNAPSHOT_ID}"
printf '[R03-VERIFY] STAGING_LISTENER=127.0.0.1:%s\n' "${LOOPBACK_PORT}"
printf '[R03-VERIFY] HTTP_FIRST=%s HTTP_FINAL=%s REDIRECTS=%s HTTP_POST=%s\n' \
    "${FIRST_HTTP_CODE}" "${FINAL_HTTP_CODE}" "${REDIRECT_COUNT}" "${POST_CODE}"
printf '[R03-VERIFY] MAIN_TABLES=%s UC_TABLES=%s\n' \
    "${MAIN_TABLES}" "${UC_TABLES}"
printf '[R03-VERIFY] PRODUCTION_CODE_UNCHANGED=PASS\n'
printf '[R03-VERIFY] PRODUCTION_MANIFEST_SHA256=%s\n' \
    "${PRODUCTION_MANIFEST_SHA}"
printf '[R03-VERIFY] ARTIFACT_MANIFEST_SHA256=%s\n' \
    "${ARTIFACT_MANIFEST_SHA}"
