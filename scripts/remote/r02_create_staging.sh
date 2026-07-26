#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

PRODUCTION_ROOT="/www/wwwroot/tg.suewammes.com"
STAGING_BASE="/www/staging/tg-h5-ui-r02"
STAGING_SITE="${STAGING_BASE}/site"
STAGING_PRIVATE="${STAGING_BASE}/private"
BACKUP_ROOT="/www/backup/tg-h5-ui-r02"
NGINX_CONFIG="/www/server/panel/vhost/nginx/tg-h5-ui-r02-loopback.conf"
LOOPBACK_PORT="18082"
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"

STAGE_MAIN_DB="tgb_stage_r02_main"
STAGE_MAIN_USER="tgb_r02_main"
STAGE_UC_DB="tgb_stage_r02_uc"
STAGE_UC_USER="tgb_r02_uc"

log() {
    printf '[R02] %s\n' "$1"
}

abort_if_exists() {
    if [ -e "$1" ]; then
        printf '[R02] ABORT existing path: %s\n' "$1" >&2
        exit 20
    fi
}

if [ "$(id -u)" -ne 0 ]; then
    printf '[R02] ABORT: root is required\n' >&2
    exit 21
fi

test -d "${PRODUCTION_ROOT}/source/plugin"
test -x "${PANEL_PYTHON}"
command -v mysql >/dev/null
command -v mysqldump >/dev/null
command -v nginx >/dev/null
command -v php >/dev/null
command -v openssl >/dev/null
command -v curl >/dev/null

abort_if_exists "${STAGING_BASE}"
abort_if_exists "${NGINX_CONFIG}"
if ss -ltn | awk '{print $4}' | grep -Eq "(^|:)${LOOPBACK_PORT}$"; then
    printf '[R02] ABORT busy loopback port: %s\n' "${LOOPBACK_PORT}" >&2
    exit 29
fi

SNAPSHOT_ID="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP_DIR="${BACKUP_ROOT}/${SNAPSHOT_ID}"
abort_if_exists "${BACKUP_DIR}"

mkdir -p "${BACKUP_DIR}" "${STAGING_PRIVATE}"
chmod 700 "${BACKUP_ROOT}" "${BACKUP_DIR}" "${STAGING_PRIVATE}"
chmod 711 "/www/staging" "${STAGING_BASE}"

ROOT_CNF="$(mktemp /tmp/tgb-r02-root.XXXXXX.cnf)"
cleanup() {
    rm -f "${ROOT_CNF}"
}
trap cleanup EXIT

log "create protected local MySQL client file"
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

log "resolve source database identifiers without printing credentials"
MAIN_SOURCE_DB="$(php -r 'include "/www/wwwroot/tg.suewammes.com/config/config_global.php"; echo $_config["db"][1]["dbname"];')"
MAIN_TABLE_PREFIX="$(php -r 'include "/www/wwwroot/tg.suewammes.com/config/config_global.php"; echo $_config["db"][1]["tablepre"];')"
UC_SOURCE_DB="$(php -r 'include "/www/wwwroot/tg.suewammes.com/uc_server/data/config.inc.php"; echo UC_DBNAME;')"
UC_TABLE_PREFIX="$(php -r 'include "/www/wwwroot/tg.suewammes.com/uc_server/data/config.inc.php"; echo UC_DBTABLEPRE;')"

case "${MAIN_SOURCE_DB}:${MAIN_TABLE_PREFIX}:${UC_SOURCE_DB}:${UC_TABLE_PREFIX}" in
    *[!A-Za-z0-9_:]*)
        printf '[R02] ABORT: database identifier contains unsupported characters\n' >&2
        exit 22
        ;;
esac

for db_name in "${STAGE_MAIN_DB}" "${STAGE_UC_DB}"; do
    if mysql --defaults-extra-file="${ROOT_CNF}" -NBe \
        "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='${db_name}'" |
        grep -q .; then
        printf '[R02] ABORT existing database: %s\n' "${db_name}" >&2
        exit 23
    fi
done

for db_user in "${STAGE_MAIN_USER}" "${STAGE_UC_USER}"; do
    if mysql --defaults-extra-file="${ROOT_CNF}" -NBe \
        "SELECT User FROM mysql.user WHERE User='${db_user}'" |
        grep -q .; then
        printf '[R02] ABORT existing database user: %s\n' "${db_user}" >&2
        exit 24
    fi
done

log "capture stable production code manifest before work"
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
    xargs -0 sha256sum >"${BACKUP_DIR}/production-code-before.sha256"

