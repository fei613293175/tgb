#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

ARCHIVE_PATH="${1:-}"
EXPECTED_ARCHIVE_SHA="${2:-}"
MANIFEST_PATH="${3:-}"
ROOT="/www/staging/tg-h5-ui-r08/site"
PRIVATE="/www/staging/tg-h5-ui-r08/private"
HOST="tg-h5-ui-r08.local"
PORT="18088"

fail() { printf '[R09-STAGING] ABORT: %s\n' "$1" >&2; exit 1; }
[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${ROOT}/source/plugin" ] || fail "R08 staging root is invalid"
[ -f "${ARCHIVE_PATH}" ] || fail "candidate archive is absent"
[ -f "${MANIFEST_PATH}" ] || fail "candidate manifest is absent"
[[ "${EXPECTED_ARCHIVE_SHA}" =~ ^[0-9a-f]{64}$ ]] || fail "candidate SHA-256 is invalid"
[ "$(sha256sum "${ARCHIVE_PATH}" | awk '{print $1}')" = "${EXPECTED_ARCHIVE_SHA}" ] || fail "candidate archive SHA-256 mismatch"
PRE_POST="$(curl -sS -o /dev/null -w '%{http_code}' -X POST -H "Host: ${HOST}" "http://127.0.0.1:${PORT}/")"
[ "${PRE_POST}" = "405" ] || fail "pre-deploy POST guard is ${PRE_POST}"

WORK_DIR="$(mktemp -d "${PRIVATE}/r09-candidate.XXXXXX")"
NORMALIZED_MANIFEST="${WORK_DIR}.sha256"
cleanup() { rm -rf -- "${WORK_DIR}"; rm -f -- "${NORMALIZED_MANIFEST}"; }
trap cleanup EXIT
tar -xzf "${ARCHIVE_PATH}" --strip-components=1 --no-same-owner --no-same-permissions -C "${WORK_DIR}"
sed 's/\r$//' "${MANIFEST_PATH}" >"${NORMALIZED_MANIFEST}"
mapfile -t EXPECTED_FILES < <(awk '{sub(/^[^ ]+  /, ""); print}' "${NORMALIZED_MANIFEST}" | LC_ALL=C sort)
mapfile -t ARCHIVE_FILES < <(cd "${WORK_DIR}" && find . -type f -printf '%P\n' | LC_ALL=C sort)
[ "${#EXPECTED_FILES[@]}" -eq 81 ] || fail "expected file count is ${#EXPECTED_FILES[@]}"
[ "${ARCHIVE_FILES[*]}" = "${EXPECTED_FILES[*]}" ] || fail "archive and manifest file lists differ"
(cd "${WORK_DIR}" && sha256sum -c "${NORMALIZED_MANIFEST}" >/dev/null) || fail "candidate file hash mismatch"
for relative in "${EXPECTED_FILES[@]}"; do
  [ ! -L "${WORK_DIR}/${relative}" ] || fail "symbolic links are forbidden"
  case "${relative}" in *.php) php -l "${WORK_DIR}/${relative}" >/dev/null || fail "PHP lint failed: ${relative}" ;; esac
done

DEPLOY_ID="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP_DIR="${PRIVATE}/change-backups/${DEPLOY_ID}-r09-production-candidate"
mkdir -p "${BACKUP_DIR}/files"
printf 'deploy_id=%s\narchive_sha256=%s\nfiles=81\n' "${DEPLOY_ID}" "${EXPECTED_ARCHIVE_SHA}" >"${BACKUP_DIR}/DEPLOYMENT.env"
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
(cd "${ROOT}" && sha256sum -c "${NORMALIZED_MANIFEST}" >/dev/null) || fail "installed staging files differ from candidate"
find "${ROOT}/data/template" -mindepth 1 -maxdepth 1 -type f -delete

HOME_CODE="$(curl -sS -L --max-redirs 5 -o "${WORK_DIR}/home.html" -w '%{http_code}' -H "Host: ${HOST}" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' "http://127.0.0.1:${PORT}/plugin.php?id=xigua_hb&mobile=2")"
APP_CODE="$(curl -sS -o "${WORK_DIR}/app.html" -w '%{http_code}' -H "Host: ${HOST}" "http://127.0.0.1:${PORT}/done/app.html")"
POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' -X POST -H "Host: ${HOST}" "http://127.0.0.1:${PORT}/")"
[ "${HOME_CODE}" = "200" ] || fail "staging home HTTP ${HOME_CODE}"
[ "${APP_CODE}" = "200" ] || fail "staging App landing HTTP ${APP_CODE}"
[ "${POST_CODE}" = "405" ] || fail "post-deploy POST guard is ${POST_CODE}"
chmod -R a-w "${BACKUP_DIR}"

printf '[R09-STAGING] PASS DEPLOY_ID=%s FILES=81 HOME=%s APP=%s POST=%s\n' "${DEPLOY_ID}" "${HOME_CODE}" "${APP_CODE}" "${POST_CODE}"
printf '[R09-STAGING] BACKUP_DIR=%s\n' "${BACKUP_DIR}"
