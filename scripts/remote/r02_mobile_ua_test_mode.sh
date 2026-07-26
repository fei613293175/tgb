#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
NGINX_CONFIG="/www/server/panel/vhost/nginx/tg-h5-ui-r02-loopback.conf"
STAGING_PRIVATE="/www/staging/tg-h5-ui-r02/private"
ACTIVE_DIR="${STAGING_PRIVATE}/mobile-ua-test-active"
ARCHIVE_ROOT="${STAGING_PRIVATE}/mobile-ua-test-history"
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"

fail() {
  printf '[R02-MOBILE-UA] ABORT: %s\n' "$1" >&2
  exit 1
}

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -f "${NGINX_CONFIG}" ] || fail "R02 Nginx config is absent"
[ -x "${PANEL_PYTHON}" ] || fail "panel Python is unavailable"

probe_post_guard() {
  local code
  code="$(curl -sS -o /dev/null -w '%{http_code}' \
    -X POST -H 'Host: tg-h5-ui-r02.local' \
    "http://127.0.0.1:18082/plugin.php?id=xigua_hb")"
  [ "${code}" = "405" ] || fail "POST guard is not closed"
  printf '%s' "${code}"
}

case "${MODE}" in
  start)
    [ ! -e "${ACTIVE_DIR}" ] || fail "mobile UA mode is already active"
    POST_CODE="$(probe_post_guard)"
    mkdir -p "${ACTIVE_DIR}"
    cp -a "${NGINX_CONFIG}" "${ACTIVE_DIR}/dynamic-ua.conf"
    sha256sum "${NGINX_CONFIG}" > "${ACTIVE_DIR}/BEFORE_SHA256.txt"
    date '+started_at=%Y-%m-%dT%H:%M:%S%z' > "${ACTIVE_DIR}/FACTS.env"

    "${PANEL_PYTHON}" - "${NGINX_CONFIG}" <<'PY'
from pathlib import Path
import sys

path = Path(sys.argv[1])
text = path.read_text(encoding="utf-8")
dynamic = "        fastcgi_param HTTP_USER_AGENT $http_user_agent;"
fixed = """        # R02 ephemeral mobile-UA mode; method guard remains GET/HEAD-only.
        fastcgi_param HTTP_USER_AGENT "TuiGuangBaoAndroid/1.0.0 Android";"""
if text.count(dynamic) != 1:
    raise SystemExit("dynamic user-agent parameter was not found exactly once")
with path.open("w", encoding="utf-8", newline="\n") as handle:
    handle.write(text.replace(dynamic, fixed))
PY

    if ! nginx -t; then
      cp -a "${ACTIVE_DIR}/dynamic-ua.conf" "${NGINX_CONFIG}"
      nginx -t
      fail "invalid mobile UA config; dynamic config restored"
    fi
    nginx -s reload
    sleep 1
    POST_CODE="$(probe_post_guard)"
    printf '[R02-MOBILE-UA] ACTIVE POST_HTTP=%s\n' "${POST_CODE}"
    ;;

  stop)
    [ -f "${ACTIVE_DIR}/dynamic-ua.conf" ] ||
      fail "mobile UA mode is not active"
    cp -a "${ACTIVE_DIR}/dynamic-ua.conf" "${NGINX_CONFIG}"
    nginx -t
    nginx -s reload
    sleep 1
    POST_CODE="$(probe_post_guard)"

    STOP_ID="$(date '+%Y%m%dT%H%M%S%z')"
    install -d -m 0700 "${ARCHIVE_ROOT}"
    sha256sum "${NGINX_CONFIG}" > "${ACTIVE_DIR}/RESTORED_SHA256.txt"
    date '+stopped_at=%Y-%m-%dT%H:%M:%S%z' >> "${ACTIVE_DIR}/FACTS.env"
    chmod -R a-w "${ACTIVE_DIR}"
    mv "${ACTIVE_DIR}" "${ARCHIVE_ROOT}/${STOP_ID}"
    printf '[R02-MOBILE-UA] RESTORED POST_HTTP=%s\n' "${POST_CODE}"
    printf '[R02-MOBILE-UA] HISTORY=%s\n' "${ARCHIVE_ROOT}/${STOP_ID}"
    ;;

  *)
    fail "usage: r02_mobile_ua_test_mode.sh start|stop"
    ;;
esac
