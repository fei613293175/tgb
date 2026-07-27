#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
ARCHIVE="${2:-/tmp/r09-member-chat-overlay.tar.gz}"
EXPECTED_ARCHIVE_SHA="${3:-}"
ROLLBACK_ID="${4:-}"
ROOT="/www/wwwroot/tg.suewammes.com"
BACKUP_ROOT="/www/staging/tg-h5-ui-r08/private/production-member-chat-backups"
EXPECTED_MEMBER_SHA="e787a81ab9306a0dc5d4b97e82de585d37f71831bec8ae31603eb0e5c41afbf8"
EXPECTED_MEMBER_HEADER_SHA="209171c81201545ef8ce680b255c4e8e36beae56c6328c2dee73b3b68f8e8d3a"
EXPECTED_CHAT_SHA="b0e370ebcb8aee006c88e4c26dbb6a1ad57693fd9a245303a20181b51f8857bb"
TMP=""

fail() { printf '[R09-MEMBER-CHAT-PRODUCTION] ABORT: %s\n' "$1" >&2; exit 1; }
hash_file() { sha256sum "$1" | awk '{print $1}'; }
cleanup() { [ -z "${TMP}" ] || rm -rf -- "${TMP}"; }
trap cleanup EXIT

member_target="${ROOT}/source/plugin/xigua_hb/template/touch/member_new.php"
member_header_target="${ROOT}/source/plugin/xigua_hb/template/touch/wdk_header.php"
chat_target="${ROOT}/source/plugin/xigua_lt/template/touch/chat.php"
member_css_target="${ROOT}/source/plugin/xigua_hb/static/tgb-r09/member-detail-light-grid-r09.css"
chat_css_target="${ROOT}/source/plugin/xigua_lt/static/tgb-r09/chat-detail-light-grid-r09.css"

verify_baseline() {
  [ "$(hash_file "${member_target}")" = "${EXPECTED_MEMBER_SHA}" ] || fail 'member template baseline drift'
  [ "$(hash_file "${member_header_target}")" = "${EXPECTED_MEMBER_HEADER_SHA}" ] || fail 'member header baseline drift'
  [ "$(hash_file "${chat_target}")" = "${EXPECTED_CHAT_SHA}" ] || fail 'chat template baseline drift'
  [ ! -e "${member_css_target}" ] || fail 'member CSS already exists'
  [ ! -e "${chat_css_target}" ] || fail 'chat CSS already exists'
}

prepare_archive() {
  [ -n "${EXPECTED_ARCHIVE_SHA}" ] || fail 'expected archive SHA-256 is required'
  [[ "${EXPECTED_ARCHIVE_SHA}" =~ ^[0-9a-f]{64}$ ]] || fail 'expected archive SHA-256 is invalid'
  [ -f "${ARCHIVE}" ] || fail 'overlay archive is missing'
  [ "$(hash_file "${ARCHIVE}")" = "${EXPECTED_ARCHIVE_SHA}" ] || fail 'archive SHA-256 mismatch'
  expected_list="$(printf '%s\n' \
    'source/plugin/xigua_hb/static/tgb-r09/member-detail-light-grid-r09.css' \
    'source/plugin/xigua_hb/template/touch/member_new.php' \
    'source/plugin/xigua_hb/template/touch/wdk_header.php' \
    'source/plugin/xigua_lt/static/tgb-r09/chat-detail-light-grid-r09.css' \
    'source/plugin/xigua_lt/template/touch/chat.php')"
  actual_list="$(tar -tzf "${ARCHIVE}" | sed 's#^\./##' | sed '/\/$/d' | LC_ALL=C sort)"
  [ "${actual_list}" = "${expected_list}" ] || fail 'archive file list is not the five-file minimal overlay'
  ! tar -tzf "${ARCHIVE}" | grep -Eq '(^/|(^|/)\.\.(/|$))' || fail 'archive contains an unsafe path'
  TMP="$(mktemp -d /tmp/r09-member-chat-production.XXXXXX)"
  tar -xzf "${ARCHIVE}" --no-same-owner --no-same-permissions -C "${TMP}"
  ! grep -RIEq 'cdn\.tailwindcss|cdn\.jsdelivr|cdnjs\.cloudflare|unpkg\.com|fonts\.googleapis|use\.fontawesome|img\.imehui\.com' "${TMP}" || fail 'public UI dependency remains'
}

[ "$(id -u)" -eq 0 ] || fail 'root is required'
[ -d "${ROOT}/source/plugin" ] || fail 'production root is invalid'

