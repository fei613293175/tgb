#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
ARCHIVE_PATH="${2:-}"
EXPECTED_ARCHIVE_SHA="${3:-}"
MANIFEST_PATH="${4:-}"
ROOT="/www/wwwroot/tg.suewammes.com"
BACKUP_ROOT="/www/staging/tg-h5-ui-r08/private/production-release-backups"
EXPECTED_PRE_SHA="128aaead7304ae1aa39df5ef99a2f69d4606246c63597d1eebf047065bd44939"
EXPECTED_FILE_COUNT=79

fail() { printf '[R09-PRODUCTION] ABORT: %s\n' "$1" >&2; exit 1; }
[[ "${MODE}" == "--verify-only" || "${MODE}" == "--apply-production" ]] || fail "mode must be --verify-only or --apply-production"
[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${ROOT}/source/plugin" ] || fail "production root is invalid"
[ -f "${ARCHIVE_PATH}" ] || fail "candidate archive is absent"
[ -f "${MANIFEST_PATH}" ] || fail "candidate manifest is absent"
[[ "${EXPECTED_ARCHIVE_SHA}" =~ ^[0-9a-f]{64}$ ]] || fail "candidate SHA-256 is invalid"
[ "$(sha256sum "${ARCHIVE_PATH}" | awk '{print $1}')" = "${EXPECTED_ARCHIVE_SHA}" ] || fail "candidate archive SHA-256 mismatch"

WORK_DIR="$(mktemp -d /tmp/r09-production.XXXXXX)"
NORMALIZED_MANIFEST="${WORK_DIR}.sha256"
cleanup() { rm -rf -- "${WORK_DIR}"; rm -f -- "${NORMALIZED_MANIFEST}"; }
trap cleanup EXIT
tar -xzf "${ARCHIVE_PATH}" --strip-components=1 --no-same-owner --no-same-permissions -C "${WORK_DIR}"
sed 's/\r$//' "${MANIFEST_PATH}" >"${NORMALIZED_MANIFEST}"
mapfile -t EXPECTED_FILES < <(awk '{sub(/^[^ ]+  /, ""); print}' "${NORMALIZED_MANIFEST}" | LC_ALL=C sort)
mapfile -t ARCHIVE_FILES < <(cd "${WORK_DIR}" && find . -type f -printf '%P\n' | LC_ALL=C sort)
[ "${#EXPECTED_FILES[@]}" -eq "${EXPECTED_FILE_COUNT}" ] || fail "expected file count is ${#EXPECTED_FILES[@]}"
[ "${ARCHIVE_FILES[*]}" = "${EXPECTED_FILES[*]}" ] || fail "archive and manifest file lists differ"
(cd "${WORK_DIR}" && sha256sum -c "${NORMALIZED_MANIFEST}" >/dev/null) || fail "candidate file hash mismatch"
if grep -RIEq 'cdn\.tailwindcss|cdn\.jsdelivr|cdnjs\.cloudflare|unpkg\.com|fonts\.googleapis|use\.fontawesome' "${WORK_DIR}"; then
  fail "public UI CDN remains"
fi
for forbidden in xigua_hs xigua_sp tb_jjd tb_cus_adv tb_cus_taojing; do
  [ ! -e "${WORK_DIR}/source/plugin/${forbidden}" ] || fail "out-of-scope plugin included: ${forbidden}"
done
for relative in "${EXPECTED_FILES[@]}"; do
  [ ! -L "${WORK_DIR}/${relative}" ] || fail "symbolic links are forbidden"
  case "${relative}" in *.php) php -l "${WORK_DIR}/${relative}" >/dev/null || fail "PHP lint failed: ${relative}" ;; esac
done

if [ "${MODE}" = "--verify-only" ]; then
  printf '[R09-PRODUCTION] VERIFY PASS FILES=%s ARCHIVE_SHA256=%s\n' "${EXPECTED_FILE_COUNT}" "${EXPECTED_ARCHIVE_SHA}"
  exit 0
