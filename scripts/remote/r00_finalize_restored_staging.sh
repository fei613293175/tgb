#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

PRODUCTION_ROOT="/www/wwwroot/tg.suewammes.com"
STAGING_BASE="/www/staging/tg-h5-ui-r00"
STAGING_SITE="${STAGING_BASE}/site"
STAGING_PRIVATE="${STAGING_BASE}/private"
BACKUP_ROOT="/www/backup/tg-h5-ui-r00"
NGINX_CONFIG="/www/server/panel/vhost/nginx/tg-h5-ui-r00-loopback.conf"
LOOPBACK_PORT="18081"
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"

STAGE_MAIN_DB="tgb_stage_r00_main"
STAGE_MAIN_USER="tgb_r00_main"
STAGE_UC_DB="tgb_stage_r00_uc"
STAGE_UC_USER="tgb_r00_uc"

log() {
    printf '[R00-FINALIZE] %s\n' "$1"
}

if [ "$(id -u)" -ne 0 ]; then
    printf '[R00-FINALIZE] ABORT: root is required\n' >&2
    exit 31
fi

test -d "${STAGING_SITE}/source/plugin"
test -f "${STAGING_SITE}/config/config_global.php"
test -f "${STAGING_SITE}/config/config_ucenter.php"
test -f "${STAGING_SITE}/uc_server/data/config.inc.php"
test ! -e "${NGINX_CONFIG}"
chmod 711 "/www/staging" "${STAGING_BASE}"
chmod 700 "${STAGING_PRIVATE}"
if ss -ltn | awk '{print $4}' | grep -Eq "(^|:)${LOOPBACK_PORT}$"; then
    printf '[R00-FINALIZE] ABORT busy loopback port: %s\n' \
        "${LOOPBACK_PORT}" >&2
    exit 39
fi

mapfile -t SNAPSHOT_IDS < <(
    find "${BACKUP_ROOT}" -mindepth 1 -maxdepth 1 -type d \
        ! -name '*.failed-*' -printf '%f\n' | sort
)
if [ "${#SNAPSHOT_IDS[@]}" -ne 1 ]; then
    printf '[R00-FINALIZE] ABORT: expected one resumable snapshot, got %s\n' \
        "${#SNAPSHOT_IDS[@]}" >&2
    exit 32
fi
SNAPSHOT_ID="${SNAPSHOT_IDS[0]}"
BACKUP_DIR="${BACKUP_ROOT}/${SNAPSHOT_ID}"

for artifact in \
    ARTIFACTS_SHA256.txt \
    site-code.tar.gz \
    site-uploads.tar.gz \
    main-database.sql.gz \
    ucenter-database.sql.gz \
    production-code-before.sha256; do
    test -f "${BACKUP_DIR}/${artifact}"
done

log "verify restored snapshot artifacts"
(cd "${BACKUP_DIR}" && sha256sum -c ARTIFACTS_SHA256.txt >/dev/null)

ROOT_CNF="$(mktemp /tmp/tgb-r00-finalize.XXXXXX.cnf)"
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

DB_COUNT="$(mysql --defaults-extra-file="${ROOT_CNF}" -NBe \
    "SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME IN ('${STAGE_MAIN_DB}','${STAGE_UC_DB}')")"
USER_COUNT="$(mysql --defaults-extra-file="${ROOT_CNF}" -NBe \
    "SELECT COUNT(*) FROM mysql.user WHERE User IN ('${STAGE_MAIN_USER}','${STAGE_UC_USER}')")"
if [ "${DB_COUNT}" != "2" ] || [ "${USER_COUNT}" != "4" ]; then
    printf '[R00-FINALIZE] ABORT: restored database/user checkpoint is incomplete\n' >&2
    exit 33
fi

MAIN_STAGE_PASSWORD="$(openssl rand -hex 24)"
UC_STAGE_PASSWORD="$(openssl rand -hex 24)"

log "rotate staging-only database credentials after checkpoint resume"
mysql --defaults-extra-file="${ROOT_CNF}" <<SQL
ALTER USER '${STAGE_MAIN_USER}'@'127.0.0.1' IDENTIFIED BY '${MAIN_STAGE_PASSWORD}';
ALTER USER '${STAGE_MAIN_USER}'@'localhost' IDENTIFIED BY '${MAIN_STAGE_PASSWORD}';
ALTER USER '${STAGE_UC_USER}'@'127.0.0.1' IDENTIFIED BY '${UC_STAGE_PASSWORD}';
ALTER USER '${STAGE_UC_USER}'@'localhost' IDENTIFIED BY '${UC_STAGE_PASSWORD}';
FLUSH PRIVILEGES;
SQL

