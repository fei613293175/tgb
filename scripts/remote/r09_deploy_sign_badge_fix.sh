#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

ACTION="${1:-}"
ARCHIVE_PATH="${2:-}"
EXPECTED_ARCHIVE_SHA="${3:-}"
BACKUP_ID="${4:-}"
STAGING_SITE="/www/staging/tg-h5-ui-r08/site"
PRODUCTION_SITE="/www/wwwroot/tg.suewammes.com"
PRIVATE_ROOT="/www/staging/tg-h5-ui-r08/private"
TEMPLATE="source/plugin/view/module/site/sign.php"
CSS="source/plugin/view/static/tgb-r08/sign-light-grid-r08.css"
OLD_TEMPLATE_SHA="6c6301ddbc9911852b4a9fe0781fa1db3e25c206c949cee9135eab58bcd404a1"
OLD_CSS_SHA="4171a91d98ae56c1fdb9fdda11d2e495ceb1506b88f3b084ed696cf5e23ef391"
NEW_TEMPLATE_SHA="6d449f88dc76ac8fc551f093d7a27da2a7f95d80b6bc47826c6d6f1924e67bd1"
INTERMEDIATE_STAGING_CSS_SHA="17b21f6908ebb2399467675c22d9c002fb63fc39e16c510ec6b6b7bfcc66b7c2"
NEW_CSS_SHA="53ff002b41387f2237d1d859039b3d30cbaddbbaf3f81c38da77de26d6fccda1"

fail() { printf '[R09-SIGN-BADGE] ABORT: %s\n' "$1" >&2; exit 1; }
hash_of() { sha256sum "$1" | awk '{print $1}'; }
assert_hash() { [ "$(hash_of "$1")" = "$2" ] || fail "hash mismatch: $1"; }

[ "$(id -u)" -eq 0 ] || fail "root is required"

case "${ACTION}" in
  deploy-staging|verify-staging|rollback-staging)
    SITE="${STAGING_SITE}"
    SCOPE="staging"
    PRE_TEMPLATE_SHA="${NEW_TEMPLATE_SHA}"
    PRE_CSS_SHA="${INTERMEDIATE_STAGING_CSS_SHA}"
    ;;
  deploy-production|verify-production|rollback-production)
    SITE="${PRODUCTION_SITE}"
    SCOPE="production"
    PRE_TEMPLATE_SHA="${OLD_TEMPLATE_SHA}"
    PRE_CSS_SHA="${OLD_CSS_SHA}"
    ;;
  *) fail "usage: ACTION ARCHIVE EXPECTED_ARCHIVE_SHA [BACKUP_ID]" ;;
esac

[ -d "${SITE}/source/plugin" ] || fail "site root is absent"

verify_deployed() {
  assert_hash "${SITE}/${TEMPLATE}" "${NEW_TEMPLATE_SHA}"
  assert_hash "${SITE}/${CSS}" "${NEW_CSS_SHA}"
  runuser -u www -- test -r "${SITE}/${TEMPLATE}" || fail "template is not PHP-FPM readable"
  runuser -u www -- test -r "${SITE}/${CSS}" || fail "CSS is not PHP-FPM readable"
  grep -Fq 'sign-light-grid-r08.css?v=20260727-r09-1' "${SITE}/${TEMPLATE}" || fail "cache key missing"
  grep -Fq '.tgb-r08-sign-page .promo-highlight::after' "${SITE}/${CSS}" || fail "badge selector missing"
  grep -Fq 'right: 4px !important' "${SITE}/${CSS}" || fail "badge right bound missing"
  grep -Fq 'animation: none !important' "${SITE}/${CSS}" || fail "badge animation override missing"
  grep -Fq '.tgb-r08-sign-page #noticeModal .modal-box' "${SITE}/${CSS}" || fail "notice viewport bound missing"
  grep -Fq '.tgb-r08-sign-page #noticeModal .modal-close::before' "${SITE}/${CSS}" || fail "notice close glyph missing"
  php -l "${SITE}/${TEMPLATE}" >/dev/null || fail "deployed PHP lint failed"
  if [ "${SCOPE}" = "staging" ]; then
    css_code="$(curl -sS -o /dev/null -w '%{http_code}' -H 'Host: tg-h5-ui-r08.local' 'http://127.0.0.1:18088/source/plugin/view/static/tgb-r08/sign-light-grid-r08.css?v=20260727-r09-1')"
    post_code="$(curl -sS -o /dev/null -w '%{http_code}' -X POST -H 'Host: tg-h5-ui-r08.local' 'http://127.0.0.1:18088/')"
    [ "${post_code}" = "405" ] || fail "staging POST guard is ${post_code}"
  else
    css_code="$(curl -sS -o /dev/null -w '%{http_code}' 'https://tg.suewammes.com/source/plugin/view/static/tgb-r08/sign-light-grid-r08.css?v=20260727-r09-1')"
    post_code="NOT_SENT"
  fi
  [ "${css_code}" = "200" ] || fail "CSS HTTP ${css_code}"
  printf '[R09-SIGN-BADGE] VERIFY=PASS scope=%s CSS_HTTP=%s POST=%s\n' "${SCOPE}" "${css_code}" "${post_code}"
}

