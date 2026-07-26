#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

PRODUCTION_ROOT="/www/wwwroot/tg.suewammes.com"
STAGING_BASE="/www/staging/tg-h5-ui-r00"
STAGING_SITE="${STAGING_BASE}/site"
STAGING_PRIVATE="${STAGING_BASE}/private"
BACKUP_ROOT="/www/backup/tg-h5-ui-r00"
NGINX_CONFIG="/www/server/panel/vhost/nginx/tg-h5-ui-r00-loopback.conf"
OLD_PORT="18080"
LOOPBACK_PORT="18081"
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"
STAGE_MAIN_DB="tgb_stage_r00_main"
STAGE_UC_DB="tgb_stage_r00_uc"

log() {
    printf '[R00-REPAIR] %s\n' "$1"
}

if [ "$(id -u)" -ne 0 ]; then
    printf '[R00-REPAIR] ABORT: root is required\n' >&2
    exit 41
fi

test -d "${STAGING_SITE}/source/plugin"
test -f "${STAGING_SITE}/config/config_global.php"
test -f "${STAGING_SITE}/config/config_ucenter.php"
test -f "${STAGING_SITE}/uc_server/data/config.inc.php"
test -f "${NGINX_CONFIG}"
chmod 711 "/www/staging" "${STAGING_BASE}"
chmod 700 "${STAGING_PRIVATE}"

if ss -ltn | awk '{print $4}' | grep -Eq "(^|:)${LOOPBACK_PORT}$"; then
    printf '[R00-REPAIR] ABORT target port is busy: %s\n' \
        "${LOOPBACK_PORT}" >&2
    exit 42
fi

mapfile -t SNAPSHOT_IDS < <(
    find "${BACKUP_ROOT}" -mindepth 1 -maxdepth 1 -type d \
        ! -name '*.failed-*' -printf '%f\n' | sort
)
if [ "${#SNAPSHOT_IDS[@]}" -ne 1 ]; then
    printf '[R00-REPAIR] ABORT: expected one resumable snapshot, got %s\n' \
        "${#SNAPSHOT_IDS[@]}" >&2
    exit 43
fi
SNAPSHOT_ID="${SNAPSHOT_IDS[0]}"
BACKUP_DIR="${BACKUP_ROOT}/${SNAPSHOT_ID}"

log "verify snapshot artifacts before repair"
(cd "${BACKUP_DIR}" && sha256sum -c ARTIFACTS_SHA256.txt >/dev/null)

ROOT_CNF="$(mktemp /tmp/tgb-r00-repair.XXXXXX.cnf)"
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
mysql --defaults-extra-file="${ROOT_CNF}" -NBe "SELECT 1" >/dev/null

MAIN_TABLE_PREFIX="$(php -r 'include "/www/wwwroot/tg.suewammes.com/config/config_global.php"; echo $_config["db"][1]["tablepre"];')"
UC_TABLE_PREFIX="$(php -r 'include "/www/wwwroot/tg.suewammes.com/uc_server/data/config.inc.php"; echo UC_DBTABLEPRE;')"
case "${MAIN_TABLE_PREFIX}:${UC_TABLE_PREFIX}" in
    *[!A-Za-z0-9_:]*)
        printf '[R00-REPAIR] ABORT: table prefix contains unsupported characters\n' >&2
        exit 44
        ;;
esac

log "repair staging local URLs and open_basedir"
export STAGING_SITE OLD_PORT LOOPBACK_PORT
"${PANEL_PYTHON}" - <<'PY'
import os
import pathlib

site = pathlib.Path(os.environ["STAGING_SITE"])
old_port = os.environ["OLD_PORT"]
new_port = os.environ["LOOPBACK_PORT"]

config = site / "config/config_ucenter.php"
text = config.read_text(encoding="utf-8")
old = f"http://127.0.0.1:{old_port}/uc_server"
new = f"http://127.0.0.1:{new_port}/uc_server"
if text.count(old) != 1:
    raise SystemExit("unexpected old UC_API count")
config.write_text(text.replace(old, new), encoding="utf-8")

user_ini = site / ".user.ini"
user_ini.write_text(f"open_basedir={site}/:/tmp/\n", encoding="utf-8")
PY