log "create code snapshot"
tar -czf "${BACKUP_DIR}/site-code.tar.gz" \
    -C /www/wwwroot \
    --exclude='tg.suewammes.com/data/attachment' \
    --exclude='tg.suewammes.com/data/cache' \
    --exclude='tg.suewammes.com/data/log' \
    --exclude='tg.suewammes.com/data/template' \
    --exclude='tg.suewammes.com/uc_server/data/avatar' \
    --exclude='tg.suewammes.com/uc_server/data/cache' \
    --exclude='tg.suewammes.com/uc_server/data/logs' \
    --exclude='tg.suewammes.com/.well-known' \
    --exclude='tg.suewammes.com/bbb.suewammes.com_DAz6e.zip' \
    --exclude='tg.suewammes.com/tools.php' \
    --exclude='tg.suewammes.com/phpinfo.php' \
    --exclude='tg.suewammes.com/log.txt' \
    --exclude='tg.suewammes.com/test*.php' \
    tg.suewammes.com

log "create user-upload snapshot"
UPLOAD_LIST="${BACKUP_DIR}/upload-paths.txt"
: >"${UPLOAD_LIST}"
for relative in data/attachment uc_server/data/avatar; do
    if [ -e "${PRODUCTION_ROOT}/${relative}" ]; then
        printf 'tg.suewammes.com/%s\n' "${relative}" >>"${UPLOAD_LIST}"
    fi
done
if [ -s "${UPLOAD_LIST}" ]; then
    tar -czf "${BACKUP_DIR}/site-uploads.tar.gz" \
        -C /www/wwwroot \
        -T "${UPLOAD_LIST}"
else
    tar -czf "${BACKUP_DIR}/site-uploads.tar.gz" \
        --files-from /dev/null
fi

log "dump main and UCenter databases"
mysqldump --defaults-extra-file="${ROOT_CNF}" \
    --single-transaction --quick --routines --triggers --events \
    --set-gtid-purged=OFF \
    --hex-blob --default-character-set=utf8mb4 \
    "${MAIN_SOURCE_DB}" |
    gzip -9 >"${BACKUP_DIR}/main-database.sql.gz"

mysqldump --defaults-extra-file="${ROOT_CNF}" \
    --single-transaction --quick --routines --triggers --events \
    --set-gtid-purged=OFF \
    --hex-blob --default-character-set=utf8mb4 \
    "${UC_SOURCE_DB}" |
    gzip -9 >"${BACKUP_DIR}/ucenter-database.sql.gz"

gzip -t "${BACKUP_DIR}/main-database.sql.gz"
gzip -t "${BACKUP_DIR}/ucenter-database.sql.gz"
tar -tzf "${BACKUP_DIR}/site-code.tar.gz" >/dev/null
tar -tzf "${BACKUP_DIR}/site-uploads.tar.gz" >/dev/null

sha256sum \
    "${BACKUP_DIR}/site-code.tar.gz" \
    "${BACKUP_DIR}/site-uploads.tar.gz" \
    "${BACKUP_DIR}/main-database.sql.gz" \
    "${BACKUP_DIR}/ucenter-database.sql.gz" \
    >"${BACKUP_DIR}/ARTIFACTS_SHA256.txt"

MAIN_STAGE_PASSWORD="$(openssl rand -hex 24)"
UC_STAGE_PASSWORD="$(openssl rand -hex 24)"

