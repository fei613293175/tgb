#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

STAGING_BASE="/www/staging/tg-h5-ui-r05"
BACKUP_ROOT="/www/backup/tg-h5-ui-r05"
NGINX_CONFIG="/www/server/panel/vhost/nginx/tg-h5-ui-r05-loopback.conf"
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"

fail() {
    printf '[R05-CLEANUP] ABORT: %s\n' "$1" >&2
    exit 71
}

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ ! -e "${NGINX_CONFIG}" ] || fail "R05 vhost exists; this is not a partial pre-listener stage"
if ss -ltn | grep -Eq '127\.0\.0\.1:18085([^0-9]|$)'; then
    fail "R05 loopback listener is active"
fi

for target in "${STAGING_BASE}" "${BACKUP_ROOT}"; do
    if [ -e "${target}" ]; then
        resolved="$(readlink -f "${target}")"
        [ "${resolved}" = "${target}" ] || fail "unexpected cleanup target ${resolved}"
    fi
done

ROOT_CNF="$(mktemp /tmp/tgb-r05-cleanup.XXXXXX.cnf)"
cleanup() {
    rm -f "${ROOT_CNF}"
}
trap cleanup EXIT

"${PANEL_PYTHON}" - "${ROOT_CNF}" <<'PY'
import os
import sys

sys.path.insert(0, "/www/server/panel/class")
os.chdir("/www/server/panel")
import public

target = sys.argv[1]
value = public.M("config").where("id=?", (1,)).getField("mysql_root")
if not value:
    raise SystemExit("mysql root credential is unavailable")
password = str(value).replace("\\", "\\\\").replace('"', '\\"')
with open(target, "w", encoding="utf-8", newline="\n") as fh:
    fh.write('[client]\n')
    fh.write('host="127.0.0.1"\nport=3306\nuser="root"\n')
    fh.write(f'password="{password}"\n')
PY
chmod 600 "${ROOT_CNF}"
mysql --defaults-extra-file="${ROOT_CNF}" -NBe "SELECT 1" >/dev/null

mysql --defaults-extra-file="${ROOT_CNF}" <<'SQL'
DROP DATABASE IF EXISTS `tgb_stage_r05_main`;
DROP DATABASE IF EXISTS `tgb_stage_r05_uc`;
DROP USER IF EXISTS 'tgb_r05_main'@'127.0.0.1';
DROP USER IF EXISTS 'tgb_r05_main'@'localhost';
DROP USER IF EXISTS 'tgb_r05_uc'@'127.0.0.1';
DROP USER IF EXISTS 'tgb_r05_uc'@'localhost';
FLUSH PRIVILEGES;
SQL

rm -rf -- "${STAGING_BASE}" "${BACKUP_ROOT}"
printf '[R05-CLEANUP] PASS removed fixed incomplete R05 targets\n'
