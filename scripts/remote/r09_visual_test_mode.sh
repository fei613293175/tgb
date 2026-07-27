#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
NGINX_CONFIG="/www/server/panel/vhost/nginx/tg-h5-ui-r08-loopback.conf"
STAGING_PRIVATE="/www/staging/tg-h5-ui-r08/private"
ACTIVE_DIR="${STAGING_PRIVATE}/r09-visual-test-active"
HISTORY_ROOT="${STAGING_PRIVATE}/r09-visual-test-history"
HOST="tg-h5-ui-r08.local"
PORT="18088"
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"

fail() { printf '[R09-VISUAL] ABORT: %s\n' "$1" >&2; exit 1; }
[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -f "${NGINX_CONFIG}" ] || fail "R08 Nginx config is absent"
[ -x "${PANEL_PYTHON}" ] || fail "panel Python is unavailable"

probe_post() {
  curl -sS -o /dev/null -w '%{http_code}' -X POST -H "Host: ${HOST}" \
    "http://127.0.0.1:${PORT}/plugin.php?id=xigua_hb"
}

case "${MODE}" in
  start)
    [ ! -e "${ACTIVE_DIR}" ] || fail "visual test mode is already active"
    [ "$(probe_post)" = "405" ] || fail "POST guard is not initially closed"
    install -d -m 0700 "${ACTIVE_DIR}"
    cp -a "${NGINX_CONFIG}" "${ACTIVE_DIR}/read-only.conf"
    sha256sum "${NGINX_CONFIG}" >"${ACTIVE_DIR}/BEFORE_SHA256.txt"

    "${PANEL_PYTHON}" - "${NGINX_CONFIG}" <<'PY'
from pathlib import Path
import sys

path = Path(sys.argv[1])
text = path.read_text(encoding="utf-8")
closed = """    if ($request_method !~ ^(GET|HEAD)$) {
        return 405;
    }"""
opened = """    # R09 ephemeral visual-test mode; restored after browser review.
    if ($request_method !~ ^(GET|HEAD|POST)$) {
        return 405;
    }"""
if text.count(closed) != 1:
    raise SystemExit("read-only method guard was not found exactly once")
with path.open("w", encoding="utf-8", newline="\n") as handle:
    handle.write(text.replace(closed, opened))
PY
    if ! nginx -t; then
      cp -a "${ACTIVE_DIR}/read-only.conf" "${NGINX_CONFIG}"
      nginx -t
      fail "invalid test config; original restored"
    fi
    nginx -s reload
    sleep 1
    [ "$(probe_post)" != "405" ] || fail "POST window did not open"
    printf '[R09-VISUAL] ACTIVE POST_HTTP=%s\n' "$(probe_post)"
    ;;
  stop)
    [ -f "${ACTIVE_DIR}/read-only.conf" ] || fail "visual test mode is not active"
    cp -a "${ACTIVE_DIR}/read-only.conf" "${NGINX_CONFIG}"
    nginx -t
    nginx -s reload
    sleep 1
    [ "$(probe_post)" = "405" ] || fail "POST guard was not restored"
    sha256sum -c "${ACTIVE_DIR}/BEFORE_SHA256.txt" >/dev/null || fail "restored config hash mismatch"
    stop_id="$(date '+%Y%m%dT%H%M%S%z')"
    install -d -m 0700 "${HISTORY_ROOT}"
    chmod -R a-w "${ACTIVE_DIR}"
    mv "${ACTIVE_DIR}" "${HISTORY_ROOT}/${stop_id}"
    printf '[R09-VISUAL] RESTORED POST_HTTP=405 HISTORY=%s\n' "${HISTORY_ROOT}/${stop_id}"
    ;;
  status)
    if [ -d "${ACTIVE_DIR}" ]; then
      printf '[R09-VISUAL] STATUS=ACTIVE POST_HTTP=%s\n' "$(probe_post)"
    else
      printf '[R09-VISUAL] STATUS=OFF POST_HTTP=%s\n' "$(probe_post)"
    fi
    ;;
  *) fail "usage: r09_visual_test_mode.sh start|stop|status" ;;
esac