case "${ACTION}" in
  verify-*)
    verify_deployed
    exit 0
    ;;
  rollback-*)
    [[ "${BACKUP_ID}" =~ ^[0-9]{8}T[0-9]{6}[+-][0-9]{4}$ ]] || fail "invalid backup ID"
    BACKUP_DIR="${PRIVATE_ROOT}/${SCOPE}-sign-badge-backups/${BACKUP_ID}"
    [ -d "${BACKUP_DIR}" ] || fail "backup is absent"
    assert_hash "${SITE}/${TEMPLATE}" "${NEW_TEMPLATE_SHA}"
    assert_hash "${SITE}/${CSS}" "${NEW_CSS_SHA}"
    assert_hash "${BACKUP_DIR}/${TEMPLATE}" "${PRE_TEMPLATE_SHA}"
    assert_hash "${BACKUP_DIR}/${CSS}" "${PRE_CSS_SHA}"
    install -o www -g www -m 0644 "${BACKUP_DIR}/${TEMPLATE}" "${SITE}/${TEMPLATE}"
    install -o www -g www -m 0644 "${BACKUP_DIR}/${CSS}" "${SITE}/${CSS}"
    assert_hash "${SITE}/${TEMPLATE}" "${PRE_TEMPLATE_SHA}"
    assert_hash "${SITE}/${CSS}" "${PRE_CSS_SHA}"
    printf '[R09-SIGN-BADGE] ROLLBACK=PASS scope=%s backup=%s\n' "${SCOPE}" "${BACKUP_DIR}"
    exit 0
    ;;
esac

[ -f "${ARCHIVE_PATH}" ] || fail "candidate archive is absent"
[[ "${EXPECTED_ARCHIVE_SHA}" =~ ^[0-9a-f]{64}$ ]] || fail "archive SHA is invalid"
assert_hash "${ARCHIVE_PATH}" "${EXPECTED_ARCHIVE_SHA}"
assert_hash "${SITE}/${TEMPLATE}" "${PRE_TEMPLATE_SHA}"
assert_hash "${SITE}/${CSS}" "${PRE_CSS_SHA}"

if [ "${ACTION}" = "deploy-production" ]; then
  assert_hash "${STAGING_SITE}/${TEMPLATE}" "${NEW_TEMPLATE_SHA}"
  assert_hash "${STAGING_SITE}/${CSS}" "${NEW_CSS_SHA}"
fi

WORK_DIR="$(mktemp -d "${PRIVATE_ROOT}/r09-sign-badge.XXXXXX")"
cleanup() { rm -rf -- "${WORK_DIR}"; }
trap cleanup EXIT
tar -xzf "${ARCHIVE_PATH}" -C "${WORK_DIR}"
mapfile -t ROOTS < <(find "${WORK_DIR}" -mindepth 1 -maxdepth 1 -type d)
[ "${#ROOTS[@]}" -eq 1 ] || fail "candidate archive root is ambiguous"
CANDIDATE_ROOT="${ROOTS[0]}"
assert_hash "${CANDIDATE_ROOT}/${TEMPLATE}" "${NEW_TEMPLATE_SHA}"
assert_hash "${CANDIDATE_ROOT}/${CSS}" "${NEW_CSS_SHA}"
php -l "${CANDIDATE_ROOT}/${TEMPLATE}" >/dev/null || fail "candidate PHP lint failed"

DEPLOY_ID="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP_DIR="${PRIVATE_ROOT}/${SCOPE}-sign-badge-backups/${DEPLOY_ID}"
install -d -m 0700 "${BACKUP_DIR}/$(dirname "${TEMPLATE}")" "${BACKUP_DIR}/$(dirname "${CSS}")"
cp -- "${SITE}/${TEMPLATE}" "${BACKUP_DIR}/${TEMPLATE}"
cp -- "${SITE}/${CSS}" "${BACKUP_DIR}/${CSS}"
printf 'deploy_id=%s\nscope=%s\narchive_sha256=%s\n' "${DEPLOY_ID}" "${SCOPE}" "${EXPECTED_ARCHIVE_SHA}" >"${BACKUP_DIR}/DEPLOYMENT.env"
assert_hash "${BACKUP_DIR}/${TEMPLATE}" "${PRE_TEMPLATE_SHA}"
assert_hash "${BACKUP_DIR}/${CSS}" "${PRE_CSS_SHA}"

install -o www -g www -m 0644 "${CANDIDATE_ROOT}/${TEMPLATE}" "${SITE}/${TEMPLATE}"
install -o www -g www -m 0644 "${CANDIDATE_ROOT}/${CSS}" "${SITE}/${CSS}"
verify_deployed
chmod -R a-w "${BACKUP_DIR}"
printf '[R09-SIGN-BADGE] DEPLOY=PASS scope=%s deploy_id=%s backup=%s\n' "${SCOPE}" "${DEPLOY_ID}" "${BACKUP_DIR}"
