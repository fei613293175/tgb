#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
PUBID="${2:-}"
STAGE_DB="tgb_stage_r05_main"
PRIVATE_ROOT="/www/staging/tg-h5-ui-r05/private"
ACTIVE_DIR="${PRIVATE_ROOT}/detail-get-active"
HISTORY_ROOT="${PRIVATE_ROOT}/detail-get-history"
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"
FIXTURE_USERNAME="tgb_r02_visual"
ROOT_CNF=""

fail() { printf '[R05-DETAIL-ROLLBACK] ABORT: %s\n' "$1" >&2; exit 1; }
cleanup() { [ -z "${ROOT_CNF}" ] || rm -f -- "${ROOT_CNF}"; }
trap cleanup EXIT

[ "$(id -u)" -eq 0 ] || fail "root is required"
case "${PUBID}" in ''|*[!0-9]*) fail "pubid must be numeric" ;; esac
[ "${PUBID}" -gt 0 ] || fail "pubid must be positive"
[ -d "${PRIVATE_ROOT}" ] || fail "R05 private directory is absent"

ROOT_CNF="$(mktemp /tmp/r05-detail-get.XXXXXX.cnf)"
cd /www/server/panel
"${PANEL_PYTHON}" - "${ROOT_CNF}" <<'PY'
import os
import sys
sys.path.insert(0, "/www/server/panel/class")
os.chdir("/www/server/panel")
import public
target = sys.argv[1]
value = public.M("config").where("id=?", (1,)).getField("mysql_root")
if not value:
    raise SystemExit("panel MySQL credential is unavailable")
password = str(value).replace("\\", "\\\\").replace('"', '\\"')
with open(target, "w", encoding="utf-8", newline="\n") as handle:
    handle.write('[client]\nhost="127.0.0.1"\nport=3306\nuser="root"\n')
    handle.write(f'password="{password}"\n')
PY
chmod 600 "${ROOT_CNF}"
mysql --defaults-extra-file="${ROOT_CNF}" -NBe "SELECT 1" >/dev/null || fail "MySQL probe failed"

query() { mysql --defaults-extra-file="${ROOT_CNF}" --batch --raw --skip-column-names "${STAGE_DB}" -e "$1"; }
dump_rows() {
  local table="$1" where="$2" output="$3"
  mysqldump --defaults-extra-file="${ROOT_CNF}" --single-transaction --set-gtid-purged=OFF --no-create-info --skip-triggers --compact --skip-extended-insert --hex-blob "${STAGE_DB}" "${table}" --where="${where}" >"${output}"
}
compare_row_sets() {
  local before="$1" after="$2" scratch_before scratch_after status=0
  scratch_before="$(mktemp /tmp/r05-rowset-before.XXXXXX)"
  scratch_after="$(mktemp /tmp/r05-rowset-after.XXXXXX)"
  LC_ALL=C sort "${before}" >"${scratch_before}"
  LC_ALL=C sort "${after}" >"${scratch_after}"
  cmp "${scratch_before}" "${scratch_after}" || status=$?
  rm -f -- "${scratch_before}" "${scratch_after}"
  return "${status}"
}
compare_invariants() {
  local before_dir="$1" after_dir="$2"
  cmp "${before_dir}/pub-nonview.tsv" "${after_dir}/pub-nonview.tsv" || return 1
  for file in redpack-log.sql wallet-user.sql member-count.sql; do
    compare_row_sets "${before_dir}/${file}" "${after_dir}/${file}" || return 1
  done
}
capture_rows() {
  local directory="$1" uid="$2"
  dump_rows pre_xigua_hb_viewlog "visiter=${uid} OR uid=${PUBID}" "${directory}/viewlog.sql"
  dump_rows pre_view_user_view_log "uid=${uid} OR pubid=${PUBID}" "${directory}/view-user-log.sql"
  dump_rows pre_view_user_day_task "uid=${uid}" "${directory}/day-task.sql"
}
capture_invariants() {
  local directory="$1" uid="$2"
  install -d -m 0700 "${directory}"
  query "SELECT id,hb_money,hb_num,hb_sendnum,shares,votes,comments FROM pre_xigua_hb_pub WHERE id=${PUBID}" >"${directory}/pub-nonview.tsv"
  dump_rows pre_xigua_hb_hongbaolog "pubid=${PUBID}" "${directory}/redpack-log.sql"
  dump_rows pre_xigua_hb_user "uid=${uid}" "${directory}/wallet-user.sql"
  dump_rows pre_common_member_count "uid=${uid}" "${directory}/member-count.sql"
}

