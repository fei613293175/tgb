#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

ARCHIVE="${1:-/tmp/r09-member-chat-overlay.tar.gz}"
EXPECTED_ARCHIVE_SHA="${2:-}"
SITE="/www/staging/tg-h5-ui-r08/site"
PRIVATE="/www/staging/tg-h5-ui-r08/private"
BACKUP_ROOT="${PRIVATE}/change-backups"
EXPECTED_MEMBER_SHA="e787a81ab9306a0dc5d4b97e82de585d37f71831bec8ae31603eb0e5c41afbf8"
EXPECTED_CHAT_SHA="b0e370ebcb8aee006c88e4c26dbb6a1ad57693fd9a245303a20181b51f8857bb"
TMP=""
BACKUP=""
DEPLOYED=0

fail() { printf '[R09-MEMBER-CHAT-DEPLOY] ABORT: %s\n' "$1" >&2; exit 1; }

cleanup() {
  [ -z "${TMP}" ] || rm -rf -- "${TMP}"
}

rollback_on_error() {
  local code=$?
  if [ "${DEPLOYED}" -eq 1 ] && [ -n "${BACKUP}" ] && [ -d "${BACKUP}/files" ]; then
    cp -a -- "${BACKUP}/files/." "${SITE}/"
    rm -f -- \
      "${SITE}/source/plugin/xigua_hb/static/tgb-r09/member-detail-light-grid-r09.css" \
      "${SITE}/source/plugin/xigua_lt/static/tgb-r09/chat-detail-light-grid-r09.css"
    printf '[R09-MEMBER-CHAT-DEPLOY] AUTO_ROLLBACK=COMPLETE\n' >&2
  fi
  cleanup
  exit "${code}"
}
trap rollback_on_error ERR
trap cleanup EXIT

[ "$(id -u)" -eq 0 ] || fail 'root is required'
[ -n "${EXPECTED_ARCHIVE_SHA}" ] || fail 'expected archive SHA-256 is required'
[ -f "${ARCHIVE}" ] || fail 'overlay archive is missing'
[ -d "${SITE}/source/plugin" ] || fail 'R08 staging site is absent'
[ "$(stat -c '%a' "${PRIVATE}")" = '700' ] || fail 'R08 private directory mode is not 700'
grep -Fq 'tgb_stage_r08_main' "${SITE}/config/config_global.php" || fail 'not the R08 main database'
grep -Fq 'tgb_stage_r08_uc' "${SITE}/config/config_ucenter.php" || fail 'not the R08 UCenter database'
[ -d "${PRIVATE}/r09-member-chat-fixture-active" ] || fail 'member/chat fixture is not active'
[ -d "${SITE}/__r08_auth__" ] || fail 'authentication bridge is not active'
[ -d "${PRIVATE}/browser-origin-active" ] || fail 'browser origin bridge is not active'

actual_archive_sha="$(sha256sum "${ARCHIVE}" | awk '{print $1}')"
[ "${actual_archive_sha}" = "${EXPECTED_ARCHIVE_SHA,,}" ] || fail 'archive SHA-256 mismatch'

member_target="${SITE}/source/plugin/xigua_hb/template/touch/member_new.php"
chat_target="${SITE}/source/plugin/xigua_lt/template/touch/chat.php"
[ "$(sha256sum "${member_target}" | awk '{print $1}')" = "${EXPECTED_MEMBER_SHA}" ] || fail 'member template predeploy hash drift'
[ "$(sha256sum "${chat_target}" | awk '{print $1}')" = "${EXPECTED_CHAT_SHA}" ] || fail 'chat template predeploy hash drift'

expected_list="$(printf '%s\n' \
  'source/plugin/xigua_hb/static/tgb-r09/member-detail-light-grid-r09.css' \
  'source/plugin/xigua_hb/template/touch/member_new.php' \
  'source/plugin/xigua_lt/static/tgb-r09/chat-detail-light-grid-r09.css' \
  'source/plugin/xigua_lt/template/touch/chat.php')"
actual_list="$(tar -tzf "${ARCHIVE}" | sed 's#^\./##' | sed '/\/$/d' | sort)"
[ "${actual_list}" = "${expected_list}" ] || fail 'archive file list is not the four-file minimal overlay'
if tar -tzf "${ARCHIVE}" | grep -Eq '(^/|(^|/)\.\.(/|$))'; then
  fail 'archive contains an unsafe path'
fi

TMP="$(mktemp -d /tmp/r09-member-chat-deploy.XXXXXX)"
tar -xzf "${ARCHIVE}" -C "${TMP}"

deploy_id="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP="${BACKUP_ROOT}/${deploy_id}-r09-member-chat"
[ ! -e "${BACKUP}" ] || fail 'backup path already exists'
install -d -m 0700 "${BACKUP}/files/source/plugin/xigua_hb/template/touch" "${BACKUP}/files/source/plugin/xigua_lt/template/touch"
cp -a -- "${member_target}" "${BACKUP}/files/source/plugin/xigua_hb/template/touch/member_new.php"
cp -a -- "${chat_target}" "${BACKUP}/files/source/plugin/xigua_lt/template/touch/chat.php"

install -d -o www -g www -m 0755 \
  "${SITE}/source/plugin/xigua_hb/static/tgb-r09" \
  "${SITE}/source/plugin/xigua_lt/static/tgb-r09"
install -o www -g www -m 0644 "${TMP}/source/plugin/xigua_hb/template/touch/member_new.php" "${member_target}"
install -o www -g www -m 0644 "${TMP}/source/plugin/xigua_hb/static/tgb-r09/member-detail-light-grid-r09.css" "${SITE}/source/plugin/xigua_hb/static/tgb-r09/member-detail-light-grid-r09.css"
install -o www -g www -m 0644 "${TMP}/source/plugin/xigua_lt/template/touch/chat.php" "${chat_target}"
install -o www -g www -m 0644 "${TMP}/source/plugin/xigua_lt/static/tgb-r09/chat-detail-light-grid-r09.css" "${SITE}/source/plugin/xigua_lt/static/tgb-r09/chat-detail-light-grid-r09.css"
DEPLOYED=1

grep -Fq 'tgb-r09-member-detail-page' "${member_target}" || fail 'member scope marker missing after deploy'
grep -Fq 'tgb-r09-chat-detail-page' "${chat_target}" || fail 'chat scope marker missing after deploy'
! grep -Fq 'img.imehui.com' "${member_target}" || fail 'member template still uses external UI images'

cat >"${BACKUP}/ROLLBACK.txt" <<EOF
cp -a -- '${BACKUP}/files/.' '${SITE}/'
rm -f -- '${SITE}/source/plugin/xigua_hb/static/tgb-r09/member-detail-light-grid-r09.css' '${SITE}/source/plugin/xigua_lt/static/tgb-r09/chat-detail-light-grid-r09.css'
EOF
chmod 0400 "${BACKUP}/ROLLBACK.txt"
find "${BACKUP}" -type f -exec chmod a-w {} +

printf '[R09-MEMBER-CHAT-DEPLOY] DEPLOY_ID=%s\n' "${deploy_id}"
printf '[R09-MEMBER-CHAT-DEPLOY] BACKUP=%s\n' "${BACKUP}"
printf '[R09-MEMBER-CHAT-DEPLOY] ARCHIVE_SHA256=%s\n' "${actual_archive_sha}"
printf '[R09-MEMBER-CHAT-DEPLOY] FILES=4\n'
printf '[R09-MEMBER-CHAT-DEPLOY] RESULT=PASS\n'