log "create isolated staging databases and users"
mysql --defaults-extra-file="${ROOT_CNF}" <<SQL
CREATE DATABASE \`${STAGE_MAIN_DB}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE DATABASE \`${STAGE_UC_DB}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER '${STAGE_MAIN_USER}'@'127.0.0.1' IDENTIFIED BY '${MAIN_STAGE_PASSWORD}';
CREATE USER '${STAGE_MAIN_USER}'@'localhost' IDENTIFIED BY '${MAIN_STAGE_PASSWORD}';
CREATE USER '${STAGE_UC_USER}'@'127.0.0.1' IDENTIFIED BY '${UC_STAGE_PASSWORD}';
CREATE USER '${STAGE_UC_USER}'@'localhost' IDENTIFIED BY '${UC_STAGE_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${STAGE_MAIN_DB}\`.* TO '${STAGE_MAIN_USER}'@'127.0.0.1';
GRANT ALL PRIVILEGES ON \`${STAGE_MAIN_DB}\`.* TO '${STAGE_MAIN_USER}'@'localhost';
GRANT ALL PRIVILEGES ON \`${STAGE_UC_DB}\`.* TO '${STAGE_UC_USER}'@'127.0.0.1';
GRANT ALL PRIVILEGES ON \`${STAGE_UC_DB}\`.* TO '${STAGE_UC_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

gzip -dc "${BACKUP_DIR}/main-database.sql.gz" |
    mysql --defaults-extra-file="${ROOT_CNF}" "${STAGE_MAIN_DB}"
gzip -dc "${BACKUP_DIR}/ucenter-database.sql.gz" |
    mysql --defaults-extra-file="${ROOT_CNF}" "${STAGE_UC_DB}"

log "restore code and uploads into isolated staging path"
mkdir -p "${STAGING_SITE}"
tar -xzf "${BACKUP_DIR}/site-code.tar.gz" \
    -C "${STAGING_SITE}" --strip-components=1
tar -xzf "${BACKUP_DIR}/site-uploads.tar.gz" \
    -C "${STAGING_SITE}" --strip-components=1

export STAGING_SITE STAGE_MAIN_DB STAGE_MAIN_USER STAGE_UC_DB STAGE_UC_USER UC_TABLE_PREFIX
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
uc_table_prefix = php_single(os.environ["UC_TABLE_PREFIX"])
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
         f"define('UC_DBUSER', '{uc_user}');"),
        (r"define\('UC_DBPW',\s*'[^']*'\);",
         f"define('UC_DBPW', '{uc_password}');"),
        (r"define\('UC_DBNAME',\s*'[^']*'\);",
         f"define('UC_DBNAME', '{uc_db}');"),
        (r"define\('UC_DBTABLEPRE',\s*'[^']*'\);",
         f"define('UC_DBTABLEPRE', '`{uc_db}`.{uc_table_prefix}');"),
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
SET svalue='http://tg-h5-ui-r02.local:${LOOPBACK_PORT}'
WHERE skey IN ('siteurl', 'bburl');
SQL
mysql --defaults-extra-file="${ROOT_CNF}" "${STAGE_UC_DB}" <<SQL
UPDATE \`${UC_TABLE_PREFIX}applications\`
SET url='http://tg-h5-ui-r02.local:${LOOPBACK_PORT}/uc_server'
WHERE url <> '';
SQL

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
        printf '[R02] ABORT forbidden staging artifact: %s\n' "${forbidden}" >&2
        exit 25
    fi
done

log "install loopback-only read-only Nginx virtual host"
cat >"${NGINX_CONFIG}.new" <<NGINX
server {
    listen 127.0.0.1:${LOOPBACK_PORT};
    server_name tg-h5-ui-r02.local;
    root ${STAGING_SITE};
    index index.php index.html;
    charset utf-8;

    access_log ${STAGING_PRIVATE}/access.log;
    error_log ${STAGING_PRIVATE}/error.log;

    add_header X-Robots-Tag "noindex, nofollow, noarchive" always;
    add_header X-TGB-Staging "R02" always;

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

    location ~ [^/]\\.php(/|\$) {
        try_files \$uri =404;
        fastcgi_pass unix:/tmp/php-cgi-74.sock;
        fastcgi_index index.php;
        include /www/server/nginx/conf/fastcgi.conf;
        include /www/server/nginx/conf/pathinfo.conf;
        fastcgi_param HTTP_USER_AGENT \$http_user_agent;
        fastcgi_param HTTP_COOKIE \$http_cookie;
        fastcgi_param HTTP_ACCEPT \$http_accept;
        fastcgi_param HTTP_ACCEPT_LANGUAGE \$http_accept_language;
        fastcgi_param HTTP_REFERER \$http_referer;
        fastcgi_param HTTP_X_REQUESTED_WITH \$http_x_requested_with;
    }
}
NGINX
mv "${NGINX_CONFIG}.new" "${NGINX_CONFIG}"
if ! nginx -t; then
    mv "${NGINX_CONFIG}" "${NGINX_CONFIG}.failed"
    exit 26
fi
nginx -s reload
sleep 1

log "verify staging HTTP, redirects and database restore"
HTTP_FIRST="$(curl -sS -D "${STAGING_PRIVATE}/home.headers" \
    -o /dev/null \
    -w '%{http_code}' \
    -H 'Host: tg-h5-ui-r02.local' \
    -A 'Mozilla/5.0 (Linux; Android 14; Pixel 8 Pro) AppleWebKit/537.36 Chrome/138.0 Mobile Safari/537.36' \
    "http://127.0.0.1:${LOOPBACK_PORT}/plugin.php?id=xigua_hb")"
case "${HTTP_FIRST}" in
    200|302) ;;
    *)
        printf '[R02] ABORT staging first HTTP code: %s\n' "${HTTP_FIRST}" >&2
        exit 27
        ;;
esac
FOLLOW_RESULT="$(curl -sS -L --max-redirs 5 \
    --resolve "tg-h5-ui-r02.local:${LOOPBACK_PORT}:127.0.0.1" \
    -D "${STAGING_PRIVATE}/home-follow.headers" \
    -o "${STAGING_PRIVATE}/home.html" \
    -w '%{http_code}|%{url_effective}|%{num_redirects}' \
    -A 'Mozilla/5.0 (Linux; Android 14; Pixel 8 Pro) AppleWebKit/537.36 Chrome/138.0 Mobile Safari/537.36' \
    "http://tg-h5-ui-r02.local:${LOOPBACK_PORT}/plugin.php?id=xigua_hb")"
HTTP_FINAL="${FOLLOW_RESULT%%|*}"
FOLLOW_REST="${FOLLOW_RESULT#*|}"
FINAL_URL="${FOLLOW_REST%%|*}"
REDIRECT_COUNT="${FOLLOW_RESULT##*|}"
[ "${HTTP_FINAL}" = "200" ] || {
    printf '[R02] ABORT staging final HTTP code: %s\n' "${HTTP_FINAL}" >&2
    exit 27
}
case "${FINAL_URL}" in
    "http://tg-h5-ui-r02.local:${LOOPBACK_PORT}/"*) ;;
    *)
        printf '[R02] ABORT staging redirect escaped isolated host\n' >&2
        exit 27
        ;;
esac
grep -qiE '<html|<!doctype' "${STAGING_PRIVATE}/home.html" || {
    printf '[R02] ABORT staging response is not HTML\n' >&2
    exit 27
}
grep -qi '^X-TGB-Staging: R02' "${STAGING_PRIVATE}/home.headers" || {
    printf '[R02] ABORT staging marker header is absent\n' >&2
    exit 27
}

POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' \
    -X POST \
    -H 'Host: tg-h5-ui-r02.local' \
    "http://127.0.0.1:${LOOPBACK_PORT}/plugin.php?id=xigua_hb")"
if [ "${POST_CODE}" != "405" ]; then
    printf '[R02] ABORT staging write guard code: %s\n' "${POST_CODE}" >&2
    exit 28
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
cmp "${BACKUP_DIR}/production-code-before.sha256" \
    "${BACKUP_DIR}/production-code-after.sha256"

PRODUCTION_MANIFEST_SHA="$(sha256sum "${BACKUP_DIR}/production-code-before.sha256" | cut -d ' ' -f 1)"
ARTIFACT_MANIFEST_SHA="$(sha256sum "${BACKUP_DIR}/ARTIFACTS_SHA256.txt" | cut -d ' ' -f 1)"

cat >"${STAGING_PRIVATE}/R02_FACTS.txt" <<FACTS
snapshot_id=${SNAPSHOT_ID}
production_root=${PRODUCTION_ROOT}
staging_root=${STAGING_SITE}
staging_listener=127.0.0.1:${LOOPBACK_PORT}
staging_access=SSH tunnel only
staging_write_guard=GET_HEAD_ONLY
main_table_count=${MAIN_TABLES}
ucenter_table_count=${UC_TABLES}
http_first=${HTTP_FIRST}
http_final=${HTTP_FINAL}
redirect_count=${REDIRECT_COUNT}
http_post=${POST_CODE}
production_code_unchanged=PASS
production_manifest_sha256=${PRODUCTION_MANIFEST_SHA}
artifact_manifest_sha256=${ARTIFACT_MANIFEST_SHA}
dangerous_public_files_copied=NO
FACTS
chmod 600 "${STAGING_PRIVATE}/R02_FACTS.txt"

cat >"${BACKUP_DIR}/RESTORE_README.md" <<RESTORE
# R02 snapshot restore

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

printf '[R02] COMPLETE snapshot_id=%s\n' "${SNAPSHOT_ID}"
printf '[R02] STAGING_LISTENER=127.0.0.1:%s\n' "${LOOPBACK_PORT}"
printf '[R02] HTTP_FIRST=%s HTTP_FINAL=%s REDIRECTS=%s HTTP_POST=%s\n' \
    "${HTTP_FIRST}" "${HTTP_FINAL}" "${REDIRECT_COUNT}" "${POST_CODE}"
printf '[R02] MAIN_TABLES=%s UC_TABLES=%s\n' "${MAIN_TABLES}" "${UC_TABLES}"
printf '[R02] PRODUCTION_CODE_UNCHANGED=PASS\n'
printf '[R02] PRODUCTION_MANIFEST_SHA256=%s\n' "${PRODUCTION_MANIFEST_SHA}"
printf '[R02] ARTIFACT_MANIFEST_SHA256=%s\n' "${ARTIFACT_MANIFEST_SHA}"