MAIN_TABLE_PREFIX="$(php -r 'include "/www/wwwroot/tg.suewammes.com/config/config_global.php"; echo $_config["db"][1]["tablepre"];')"
UC_TABLE_PREFIX="$(php -r 'include "/www/wwwroot/tg.suewammes.com/uc_server/data/config.inc.php"; echo UC_DBTABLEPRE;')"
case "${MAIN_TABLE_PREFIX}:${UC_TABLE_PREFIX}" in
    *[!A-Za-z0-9_:]*)
        printf '[R00-FINALIZE] ABORT: table prefix contains unsupported characters\n' >&2
        exit 34
        ;;
esac

export STAGING_SITE STAGE_MAIN_DB STAGE_MAIN_USER STAGE_UC_DB STAGE_UC_USER
export MAIN_STAGE_PASSWORD UC_STAGE_PASSWORD LOOPBACK_PORT

log "rewrite staging-only database and local URL configuration"
"${PANEL_PYTHON}" - <<'PY'
import os
import pathlib
import re

site = pathlib.Path(os.environ["STAGING_SITE"])

def rewrite(path, replacements):
    text = path.read_text(encoding="utf-8")
    for pattern, replacement in replacements:
        text, count = re.subn(pattern, replacement, text, count=1)
        if count != 1:
            raise SystemExit(f"expected exactly one replacement in {path}: {pattern}")
    path.write_text(text, encoding="utf-8")

def php_single(value):
    return value.replace("\\", "\\\\").replace("'", "\\'")

main_user = php_single(os.environ["STAGE_MAIN_USER"])
main_password = php_single(os.environ["MAIN_STAGE_PASSWORD"])
main_db = php_single(os.environ["STAGE_MAIN_DB"])
uc_user = php_single(os.environ["STAGE_UC_USER"])
uc_password = php_single(os.environ["UC_STAGE_PASSWORD"])
uc_db = php_single(os.environ["STAGE_UC_DB"])
local_api = f"http://127.0.0.1:{os.environ['LOOPBACK_PORT']}/uc_server"

rewrite(
    site / "config/config_global.php",
    [
        (r"(\$_config\['db'\]\[1\]\['dbuser'\]\s*=\s*)'[^']*'(\s*;)",
         lambda m: m.group(1) + repr(main_user) + m.group(2)),
        (r"(\$_config\['db'\]\[1\]\['dbpw'\]\s*=\s*)'[^']*'(\s*;)",
         lambda m: m.group(1) + repr(main_password) + m.group(2)),
        (r"(\$_config\['db'\]\[1\]\['dbname'\]\s*=\s*)'[^']*'(\s*;)",
         lambda m: m.group(1) + repr(main_db) + m.group(2)),
    ],
)

rewrite(
    site / "config/config_ucenter.php",
    [
        (r"define\('UC_DBUSER',\s*'[^']*'\);",
         f"define('UC_DBUSER', '{main_user}');"),
        (r"define\('UC_DBPW',\s*'[^']*'\);",
         f"define('UC_DBPW', '{main_password}');"),
        (r"define\('UC_DBNAME',\s*'[^']*'\);",
         f"define('UC_DBNAME', '{main_db}');"),
        (r"define\('UC_API',\s*'[^']*'\);",
         f"define('UC_API', '{local_api}');"),
    ],
)

rewrite(
    site / "uc_server/data/config.inc.php",
    [
        (r"define\('UC_DBUSER',\s*'[^']*'\);",
         f"define('UC_DBUSER', '{uc_user}');"),
        (r"define\('UC_DBPW',\s*'[^']*'\);",
         f"define('UC_DBPW', '{uc_password}');"),
        (r"define\('UC_DBNAME',\s*'[^']*'\);",
         f"define('UC_DBNAME', '{uc_db}');"),
    ],
)

index = site / "index.php"
text = index.read_text(encoding="utf-8")
old = "https://tg.suewammes.com/plugin.php?id=xigua_hb"
if text.count(old) != 1:
    raise SystemExit("unexpected production absolute H5 entry count")
index.write_text(text.replace(old, "/plugin.php?id=xigua_hb"), encoding="utf-8")
PY

printf 'open_basedir=%s/:/tmp/\n' "${STAGING_SITE}" \
    >"${STAGING_SITE}/.user.ini"

log "point cloned site and UCenter application URLs at loopback staging"
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

mkdir -p \
    "${STAGING_SITE}/data/cache" \
    "${STAGING_SITE}/data/log" \
    "${STAGING_SITE}/data/template" \
    "${STAGING_SITE}/uc_server/data/cache" \
    "${STAGING_SITE}/uc_server/data/logs"
chown -R www:www "${STAGING_SITE}"
chmod 640 \
    "${STAGING_SITE}/.user.ini" \
    "${STAGING_SITE}/config/config_global.php" \
    "${STAGING_SITE}/config/config_ucenter.php" \
    "${STAGING_SITE}/uc_server/data/config.inc.php"