mysql --defaults-extra-file="${ROOT_CNF}" "${STAGE_MAIN_DB}" <<SQL
UPDATE \`${MAIN_TABLE_PREFIX}common_setting\`
SET svalue='http://127.0.0.1:${LOOPBACK_PORT}'
WHERE skey IN ('siteurl', 'bburl');
SQL
mysql --defaults-extra-file="${ROOT_CNF}" "${STAGE_UC_DB}" <<SQL
UPDATE \`${UC_TABLE_PREFIX}applications\`
SET url='http://127.0.0.1:${LOOPBACK_PORT}/uc_server'
WHERE url <> '';
SQL

chown www:www \
    "${STAGING_SITE}/.user.ini" \
    "${STAGING_SITE}/config/config_ucenter.php"
chmod 640 \
    "${STAGING_SITE}/.user.ini" \
    "${STAGING_SITE}/config/config_ucenter.php"

log "move loopback listener away from pre-existing Docker service"
"${PANEL_PYTHON}" - "${NGINX_CONFIG}" "${OLD_PORT}" "${LOOPBACK_PORT}" <<'PY'
import pathlib
import sys

path = pathlib.Path(sys.argv[1])
old_port = sys.argv[2]
new_port = sys.argv[3]
text = path.read_text(encoding="utf-8")
old = f"listen 127.0.0.1:{old_port};"
new = f"listen 127.0.0.1:{new_port};"
if text.count(old) != 1:
    raise SystemExit("unexpected old Nginx listen count")
path.write_text(text.replace(old, new), encoding="utf-8")
PY

nginx -t
nginx -s reload
sleep 1
if ! ss -ltnp | grep -E "127\\.0\\.0\\.1:${LOOPBACK_PORT}.*nginx" >/dev/null; then
    printf '[R00-REPAIR] ABORT: host Nginx did not bind target port\n' >&2
    exit 45
fi

log "verify real Discuz H5 response and staging write guard"
FIRST_HTTP_CODE="$(curl -sS -D "${STAGING_PRIVATE}/home.headers" \
    -o /dev/null \
    -w '%{http_code}' \
    -H 'Host: tg-h5-ui-r00.local' \
    -A 'Mozilla/5.0 (Linux; Android 14; Pixel 8 Pro) AppleWebKit/537.36 Chrome/138.0 Mobile Safari/537.36' \
    "http://127.0.0.1:${LOOPBACK_PORT}/plugin.php?id=xigua_hb")"
case "${FIRST_HTTP_CODE}" in
    200|302) ;;
    *)
        printf '[R00-REPAIR] ABORT staging first-hop HTTP code: %s\n' \
            "${FIRST_HTTP_CODE}" >&2
        exit 46
        ;;
esac

FOLLOW_RESULT="$(curl -sS -L --max-redirs 5 \
    --resolve "tg-h5-ui-r00.local:${LOOPBACK_PORT}:127.0.0.1" \
    -D "${STAGING_PRIVATE}/home-follow.headers" \
    -o "${STAGING_PRIVATE}/home-follow.html" \
    -w '%{http_code}|%{url_effective}|%{num_redirects}' \
    -A 'Mozilla/5.0 (Linux; Android 14; Pixel 8 Pro) AppleWebKit/537.36 Chrome/138.0 Mobile Safari/537.36' \
    "http://tg-h5-ui-r00.local:${LOOPBACK_PORT}/plugin.php?id=xigua_hb")"
FINAL_HTTP_CODE="${FOLLOW_RESULT%%|*}"
FOLLOW_REST="${FOLLOW_RESULT#*|}"
FINAL_URL="${FOLLOW_REST%%|*}"
REDIRECT_COUNT="${FOLLOW_RESULT##*|}"
if [ "${FINAL_HTTP_CODE}" != "200" ]; then
    printf '[R00-REPAIR] ABORT staging final HTTP code: %s\n' \
        "${FINAL_HTTP_CODE}" >&2
    exit 46
fi
case "${FINAL_URL}" in
    "http://tg-h5-ui-r00.local:${LOOPBACK_PORT}/"*) ;;
    *)
        printf '[R00-REPAIR] ABORT staging redirect escaped host\n' >&2
        exit 47
        ;;
esac
if ! grep -qiE '<html|<!doctype' "${STAGING_PRIVATE}/home-follow.html"; then
    printf '[R00-REPAIR] ABORT staging response is not HTML\n' >&2
    exit 47
fi
if ! grep -qi '^X-TGB-Staging: R00' "${STAGING_PRIVATE}/home.headers"; then
    printf '[R00-REPAIR] ABORT staging marker header is absent\n' >&2
    exit 48
fi

POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' \
    -X POST \
    -H 'Host: tg-h5-ui-r00.local' \
    "http://127.0.0.1:${LOOPBACK_PORT}/plugin.php?id=xigua_hb")"
if [ "${POST_CODE}" != "405" ]; then
    printf '[R00-REPAIR] ABORT staging write guard code: %s\n' \
        "${POST_CODE}" >&2
    exit 49
fi

MAIN_TABLES="$(mysql --defaults-extra-file="${ROOT_CNF}" -NBe \
    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${STAGE_MAIN_DB}'")"
UC_TABLES="$(mysql --defaults-extra-file="${ROOT_CNF}" -NBe \
    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${STAGE_UC_DB}'")"
if [ "${MAIN_TABLES}" -lt 1 ] || [ "${UC_TABLES}" -lt 1 ]; then
    printf '[R00-REPAIR] ABORT restored database table count is zero\n' >&2
    exit 50
fi

log "prove production stable code tree is unchanged"
find "${PRODUCTION_ROOT}" -xdev -type f \
    ! -path "${PRODUCTION_ROOT}/data/attachment/*" \
    ! -path "${PRODUCTION_ROOT}/data/cache/*" \
    ! -path "${PRODUCTION_ROOT}/data/log/*" \
    ! -path "${PRODUCTION_ROOT}/data/template/*" \
    ! -path "${PRODUCTION_ROOT}/uc_server/data/avatar/*" \
    ! -path "${PRODUCTION_ROOT}/uc_server/data/cache/*" \
    ! -path "${PRODUCTION_ROOT}/uc_server/data/logs/*" \
    ! -name 'operation.log' \
    -print0 |
    sort -z |
    xargs -0 sha256sum >"${BACKUP_DIR}/production-code-after.sha256"
cmp "${BACKUP_DIR}/production-code-before.sha256" \
    "${BACKUP_DIR}/production-code-after.sha256"

PRODUCTION_MANIFEST_SHA="$(sha256sum "${BACKUP_DIR}/production-code-before.sha256" | cut -d ' ' -f 1)"
ARTIFACT_MANIFEST_SHA="$(sha256sum "${BACKUP_DIR}/ARTIFACTS_SHA256.txt" | cut -d ' ' -f 1)"

cat >"${STAGING_PRIVATE}/R00_FACTS.txt" <<FACTS
snapshot_id=${SNAPSHOT_ID}
production_root=${PRODUCTION_ROOT}
staging_root=${STAGING_SITE}
staging_listener=127.0.0.1:${LOOPBACK_PORT}
staging_access=SSH tunnel only
staging_write_guard=GET_HEAD_ONLY
main_table_count=${MAIN_TABLES}
ucenter_table_count=${UC_TABLES}
production_code_unchanged=PASS
production_manifest_sha256=${PRODUCTION_MANIFEST_SHA}
artifact_manifest_sha256=${ARTIFACT_MANIFEST_SHA}
dangerous_public_files_copied=NO
FACTS
chmod 600 "${STAGING_PRIVATE}/R00_FACTS.txt"

cat >"${BACKUP_DIR}/RESTORE_README.md" <<RESTORE
# R00 snapshot restore

Snapshot: ${SNAPSHOT_ID}

Artifacts are verified by ARTIFACTS_SHA256.txt.

Dry-run proof completed:
- site-code.tar.gz extracted into the isolated staging root;
- site-uploads.tar.gz extracted into the isolated staging root;
- main-database.sql.gz imported into an isolated database;
- ucenter-database.sql.gz imported into an isolated database;
- loopback staging returned real HTML with HTTP 200;
- non-GET requests returned HTTP 405;
- production stable-code manifest matched before and after.

Never restore directly over production while Nginx/PHP workers are serving it.
Use a new recovery directory and new recovery databases, verify there, then
switch paths during an approved maintenance window. Database credentials must
be supplied from the protected server source and must not be written here.
RESTORE
chmod 600 "${BACKUP_DIR}/RESTORE_README.md"

printf '[R00-REPAIR] COMPLETE snapshot_id=%s\n' "${SNAPSHOT_ID}"
printf '[R00-REPAIR] STAGING_LISTENER=127.0.0.1:%s\n' "${LOOPBACK_PORT}"
printf '[R00-REPAIR] HTTP_FIRST=%s HTTP_FINAL=%s REDIRECTS=%s HTTP_POST=%s\n' \
    "${FIRST_HTTP_CODE}" "${FINAL_HTTP_CODE}" "${REDIRECT_COUNT}" "${POST_CODE}"
printf '[R00-REPAIR] MAIN_TABLES=%s UC_TABLES=%s\n' \
    "${MAIN_TABLES}" "${UC_TABLES}"
printf '[R00-REPAIR] PRODUCTION_CODE_UNCHANGED=PASS\n'
printf '[R00-REPAIR] PRODUCTION_MANIFEST_SHA256=%s\n' \
    "${PRODUCTION_MANIFEST_SHA}"
printf '[R00-REPAIR] ARTIFACT_MANIFEST_SHA256=%s\n' \
    "${ARTIFACT_MANIFEST_SHA}"
