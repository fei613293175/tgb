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
TEMPLATE="source/plugin/xigua_hb/template/touch/pub.php"
OLD_TEMPLATE_SHA="d78f501a2a107fc9060a9ae90d7c07e63858b45ba0d8c38677224c2b2265bb75"
NEW_TEMPLATE_SHA="d959be90891cda64663e0fd1e70d31ec305cabfab45ce6ceb34ea8d96878cec9"

fail() { printf '[R09-PUBLISH-VISUAL] ABORT: %s\n' "$1" >&2; exit 1; }
hash_of() { sha256sum "$1" | awk '{print $1}'; }
assert_hash() { [ "$(hash_of "$1")" = "$2" ] || fail "hash mismatch: $1"; }

[ "$(id -u)" -eq 0 ] || fail 'root is required'
case "${ACTION}" in
  deploy-staging|verify-staging|rollback-staging) SITE="${STAGING_SITE}"; SCOPE="staging" ;;
  deploy-production|verify-production|rollback-production) SITE="${PRODUCTION_SITE}"; SCOPE="production" ;;
  *) fail 'usage: ACTION ARCHIVE EXPECTED_ARCHIVE_SHA [BACKUP_ID]' ;;
esac

verify_deployed() {
  assert_hash "${SITE}/${TEMPLATE}" "${NEW_TEMPLATE_SHA}"
  runuser -u www -- test -r "${SITE}/${TEMPLATE}" || fail 'template is not PHP-FPM readable'
  php -l "${SITE}/${TEMPLATE}" >/dev/null || fail 'PHP lint failed'
  grep -Fq 'TGB-R09-PUBLISH-VISUAL-FIX:START' "${SITE}/${TEMPLATE}" || fail 'visual fix marker missing'
  grep -Fq 'width:64px!important;' "${SITE}/${TEMPLATE}" || fail 'publish button width contract missing'
  grep -Fq '.tgb-publish-form-spacer' "${SITE}/${TEMPLATE}" || fail 'form spacer contract missing'
  if [ "${SCOPE}" = 'staging' ]; then
    page_code="$(curl -sSL -o /dev/null -w '%{http_code}' -H 'Host: tg-h5-ui-r08.local' -H 'User-Agent: Mozilla/5.0 (Linux; Android 15; Pixel 8) AppleWebKit/537.36 Chrome/138.0 Mobile Safari/537.36 TuiGuangBaoAndroid/1.0.0' 'http://127.0.0.1:18088/plugin.php?id=xigua_hb&ac=pub&step=3&catid=31&mobile=2')"
    post_code="$(curl -sS -o /dev/null -w '%{http_code}' -X POST -H 'Host: tg-h5-ui-r08.local' 'http://127.0.0.1:18088/')"
    [ "${post_code}" = '405' ] || fail "staging POST guard is ${post_code}"
  else
    page_code="$(curl -sSL -o /dev/null -w '%{http_code}' -H 'User-Agent: Mozilla/5.0 (Linux; Android 15; Pixel 8) AppleWebKit/537.36 Chrome/138.0 Mobile Safari/537.36 TuiGuangBaoAndroid/1.0.0' 'https://tg.suewammes.com/plugin.php?id=xigua_hb&ac=pub&step=3&catid=31&mobile=2')"
    post_code='NOT_SENT'
  fi
  [ "${page_code}" = '200' ] || fail "publish page HTTP ${page_code}"
  printf '[R09-PUBLISH-VISUAL] VERIFY=PASS scope=%s HTTP=%s POST=%s\n' "${SCOPE}" "${page_code}" "${post_code}"
}

case "${ACTION}" in
  verify-*) verify_deployed; exit 0 ;;
  rollback-*)
    [[ "${BACKUP_ID}" =~ ^[0-9]{8}T[0-9]{6}[+-][0-9]{4}$ ]] || fail 'invalid backup ID'
    BACKUP_DIR="${PRIVATE_ROOT}/${SCOPE}-publish-visual-backups/${BACKUP_ID}"
    [ -d "${BACKUP_DIR}" ] || fail 'backup is absent'
    assert_hash "${SITE}/${TEMPLATE}" "${NEW_TEMPLATE_SHA}"
    assert_hash "${BACKUP_DIR}/${TEMPLATE}" "${OLD_TEMPLATE_SHA}"
    install -o www -g www -m 0644 "${BACKUP_DIR}/${TEMPLATE}" "${SITE}/${TEMPLATE}"
    assert_hash "${SITE}/${TEMPLATE}" "${OLD_TEMPLATE_SHA}"
    printf '[R09-PUBLISH-VISUAL] ROLLBACK=PASS scope=%s backup=%s\n' "${SCOPE}" "${BACKUP_DIR}"
    exit 0
    ;;
esac

[ -f "${ARCHIVE_PATH}" ] || fail 'candidate archive is absent'
[[ "${EXPECTED_ARCHIVE_SHA}" =~ ^[0-9a-f]{64}$ ]] || fail 'archive SHA is invalid'
assert_hash "${ARCHIVE_PATH}" "${EXPECTED_ARCHIVE_SHA}"
assert_hash "${SITE}/${TEMPLATE}" "${OLD_TEMPLATE_SHA}"
if [ "${ACTION}" = 'deploy-production' ]; then
  assert_hash "${STAGING_SITE}/${TEMPLATE}" "${NEW_TEMPLATE_SHA}"
fi

WORK_DIR="$(mktemp -d "${PRIVATE_ROOT}/r09-publish-visual.XXXXXX")"
cleanup() { rm -rf -- "${WORK_DIR}"; }
trap cleanup EXIT
tar -xzf "${ARCHIVE_PATH}" -C "${WORK_DIR}"
mapfile -t ROOTS < <(find "${WORK_DIR}" -mindepth 1 -maxdepth 1 -type d)
[ "${#ROOTS[@]}" -eq 1 ] || fail 'candidate archive root is ambiguous'
CANDIDATE_ROOT="${ROOTS[0]}"
assert_hash "${CANDIDATE_ROOT}/${TEMPLATE}" "${NEW_TEMPLATE_SHA}"
php -l "${CANDIDATE_ROOT}/${TEMPLATE}" >/dev/null || fail 'candidate PHP lint failed'

DEPLOY_ID="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP_DIR="${PRIVATE_ROOT}/${SCOPE}-publish-visual-backups/${DEPLOY_ID}"
install -d -m 0700 "${BACKUP_DIR}/$(dirname "${TEMPLATE}")"
cp -- "${SITE}/${TEMPLATE}" "${BACKUP_DIR}/${TEMPLATE}"
printf 'deploy_id=%s\nscope=%s\narchive_sha256=%s\n' "${DEPLOY_ID}" "${SCOPE}" "${EXPECTED_ARCHIVE_SHA}" >"${BACKUP_DIR}/DEPLOYMENT.env"
assert_hash "${BACKUP_DIR}/${TEMPLATE}" "${OLD_TEMPLATE_SHA}"
install -o www -g www -m 0644 "${CANDIDATE_ROOT}/${TEMPLATE}" "${SITE}/${TEMPLATE}"
verify_deployed
chmod -R a-w "${BACKUP_DIR}"
printf '[R09-PUBLISH-VISUAL] DEPLOY=PASS scope=%s deploy_id=%s backup=%s\n' "${SCOPE}" "${DEPLOY_ID}" "${BACKUP_DIR}"