case "${MODE}" in
  --verify-only)
    verify_baseline
    prepare_archive
    printf '[R09-MEMBER-CHAT-PRODUCTION] VERIFY=PASS FILES=5 ARCHIVE_SHA256=%s\n' "${EXPECTED_ARCHIVE_SHA}"
    ;;
  --apply-production)
    verify_baseline
    prepare_archive
    deploy_id="$(date '+%Y%m%dT%H%M%S%z')"
    backup="${BACKUP_ROOT}/${deploy_id}"
    [ ! -e "${backup}" ] || fail 'backup path already exists'
    install -d -m 0700 \
      "${backup}/files/source/plugin/xigua_hb/template/touch" \
      "${backup}/files/source/plugin/xigua_lt/template/touch"
    cp -a -- "${member_target}" "${backup}/files/source/plugin/xigua_hb/template/touch/member_new.php"
    cp -a -- "${member_header_target}" "${backup}/files/source/plugin/xigua_hb/template/touch/wdk_header.php"
    cp -a -- "${chat_target}" "${backup}/files/source/plugin/xigua_lt/template/touch/chat.php"
    printf '%s\n' \
      'source/plugin/xigua_hb/static/tgb-r09/member-detail-light-grid-r09.css' \
      'source/plugin/xigua_lt/static/tgb-r09/chat-detail-light-grid-r09.css' >"${backup}/BEFORE_ABSENT.txt"
    printf 'deploy_id=%s\narchive_sha256=%s\nfiles=5\n' "${deploy_id}" "${EXPECTED_ARCHIVE_SHA}" >"${backup}/DEPLOYMENT.env"

    install -d -o www -g www -m 0755 \
      "$(dirname "${member_target}")" "$(dirname "${chat_target}")" \
      "$(dirname "${member_css_target}")" "$(dirname "${chat_css_target}")"
    install -o www -g www -m 0644 "${TMP}/source/plugin/xigua_hb/template/touch/member_new.php" "${member_target}"
    install -o www -g www -m 0644 "${TMP}/source/plugin/xigua_hb/template/touch/wdk_header.php" "${member_header_target}"
    install -o www -g www -m 0644 "${TMP}/source/plugin/xigua_lt/template/touch/chat.php" "${chat_target}"
    install -o www -g www -m 0644 "${TMP}/source/plugin/xigua_hb/static/tgb-r09/member-detail-light-grid-r09.css" "${member_css_target}"
    install -o www -g www -m 0644 "${TMP}/source/plugin/xigua_lt/static/tgb-r09/chat-detail-light-grid-r09.css" "${chat_css_target}"

    for relative in \
      source/plugin/xigua_hb/template/touch/member_new.php \
      source/plugin/xigua_hb/template/touch/wdk_header.php \
      source/plugin/xigua_lt/template/touch/chat.php \
      source/plugin/xigua_hb/static/tgb-r09/member-detail-light-grid-r09.css \
      source/plugin/xigua_lt/static/tgb-r09/chat-detail-light-grid-r09.css; do
      [ "$(hash_file "${ROOT}/${relative}")" = "$(hash_file "${TMP}/${relative}")" ] || fail "installed hash mismatch: ${relative}"
      runuser -u www -- test -r "${ROOT}/${relative}" || fail "PHP-FPM cannot read: ${relative}"
    done
    find "${ROOT}/data/template" -mindepth 1 -maxdepth 1 -type f -delete
    home_code="$(curl -sS -L --max-redirs 5 -o /dev/null -w '%{http_code}' -A 'TuiGuangBaoAndroid/1.0.0 Android' 'https://tg.suewammes.com/plugin.php?id=xigua_hb&mobile=2')"
    [ "${home_code}" = '200' ] || fail "production home HTTP ${home_code}"
    chmod -R a-w "${backup}"
    printf '[R09-MEMBER-CHAT-PRODUCTION] APPLY=PASS DEPLOY_ID=%s BACKUP=%s HOME=%s\n' "${deploy_id}" "${backup}" "${home_code}"
    ;;
  --apply-rollback)
    [[ "${ROLLBACK_ID}" =~ ^[0-9]{8}T[0-9]{6}[+-][0-9]{4}$ ]] || fail 'rollback id is invalid'
    backup="${BACKUP_ROOT}/${ROLLBACK_ID}"
    [ -f "${backup}/DEPLOYMENT.env" ] || fail 'rollback backup is absent'
    install -d -o www -g www -m 0755 "$(dirname "${member_target}")" "$(dirname "${chat_target}")"
    install -o www -g www -m 0644 "${backup}/files/source/plugin/xigua_hb/template/touch/member_new.php" "${member_target}"
    install -o www -g www -m 0644 "${backup}/files/source/plugin/xigua_hb/template/touch/wdk_header.php" "${member_header_target}"
    install -o www -g www -m 0644 "${backup}/files/source/plugin/xigua_lt/template/touch/chat.php" "${chat_target}"
    rm -f -- "${member_css_target}" "${chat_css_target}"
    find "${ROOT}/data/template" -mindepth 1 -maxdepth 1 -type f -delete
    verify_baseline
    printf '[R09-MEMBER-CHAT-PRODUCTION] ROLLBACK=PASS ID=%s\n' "${ROLLBACK_ID}"
    ;;
  *)
    fail 'usage: --verify-only|--apply-production <archive> <sha256> or --apply-rollback _ _ <deploy-id>'
    ;;
esac
