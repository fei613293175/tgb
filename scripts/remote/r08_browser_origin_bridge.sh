#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
LOCAL_PORT="${2:-28088}"
STAGING_SITE="/www/staging/tg-h5-ui-r08/site"
STAGING_PRIVATE="/www/staging/tg-h5-ui-r08/private"
ACTIVE_DIR="${STAGING_PRIVATE}/browser-origin-active"
HISTORY_ROOT="${STAGING_PRIVATE}/browser-origin-history"
LOCK_FILE="${STAGING_PRIVATE}/browser-origin.lock"
AUTH_BRIDGE_DIR="${STAGING_SITE}/__r08_auth__"
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"
STAGE_MAIN_DB="tgb_stage_r08_main"
NORMAL_ORIGIN="http://tg-h5-ui-r08.local:18088"
ROOT_CNF=""

fail() { printf '[R08-BROWSER-ORIGIN] ABORT: %s\n' "$1" >&2; exit 1; }
cleanup() { [ -z "${ROOT_CNF}" ] || rm -f -- "${ROOT_CNF}"; }
trap cleanup EXIT

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${STAGING_SITE}/source/plugin" ] || fail "R08 staging site is absent"
[ "$(stat -c '%a' "${STAGING_PRIVATE}")" = "700" ] || fail "R08 private directory mode is not 700"
grep -Fq "${STAGE_MAIN_DB}" "${STAGING_SITE}/config/config_global.php" || fail "main database is not the R08 clone"
grep -Fq "tgb_stage_r08_uc" "${STAGING_SITE}/config/config_ucenter.php" || fail "UCenter database is not the R08 clone"

case "${LOCAL_PORT}" in ''|*[!0-9]*) fail "local port must be numeric" ;; esac
[ "${LOCAL_PORT}" -ge 1024 ] && [ "${LOCAL_PORT}" -le 65535 ] || fail "local port must be between 1024 and 65535"
TEST_ORIGIN="http://127.0.0.1:${LOCAL_PORT}"

exec 9>"${LOCK_FILE}"
flock -n 9 || fail "another browser-origin operation is active"

ROOT_CNF="$(mktemp /tmp/tgb-r08-browser-origin.XXXXXX.cnf)"
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
    raise SystemExit("panel MySQL credential is unavailable")
password = str(password_value).replace("\\", "\\\\").replace('"', '\\"')
with open(target, "w", encoding="utf-8", newline="\n") as handle:
    handle.write('[client]\nhost="127.0.0.1"\nport=3306\nuser="root"\n')
    handle.write(f'password="{password}"\n')
PY
chmod 600 "${ROOT_CNF}"
mysql --defaults-extra-file="${ROOT_CNF}" -NBe "SELECT 1" >/dev/null || fail "MySQL root probe failed"

read_settings() {
    mysql --defaults-extra-file="${ROOT_CNF}" --batch --raw --skip-column-names "${STAGE_MAIN_DB}" \
        -e "SELECT skey, svalue FROM pre_common_setting WHERE skey IN ('bburl','siteurl') ORDER BY skey"
}

expected_rows() {
    local shape="$1" origin="$2"
    awk -F '\t' -v origin="${origin}" 'BEGIN { OFS="\t" } $1 == "bburl" || $1 == "siteurl" { print $1, origin }' "${shape}"
}

assert_valid_shape() {
    local rows="$1" expected="$2"
    case "${rows}" in
        "$(printf 'siteurl\t%s' "${expected}")"|"$(printf 'bburl\t%s\nsiteurl\t%s' "${expected}" "${expected}")") ;;
        *) fail "stage setting keys or values do not match expected origin" ;;
    esac
}

assert_settings_from_shape() {
    local expected="$1" shape="$2" rows
    rows="$(read_settings)"
    [ "${rows}" = "$(expected_rows "${shape}" "${expected}")" ] || fail "stage setting shape or values changed"
}

update_settings() {
    local origin="$1"
    case "${origin}" in "${NORMAL_ORIGIN}"|"${TEST_ORIGIN}") ;; *) fail "refusing unexpected origin value" ;; esac
    mysql --defaults-extra-file="${ROOT_CNF}" "${STAGE_MAIN_DB}" -e \
        "UPDATE pre_common_setting SET svalue='${origin}' WHERE skey IN ('siteurl', 'bburl')"
    cd "${STAGING_SITE}"
    runuser -u www -- php <<'PHP'