case "${MODE}" in
  before)
    [ ! -e "${ACTIVE_DIR}" ] || fail "another detail audit is active"
    UID_VALUE="$(query "SELECT uid FROM pre_common_member WHERE username='${FIXTURE_USERNAME}' LIMIT 1")"
    case "${UID_VALUE}" in ''|*[!0-9]*) fail "fixture uid is invalid" ;; esac
    VIEWS_VALUE="$(query "SELECT views FROM pre_xigua_hb_pub WHERE id=${PUBID} AND display=1 AND recycle=0 LIMIT 1")"
    case "${VIEWS_VALUE}" in ''|*[!0-9]*) fail "target publication is invalid" ;; esac
    install -d -m 0700 "${ACTIVE_DIR}"
    printf 'pubid=%s\nuid=%s\nviews=%s\n' "${PUBID}" "${UID_VALUE}" "${VIEWS_VALUE}" >"${ACTIVE_DIR}/state.env"
    capture_rows "${ACTIVE_DIR}" "${UID_VALUE}"
    capture_invariants "${ACTIVE_DIR}/invariants" "${UID_VALUE}"
    printf '[R05-DETAIL-ROLLBACK] BEFORE=READY\n'
    ;;
  restore)
    [ -d "${ACTIVE_DIR}" ] || fail "detail audit snapshot is absent"
    # shellcheck disable=SC1091
    source "${ACTIVE_DIR}/state.env"
    [ "${pubid}" = "${PUBID}" ] || fail "pubid does not match active snapshot"
    case "${uid}:${views}" in *[!0-9:]*) fail "active snapshot contains invalid numeric values" ;; esac
    CURRENT_VIEWS="$(query "SELECT views FROM pre_xigua_hb_pub WHERE id=${PUBID} LIMIT 1")"
    VIEW_DELTA="$((CURRENT_VIEWS - views))"
    capture_invariants "${ACTIVE_DIR}/invariants-before-restore" "${uid}"
    compare_invariants "${ACTIVE_DIR}/invariants" "${ACTIVE_DIR}/invariants-before-restore" || fail "money reward or counter invariant changed"

    query "UPDATE pre_xigua_hb_pub SET views=${views} WHERE id=${PUBID}"
    query "DELETE FROM pre_xigua_hb_viewlog WHERE visiter=${uid} OR uid=${PUBID}"
    query "DELETE FROM pre_view_user_view_log WHERE uid=${uid} OR pubid=${PUBID}"
    query "DELETE FROM pre_view_user_day_task WHERE uid=${uid}"
    query "DELETE FROM pre_common_process WHERE processid=CONCAT('iclk_',MD5(CONCAT(${PUBID},'views','127.0.0.1'))) OR processid=CONCAT('view_day_task_',${PUBID},'_',${uid})"
    mysql --defaults-extra-file="${ROOT_CNF}" "${STAGE_DB}" <"${ACTIVE_DIR}/viewlog.sql"
    mysql --defaults-extra-file="${ROOT_CNF}" "${STAGE_DB}" <"${ACTIVE_DIR}/view-user-log.sql"
    mysql --defaults-extra-file="${ROOT_CNF}" "${STAGE_DB}" <"${ACTIVE_DIR}/day-task.sql"

    POST_DIR="${ACTIVE_DIR}/post-restore"
    install -d -m 0700 "${POST_DIR}"
    capture_rows "${POST_DIR}" "${uid}"
    capture_invariants "${POST_DIR}/invariants" "${uid}"
    [ "$(query "SELECT views FROM pre_xigua_hb_pub WHERE id=${PUBID}")" = "${views}" ] || fail "views rollback mismatch"
    compare_row_sets "${ACTIVE_DIR}/viewlog.sql" "${POST_DIR}/viewlog.sql" || fail "viewlog rollback mismatch"
    compare_row_sets "${ACTIVE_DIR}/view-user-log.sql" "${POST_DIR}/view-user-log.sql" || fail "daily view log rollback mismatch"
    compare_row_sets "${ACTIVE_DIR}/day-task.sql" "${POST_DIR}/day-task.sql" || fail "daily task rollback mismatch"
    compare_invariants "${ACTIVE_DIR}/invariants" "${POST_DIR}/invariants" || fail "invariant rollback mismatch"

    install -d -m 0700 "${HISTORY_ROOT}"
    HISTORY_ID="$(date '+%Y%m%dT%H%M%S%z')"
    HISTORY_DIR="${HISTORY_ROOT}/${HISTORY_ID}"
    mv -- "${ACTIVE_DIR}" "${HISTORY_DIR}"
    chmod -R a-w "${HISTORY_DIR}"
    printf '[R05-DETAIL-ROLLBACK] RESTORE=PASS VIEW_DELTA=%s HISTORY=%s\n' "${VIEW_DELTA}" "${HISTORY_DIR}"
    ;;
  *) fail "usage: r05_detail_get_rollback.sh before|restore pubid" ;;
esac