fi

PRE_MANIFEST="${WORK_DIR}/production-before.sha256"
find "${ROOT}" -xdev -type f \
  ! -path "${ROOT}/operation.log" \
  ! -path "${ROOT}/data/attachment/*" \
  ! -path "${ROOT}/data/cache/*" \
  ! -path "${ROOT}/data/log/*" \
  ! -path "${ROOT}/data/sysdata/*" \
  ! -path "${ROOT}/data/template/*" \
  ! -path "${ROOT}/source/plugin/xigua_hb/pics/*" \
  ! -path "${ROOT}/uc_server/data/avatar/*" \
  ! -path "${ROOT}/uc_server/data/cache/*" \
  ! -path "${ROOT}/uc_server/data/logs/*" \
  -print0 | sort -z | xargs -0 sha256sum >"${PRE_MANIFEST}"
CURRENT_PRE_SHA="$(sha256sum "${PRE_MANIFEST}" | awk '{print $1}')"
[ "${CURRENT_PRE_SHA}" = "${EXPECTED_PRE_SHA}" ] || fail "production pre-deploy hash changed"

DEPLOY_ID="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP_DIR="${BACKUP_ROOT}/${DEPLOY_ID}-owner-repair"
mkdir -p "${BACKUP_DIR}"
printf 'deploy_id=%s\narchive_sha256=%s\npre_production_sha=%s\n' "${DEPLOY_ID}" "${EXPECTED_ARCHIVE_SHA}" "${EXPECTED_PRE_SHA}" >"${BACKUP_DIR}/DEPLOYMENT.env"
cp "${NORMALIZED_MANIFEST}" "${BACKUP_DIR}/CANDIDATE_SHA256.txt"
for relative in "${EXPECTED_FILES[@]}"; do
  target="${ROOT}/${relative}"
  if [ -e "${target}" ]; then
    mkdir -p "${BACKUP_DIR}/files/$(dirname "${relative}")"
    cp -a -- "${target}" "${BACKUP_DIR}/files/${relative}"
    printf '%s  %s\n' "$(sha256sum "${target}" | awk '{print $1}')" "${relative}" >>"${BACKUP_DIR}/BEFORE_SHA256.txt"
  else
    printf '%s\n' "${relative}" >>"${BACKUP_DIR}/BEFORE_ABSENT.txt"
  fi
done

for relative in "${EXPECTED_FILES[@]}"; do
  target="${ROOT}/${relative}"
  install -d -o www -g www -m 0755 "$(dirname "${target}")"
  install -o www -g www -m 0644 "${WORK_DIR}/${relative}" "${target}"
done
(cd "${ROOT}" && sha256sum -c "${NORMALIZED_MANIFEST}" >/dev/null) || fail "installed production files differ from candidate"
find "${ROOT}/data/template" -mindepth 1 -maxdepth 1 -type f -delete

HOME_CODE="$(curl -sS -L --max-redirs 5 -o "${WORK_DIR}/home.html" -w '%{http_code}' -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' 'https://tg.suewammes.com/plugin.php?id=xigua_hb&mobile=2')"
APP_CODE="$(curl -sS -o "${WORK_DIR}/app.html" -w '%{http_code}' 'https://tg.suewammes.com/done/app.html')"
[ "${HOME_CODE}" = "200" ] || fail "production home HTTP ${HOME_CODE}"
[ "${APP_CODE}" = "200" ] || fail "production App landing HTTP ${APP_CODE}"
chmod -R a-w "${BACKUP_DIR}"

printf '[R09-PRODUCTION] APPLY PASS DEPLOY_ID=%s FILES=%s HOME=%s APP=%s\n' "${DEPLOY_ID}" "${EXPECTED_FILE_COUNT}" "${HOME_CODE}" "${APP_CODE}"
printf '[R09-PRODUCTION] BACKUP_DIR=%s\n' "${BACKUP_DIR}"