for forbidden in \
    bbb.suewammes.com_DAz6e.zip \
    tools.php \
    phpinfo.php \
    log.txt; do
    if [ -e "${STAGING_SITE}/${forbidden}" ]; then
        printf '[R00-FINALIZE] ABORT forbidden staging artifact: %s\n' \
            "${forbidden}" >&2
        exit 35
    fi
done

log "install loopback-only read-only Nginx virtual host"
cat >"${NGINX_CONFIG}.new" <<NGINX
server {
    listen 127.0.0.1:${LOOPBACK_PORT};
    server_name tg-h5-ui-r00.local;
    root ${STAGING_SITE};
    index index.php index.html;
    charset utf-8;

    access_log ${STAGING_PRIVATE}/access.log;
    error_log ${STAGING_PRIVATE}/error.log;

    add_header X-Robots-Tag "noindex, nofollow, noarchive" always;
    add_header X-TGB-Staging "R00" always;

    if (\$request_method !~ ^(GET|HEAD)\$) {
        return 405;
    }

    location ~ ^/(config|data/log|uc_server/data|install|fadmin|备份|\\.well-known)(/|\$) {
        deny all;
    }

    location ~* \\.(zip|tar|tar\\.gz|sql|log|bak|conf|ini)\$ {
        deny all;
    }

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    include /www/server/nginx/conf/enable-php-74.conf;
}
NGINX
mv "${NGINX_CONFIG}.new" "${NGINX_CONFIG}"
if ! nginx -t; then
    mv "${NGINX_CONFIG}" "${NGINX_CONFIG}.failed"
    exit 36
fi
nginx -s reload

log "verify staging HTTP and write guard"
HTTP_CODE="$(curl -sS -o "${STAGING_PRIVATE}/home.html" \
    -w '%{http_code}' \
    -H 'Host: tg-h5-ui-r00.local' \
    -A 'Mozilla/5.0 (Linux; Android 14; Pixel 8 Pro) AppleWebKit/537.36 Chrome/138.0 Mobile Safari/537.36' \
    "http://127.0.0.1:${LOOPBACK_PORT}/plugin.php?id=xigua_hb")"
if [ "${HTTP_CODE}" != "200" ]; then
    printf '[R00-FINALIZE] ABORT staging HTTP code: %s\n' "${HTTP_CODE}" >&2
    exit 37
fi

POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' \
    -X POST \
    -H 'Host: tg-h5-ui-r00.local' \
    "http://127.0.0.1:${LOOPBACK_PORT}/plugin.php?id=xigua_hb")"
if [ "${POST_CODE}" != "405" ]; then
    printf '[R00-FINALIZE] ABORT staging write guard code: %s\n' \
        "${POST_CODE}" >&2
    exit 38
fi

MAIN_TABLES="$(mysql --defaults-extra-file="${ROOT_CNF}" -NBe \
    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${STAGE_MAIN_DB}'")"
UC_TABLES="$(mysql --defaults-extra-file="${ROOT_CNF}" -NBe \
    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${STAGE_UC_DB}'")"

log "prove stable production code tree was not changed"
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
    "${BACKUP_DIR}/production-code-after.sha256"

PRODUCTION_MANIFEST_SHA="$(sha256sum "${STABLE_BEFORE}" | cut -d ' ' -f 1)"
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
- loopback staging returned HTTP 200;
- production stable-code manifest matched before and after.

Never restore directly over production while Nginx/PHP workers are serving it.
Use a new recovery directory and new recovery databases, verify there, then
switch paths during an approved maintenance window. Database credentials must
be supplied from the protected server source and must not be written here.
RESTORE
chmod 600 "${BACKUP_DIR}/RESTORE_README.md"

printf '[R00-FINALIZE] COMPLETE snapshot_id=%s\n' "${SNAPSHOT_ID}"
printf '[R00-FINALIZE] STAGING_LISTENER=127.0.0.1:%s\n' "${LOOPBACK_PORT}"
printf '[R00-FINALIZE] HTTP_GET=%s HTTP_POST=%s\n' "${HTTP_CODE}" "${POST_CODE}"
printf '[R00-FINALIZE] MAIN_TABLES=%s UC_TABLES=%s\n' \
    "${MAIN_TABLES}" "${UC_TABLES}"
printf '[R00-FINALIZE] PRODUCTION_CODE_UNCHANGED=PASS\n'
printf '[R00-FINALIZE] PRODUCTION_MANIFEST_SHA256=%s\n' \
    "${PRODUCTION_MANIFEST_SHA}"
printf '[R00-FINALIZE] ARTIFACT_MANIFEST_SHA256=%s\n' \
    "${ARTIFACT_MANIFEST_SHA}"
