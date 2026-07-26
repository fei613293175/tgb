#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

NGINX_CONFIG="/www/server/panel/vhost/nginx/tg-h5-ui-r02-loopback.conf"
STAGING_PRIVATE="/www/staging/tg-h5-ui-r02/private"
EXPECTED_INCLUDE="    include /www/server/nginx/conf/enable-php-74.conf;"
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"

[ "$(id -u)" -eq 0 ] || {
  printf '[R02-HEADERS] ABORT: root is required\n' >&2
  exit 1
}
[ -f "${NGINX_CONFIG}" ] || {
  printf '[R02-HEADERS] ABORT: R02 Nginx config is absent\n' >&2
  exit 1
}
[ -x "${PANEL_PYTHON}" ] || {
  printf '[R02-HEADERS] ABORT: panel Python is unavailable\n' >&2
  exit 1
}

REPAIR_ID="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP_DIR="${STAGING_PRIVATE}/nginx-backups/${REPAIR_ID}"
mkdir -p "${BACKUP_DIR}"
cp -a "${NGINX_CONFIG}" "${BACKUP_DIR}/tg-h5-ui-r02-loopback.conf"
sha256sum "${NGINX_CONFIG}" > "${BACKUP_DIR}/BEFORE_SHA256.txt"

"${PANEL_PYTHON}" - "${NGINX_CONFIG}" "${EXPECTED_INCLUDE}" <<'PY'
from pathlib import Path
import sys

path = Path(sys.argv[1])
expected = sys.argv[2]
text = path.read_text(encoding="utf-8")
replacement = """    location ~ [^/]\\.php(/|$) {
        try_files $uri =404;
        fastcgi_pass unix:/tmp/php-cgi-74.sock;
        fastcgi_index index.php;
        include /www/server/nginx/conf/fastcgi.conf;
        include /www/server/nginx/conf/pathinfo.conf;
        fastcgi_param HTTP_USER_AGENT $http_user_agent;
        fastcgi_param HTTP_COOKIE $http_cookie;
        fastcgi_param HTTP_ACCEPT $http_accept;
        fastcgi_param HTTP_ACCEPT_LANGUAGE $http_accept_language;
        fastcgi_param HTTP_REFERER $http_referer;
        fastcgi_param HTTP_X_REQUESTED_WITH $http_x_requested_with;
    }"""
if replacement in text:
    raise SystemExit(0)
if text.count(expected) != 1:
    raise SystemExit("expected PHP include was not found exactly once")
with path.open("w", encoding="utf-8", newline="\n") as handle:
    handle.write(text.replace(expected, replacement))
PY

if ! nginx -t; then
  cp -a "${BACKUP_DIR}/tg-h5-ui-r02-loopback.conf" "${NGINX_CONFIG}"
  nginx -t
  printf '[R02-HEADERS] ABORT: invalid repaired config; backup restored\n' >&2
  exit 1
fi

nginx -s reload
sleep 1
sha256sum "${NGINX_CONFIG}" > "${BACKUP_DIR}/AFTER_SHA256.txt"
chmod -R a-w "${BACKUP_DIR}"

MOBILE_LOCATION="$(curl -sS -D - -o /dev/null \
  -H 'Host: tg-h5-ui-r02.local' \
  -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  "http://127.0.0.1:18082/plugin.php?id=xigua_hb" |
  awk 'BEGIN{IGNORECASE=1} /^location:/{sub(/\r$/, ""); print substr($0, 11); exit}')"

case "${MOBILE_LOCATION}" in
  *"member.php?mod=logging"*"mobile=2"*) ;;
  *)
    printf '[R02-HEADERS] ABORT: mobile user-agent was not observed by Discuz\n' >&2
    exit 1
    ;;
esac

printf '[R02-HEADERS] PASS repair_id=%s\n' "${REPAIR_ID}"
printf '[R02-HEADERS] MOBILE_LOCATION=%s\n' "${MOBILE_LOCATION}"
printf '[R02-HEADERS] BACKUP_DIR=%s\n' "${BACKUP_DIR}"