<?php
define('CURSCRIPT', 'r08browserorigin');
$_SERVER['PHP_SELF'] = '/r08browserorigin.php';
$_SERVER['SCRIPT_NAME'] = '/r08browserorigin.php';
$_SERVER['SCRIPT_FILENAME'] = '/www/staging/tg-h5-ui-r08/site/r08browserorigin.php';
$_SERVER['REQUEST_URI'] = '/r08browserorigin.php';
$_SERVER['HTTP_HOST'] = 'tg-h5-ui-r08.local';
$_SERVER['SERVER_NAME'] = 'tg-h5-ui-r08.local';
$_SERVER['SERVER_PORT'] = '18088';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
chdir('/www/staging/tg-h5-ui-r08/site');
require './source/class/class_core.php';
$discuz = C::app();
$discuz->init();
require_once libfile('function/cache');
updatecache('setting');
echo "[R08-BROWSER-ORIGIN] CACHE=REFRESHED\n";
PHP
}

case "${MODE}" in
    browser-on)
        [ ! -e "${AUTH_BRIDGE_DIR}" ] || fail "authentication bridge must be OFF before browser-on"
        [ ! -e "${ACTIVE_DIR}" ] || fail "browser-origin mode is already active"
        current_rows="$(read_settings)"
        assert_valid_shape "${current_rows}" "${NORMAL_ORIGIN}"
        install -d -m 0700 "${ACTIVE_DIR}"
        printf '%s\n' "${current_rows}" >"${ACTIVE_DIR}/settings.tsv"
        printf 'local_port=%s\ntest_origin=%s\nnormal_origin=%s\n' "${LOCAL_PORT}" "${TEST_ORIGIN}" "${NORMAL_ORIGIN}" >"${ACTIVE_DIR}/metadata.txt"
        chmod 600 "${ACTIVE_DIR}/settings.tsv" "${ACTIVE_DIR}/metadata.txt"
        update_settings "${TEST_ORIGIN}"
        assert_settings_from_shape "${TEST_ORIGIN}" "${ACTIVE_DIR}/settings.tsv"
        printf '[R08-BROWSER-ORIGIN] ACTIVE origin=%s\n' "${TEST_ORIGIN}"
        ;;
    browser-off)
        [ ! -e "${AUTH_BRIDGE_DIR}" ] || fail "authentication bridge must be OFF before browser-off"
        [ -d "${ACTIVE_DIR}" ] || fail "browser-origin mode is not active"
        grep -Fxq "local_port=${LOCAL_PORT}" "${ACTIVE_DIR}/metadata.txt" || fail "active local port does not match"
        grep -Fxq "test_origin=${TEST_ORIGIN}" "${ACTIVE_DIR}/metadata.txt" || fail "active test origin does not match"
        saved_rows="$(cat "${ACTIVE_DIR}/settings.tsv")"
        assert_valid_shape "${saved_rows}" "${NORMAL_ORIGIN}"
        assert_settings_from_shape "${TEST_ORIGIN}" "${ACTIVE_DIR}/settings.tsv"
        update_settings "${NORMAL_ORIGIN}"
        assert_settings_from_shape "${NORMAL_ORIGIN}" "${ACTIVE_DIR}/settings.tsv"
        install -d -m 0700 "${HISTORY_ROOT}"
        history_dir="${HISTORY_ROOT}/$(date '+%Y%m%dT%H%M%S%z')"
        [ ! -e "${history_dir}" ] || fail "browser-origin history path exists"
        mv -- "${ACTIVE_DIR}" "${history_dir}"
        chmod -R a-w "${history_dir}"
        printf '[R08-BROWSER-ORIGIN] OFF restored=%s history=%s\n' "${NORMAL_ORIGIN}" "${history_dir}"
        ;;
    status)
        rows="$(read_settings)"
        if [ -d "${ACTIVE_DIR}" ]; then
            saved_rows="$(cat "${ACTIVE_DIR}/settings.tsv")"
            assert_valid_shape "${saved_rows}" "${NORMAL_ORIGIN}"
            [ "${rows}" = "$(expected_rows "${ACTIVE_DIR}/settings.tsv" "${TEST_ORIGIN}")" ] || fail "STATUS=DRIFT"
            printf '[R08-BROWSER-ORIGIN] STATUS=ACTIVE origin=%s\n' "${TEST_ORIGIN}"
        elif assert_valid_shape "${rows}" "${NORMAL_ORIGIN}"; then
            printf '[R08-BROWSER-ORIGIN] STATUS=OFF origin=%s\n' "${NORMAL_ORIGIN}"
        else
            fail "STATUS=DRIFT"
        fi
        ;;
    *) fail "usage: r08_browser_origin_bridge.sh browser-on|browser-off|status [local-port]" ;;
esac
