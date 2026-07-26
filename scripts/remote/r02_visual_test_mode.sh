#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
NGINX_CONFIG="/www/server/panel/vhost/nginx/tg-h5-ui-r02-loopback.conf"
STAGING_PRIVATE="/www/staging/tg-h5-ui-r02/private"
ACTIVE_DIR="${STAGING_PRIVATE}/visual-test-active"
ARCHIVE_ROOT="${STAGING_PRIVATE}/visual-test-history"
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"

fail() {
  printf '[R02-VISUAL] ABORT: %s\n' "$1" >&2
  exit 1
}

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -f "${NGINX_CONFIG}" ] || fail "R02 Nginx config is absent"
[ -x "${PANEL_PYTHON}" ] || fail "panel Python is unavailable"

case "${MODE}" in
  start)
    [ ! -e "${ACTIVE_DIR}" ] || fail "visual test mode is already active"
    mkdir -p "${ACTIVE_DIR}"
    cp -a "${NGINX_CONFIG}" "${ACTIVE_DIR}/read-only.conf"
    sha256sum "${NGINX_CONFIG}" > "${ACTIVE_DIR}/BEFORE_SHA256.txt"
    date '+started_at=%Y-%m-%dT%H:%M:%S%z' > "${ACTIVE_DIR}/FACTS.env"

    "${PANEL_PYTHON}" - "${NGINX_CONFIG}" <<'PY'
from pathlib import Path
import sys

path = Path(sys.argv[1])
text = path.read_text(encoding="utf-8")
old_guard = """    if ($request_method !~ ^(GET|HEAD)$) {
        return 405;
    }"""
test_guard = """    # R02 ephemeral visual-test mode; restored by r02_visual_test_mode.sh stop.
    if ($request_method !~ ^(GET|HEAD|POST)$) {
        return 405;
    }"""
old_ua = "        fastcgi_param HTTP_USER_AGENT $http_user_agent;"
test_ua = "        fastcgi_param HTTP_USER_AGENT \"TuiGuangBaoAndroid/1.0.0 Android\";"
if text.count(old_guard) != 1:
    raise SystemExit("read-only method guard was not found exactly once")
if text.count(old_ua) != 1:
    raise SystemExit("dynamic user-agent parameter was not found exactly once")
text = text.replace(old_guard, test_guard).replace(old_ua, test_ua)
with path.open("w", encoding="utf-8", newline="\n") as handle:
    handle.write(text)
PY

    if ! nginx -t; then
      cp -a "${ACTIVE_DIR}/read-only.conf" "${NGINX_CONFIG}"
      nginx -t
      fail "invalid visual-test config; read-only config restored"
    fi
    nginx -s reload
    sleep 1

    POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' \
      -X POST -H 'Host: tg-h5-ui-r02.local' \
      "http://127.0.0.1:18082/plugin.php?id=xigua_hb")"
    [ "${POST_CODE}" != "405" ] || fail "POST guard was not temporarily opened"
    printf '[R02-VISUAL] ACTIVE POST_HTTP=%s\n' "${POST_CODE}"
    ;;

  stop)
    [ -f "${ACTIVE_DIR}/read-only.conf" ] || fail "visual test mode is not active"
    cp -a "${ACTIVE_DIR}/read-only.conf" "${NGINX_CONFIG}"
    nginx -t
    nginx -s reload
    sleep 1

    POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' \
      -X POST -H 'Host: tg-h5-ui-r02.local' \
      "http://127.0.0.1:18082/plugin.php?id=xigua_hb")"
    [ "${POST_CODE}" = "405" ] || fail "read-only POST guard was not restored"

    STOP_ID="$(date '+%Y%m%dT%H%M%S%z')"
    mkdir -p "${ARCHIVE_ROOT}"
    sha256sum "${NGINX_CONFIG}" > "${ACTIVE_DIR}/RESTORED_SHA256.txt"
    date '+stopped_at=%Y-%m-%dT%H:%M:%S%z' >> "${ACTIVE_DIR}/FACTS.env"
    chmod -R a-w "${ACTIVE_DIR}"
    mv "${ACTIVE_DIR}" "${ARCHIVE_ROOT}/${STOP_ID}"
    printf '[R02-VISUAL] RESTORED POST_HTTP=%s\n' "${POST_CODE}"
    printf '[R02-VISUAL] HISTORY=%s\n' "${ARCHIVE_ROOT}/${STOP_ID}"
    ;;

  *)
    fail "usage: r02_visual_test_mode.sh start|stop"
    ;;
esac
