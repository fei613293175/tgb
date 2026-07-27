#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
SITE="/www/staging/tg-h5-ui-r08/site"
PRIVATE="/www/staging/tg-h5-ui-r08/private"
ACTIVE="${PRIVATE}/r09-cashier-fixture-active"
HISTORY_ROOT="${PRIVATE}/r09-cashier-fixture-history"
AUTH_BRIDGE="${SITE}/__r08_auth__"
ORIGIN_ACTIVE="${PRIVATE}/browser-origin-active"
STAGE_DB="tgb_stage_r08_main"
FIXTURE_USER="tgb_r02_visual"
DEBUG_LOG="${SITE}/data/member_pay_debug.log"
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"
ROOT_CNF=""

fail() { printf '[R09-CASHIER-FIXTURE] ABORT: %s\n' "$1" >&2; exit 1; }
cleanup() { [ -z "${ROOT_CNF}" ] || rm -f -- "${ROOT_CNF}"; }
trap cleanup EXIT

[ "$(id -u)" -eq 0 ] || fail 'root is required'
[ -d "${SITE}/source/plugin" ] || fail 'R08 staging site is absent'
[ "$(stat -c '%a' "${PRIVATE}")" = '700' ] || fail 'staging private directory mode is not 700'
grep -Fq "${STAGE_DB}" "${SITE}/config/config_global.php" || fail 'not the R08 clone database'
grep -Fq 'tgb_stage_r08_uc' "${SITE}/config/config_ucenter.php" || fail 'not the R08 UCenter clone'

ROOT_CNF="$(mktemp /tmp/tgb-r09-cashier.XXXXXX.cnf)"
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
mysql --defaults-extra-file="${ROOT_CNF}" -NBe 'SELECT 1' >/dev/null || fail 'MySQL root probe failed'

sql() {
  mysql --defaults-extra-file="${ROOT_CNF}" --batch --raw --skip-column-names "${STAGE_DB}" -e "$1"
}

fixture_uid() {
  sql "SELECT uid FROM pre_common_member WHERE username='${FIXTURE_USER}' LIMIT 1"
}

assert_tables() {
  [ "$(sql "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${STAGE_DB}' AND table_name='pre_tb_member_order'")" = '1' ] || fail 'member order table is absent'
  [ "$(sql "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${STAGE_DB}' AND table_name='pre_tb_pay'")" = '1' ] || fail 'payment order table is absent'
}

case "${MODE}" in
  on)
    [ ! -e "${ACTIVE}" ] || fail 'cashier fixture window is already active'
    [ ! -e "${AUTH_BRIDGE}" ] || fail 'authentication bridge must be OFF before fixture-on'
    [ ! -e "${ORIGIN_ACTIVE}" ] || fail 'browser-origin bridge must be OFF before fixture-on'
    assert_tables
    uid="$(fixture_uid)"
    [[ "${uid}" =~ ^[0-9]+$ ]] || fail 'fixture user is absent'
    install -d -m 0700 "${ACTIVE}"
    printf 'uid=%s\nstart_epoch=%s\n' "${uid}" "$(date +%s)" >"${ACTIVE}/metadata.env"
    printf 'member_orders=%s\npay_orders=%s\n' \
      "$(sql "SELECT COUNT(*) FROM pre_tb_member_order WHERE uid=${uid}")" \
      "$(sql "SELECT COUNT(*) FROM pre_tb_pay WHERE uid=${uid}")" >"${ACTIVE}/baseline-counts.env"
    if [ -f "${DEBUG_LOG}" ]; then
      cp -a -- "${DEBUG_LOG}" "${ACTIVE}/member_pay_debug.log.before"
      printf 'debug_log=present\n' >"${ACTIVE}/debug-log.env"
    else
      printf 'debug_log=absent\n' >"${ACTIVE}/debug-log.env"
    fi
    chmod -R go-rwx "${ACTIVE}"
    printf '[R09-CASHIER-FIXTURE] ON uid=sanitized\n'
    ;;
  off)
    [ -d "${ACTIVE}" ] || fail 'cashier fixture window is not active'
    [ ! -e "${AUTH_BRIDGE}" ] || fail 'authentication bridge must be OFF before fixture cleanup'
    [ ! -e "${ORIGIN_ACTIVE}" ] || fail 'browser-origin bridge must be OFF before fixture cleanup'
    # shellcheck disable=SC1091
    source "${ACTIVE}/metadata.env"
    [[ "${uid:-}" =~ ^[0-9]+$ ]] || fail 'fixture uid metadata is invalid'
    [[ "${start_epoch:-}" =~ ^[0-9]+$ ]] || fail 'fixture start metadata is invalid'
    [ "$(fixture_uid)" = "${uid}" ] || fail 'fixture user identity drift'
    sql "SELECT orderid, uid, price, paystatus, dateLine FROM pre_tb_member_order WHERE uid=${uid} AND dateLine>=${start_epoch} ORDER BY dateLine, orderid" >"${ACTIVE}/created-member-orders.tsv"
    sql "SELECT orderid, uid, price, ostatus, dateline FROM pre_tb_pay WHERE uid=${uid} AND dateline>=${start_epoch} ORDER BY dateline, orderid" >"${ACTIVE}/created-pay-orders.tsv"
    sql "DELETE FROM pre_tb_pay WHERE uid=${uid} AND dateline>=${start_epoch}"
    sql "DELETE FROM pre_tb_member_order WHERE uid=${uid} AND dateLine>=${start_epoch} AND orderid LIKE 'MB%'"
    [ "$(sql "SELECT COUNT(*) FROM pre_tb_member_order WHERE uid=${uid} AND dateLine>=${start_epoch}")" = '0' ] || fail 'member order cleanup failed'
    [ "$(sql "SELECT COUNT(*) FROM pre_tb_pay WHERE uid=${uid} AND dateline>=${start_epoch}")" = '0' ] || fail 'payment order cleanup failed'
    if grep -Fxq 'debug_log=present' "${ACTIVE}/debug-log.env"; then
      install -o www -g www -m 0644 "${ACTIVE}/member_pay_debug.log.before" "${DEBUG_LOG}"
    else
      rm -f -- "${DEBUG_LOG}"
    fi
    install -d -m 0700 "${HISTORY_ROOT}"
    history="${HISTORY_ROOT}/$(date '+%Y%m%dT%H%M%S%z')"
    [ ! -e "${history}" ] || fail 'fixture history path already exists'
    mv -- "${ACTIVE}" "${history}"
    chmod -R a-w "${history}"
    printf '[R09-CASHIER-FIXTURE] OFF cleanup=PASS history=%s\n' "${history}"
    ;;
  status)
    assert_tables
    uid="$(fixture_uid)"
    [[ "${uid}" =~ ^[0-9]+$ ]] || fail 'fixture user is absent'
    if [ -d "${ACTIVE}" ]; then
      printf '[R09-CASHIER-FIXTURE] STATUS=ACTIVE uid=sanitized\n'
    else
      printf '[R09-CASHIER-FIXTURE] STATUS=OFF uid=sanitized\n'
    fi
    ;;
  *) fail 'usage: r09_cashier_fixture.sh on|off|status' ;;
esac
