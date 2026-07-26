#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

STAGING_BASE="/www/staging/tg-h5-ui-r02"
STAGING_SITE="${STAGING_BASE}/site"
STAGING_PRIVATE="${STAGING_BASE}/private"
LOOPBACK_PORT="18082"
ARCHIVE_PATH="${1:-}"
EXPECTED_SHA256="${2:-}"

EXPECTED_FILES=(
  "index.php"
  "source/plugin/xigua_hb/static/tgb-r02/brand-mark-r02.svg"
  "source/plugin/xigua_hb/static/tgb-r02/chat-r02.svg"
  "source/plugin/xigua_hb/static/tgb-r02/light-grid-r02.css"
  "source/plugin/xigua_hb/template/touch/common_header.php"
  "source/plugin/xigua_hb/template/touch/common_nav.php"
)

fail() {
  printf '[R02-DEPLOY] ABORT: %s\n' "$1" >&2
  exit 1
}

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${STAGING_SITE}/source/plugin/xigua_hb" ] || fail "R02 staging site is absent"
[ -f "${ARCHIVE_PATH}" ] || fail "overlay archive is absent"
[[ "${EXPECTED_SHA256}" =~ ^[0-9a-f]{64}$ ]] || fail "expected SHA-256 is invalid"

ACTUAL_SHA256="$(sha256sum "${ARCHIVE_PATH}" | awk '{print $1}')"
[ "${ACTUAL_SHA256}" = "${EXPECTED_SHA256}" ] || fail "overlay archive SHA-256 mismatch"

WORK_DIR="$(mktemp -d "${STAGING_PRIVATE}/r02-deploy-work.XXXXXX")"
cleanup() {
  rm -rf -- "${WORK_DIR}"
}
trap cleanup EXIT

tar -xzf "${ARCHIVE_PATH}" -C "${WORK_DIR}"

mapfile -t ARCHIVE_FILES < <(
  cd "${WORK_DIR}"
  find . -type f -printf '%P\n' | LC_ALL=C sort
)
mapfile -t ALLOWED_FILES < <(printf '%s\n' "${EXPECTED_FILES[@]}" | LC_ALL=C sort)
[ "${ARCHIVE_FILES[*]}" = "${ALLOWED_FILES[*]}" ] || fail "archive contains an unexpected or missing file"

for relative_path in "${EXPECTED_FILES[@]}"; do
  [ -f "${WORK_DIR}/${relative_path}" ] || fail "missing ${relative_path}"
  [ ! -L "${WORK_DIR}/${relative_path}" ] || fail "symbolic links are not allowed"
done

php -l "${WORK_DIR}/index.php" >/dev/null
php -l "${WORK_DIR}/source/plugin/xigua_hb/template/touch/common_header.php" >/dev/null
php -l "${WORK_DIR}/source/plugin/xigua_hb/template/touch/common_nav.php" >/dev/null
grep -Fq 'tgb-light-grid' "${WORK_DIR}/source/plugin/xigua_hb/template/touch/common_header.php" ||
  fail "shared header marker is absent"
grep -Fq 'Light Grid R02' "${WORK_DIR}/source/plugin/xigua_hb/static/tgb-r02/light-grid-r02.css" ||
  fail "stylesheet marker is absent"
grep -Fq '请使用手机打开推广宝' "${WORK_DIR}/index.php" ||
  fail "desktop guide marker is absent"

DEPLOY_ID="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP_DIR="${STAGING_PRIVATE}/change-backups/${DEPLOY_ID}"
[ ! -e "${BACKUP_DIR}" ] || fail "backup path already exists"
mkdir -p "${BACKUP_DIR}"

{
  printf 'deploy_id=%s\n' "${DEPLOY_ID}"
  printf 'archive_sha256=%s\n' "${ACTUAL_SHA256}"
} > "${BACKUP_DIR}/DEPLOYMENT.env"

for relative_path in "${EXPECTED_FILES[@]}"; do
  target="${STAGING_SITE}/${relative_path}"
  if [ -e "${target}" ]; then
    mkdir -p "${BACKUP_DIR}/$(dirname "${relative_path}")"
    cp -a -- "${target}" "${BACKUP_DIR}/${relative_path}"
    printf '%s  %s\n' "$(sha256sum "${target}" | awk '{print $1}')" "${relative_path}" \
      >> "${BACKUP_DIR}/BEFORE_SHA256.txt"
  else
    printf '%s\n' "${relative_path}" >> "${BACKUP_DIR}/CREATED_FILES.txt"
  fi
done

for relative_path in "${EXPECTED_FILES[@]}"; do
  source_file="${WORK_DIR}/${relative_path}"
  target="${STAGING_SITE}/${relative_path}"
  previous_mode=""
  if [ -e "${target}" ]; then
    previous_mode="$(stat -c '%a' "${target}")"
  fi
  target_directory="$(dirname "${target}")"
  if [ ! -d "${target_directory}" ]; then
    install -d -o www -g www -m 0755 "${target_directory}"
  fi
  install -o www -g www -m 0644 "${source_file}" "${target}"
  if [ -n "${previous_mode}" ]; then
    chmod "${previous_mode}" "${target}"
  fi
  printf '%s  %s\n' "$(sha256sum "${target}" | awk '{print $1}')" "${relative_path}" \
    >> "${BACKUP_DIR}/AFTER_SHA256.txt"
done

# Correct the exact R02-owned asset directory even when an earlier interrupted
# deployment created it under the script's protective umask.
chown www:www "${STAGING_SITE}/source/plugin/xigua_hb/static/tgb-r02"
chmod 0755 "${STAGING_SITE}/source/plugin/xigua_hb/static/tgb-r02"

# The initial immutable staging copy may not contain Discuz's generated-template
# directory. A source-template mtime change causes the first real compile here.
if [ ! -d "${STAGING_SITE}/data/template" ]; then
  install -d -o www -g www -m 0777 "${STAGING_SITE}/data/template"
  printf '%s\n' "data/template/" >> "${BACKUP_DIR}/CREATED_DIRECTORIES.txt"
fi
chmod -R a-w "${BACKUP_DIR}"

MOBILE_HTML="${WORK_DIR}/mobile.html"
DESKTOP_HTML="${WORK_DIR}/desktop.html"
MOBILE_CODE="$(curl -sS -L --max-redirs 5 -o "${MOBILE_HTML}" -w '%{http_code}' \
  -H 'Host: tg-h5-ui-r02.local' \
  -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  "http://127.0.0.1:${LOOPBACK_PORT}/plugin.php?id=xigua_hb")"
[ "${MOBILE_CODE}" = "200" ] || fail "mobile page HTTP ${MOBILE_CODE}"
find "${STAGING_SITE}/data/template" -maxdepth 1 -type f \
  -name '*xigua_hb_touch_common_header*.php' -exec grep -Fq 'light-grid-r02.css' {} \; -print \
  | grep -q . || fail "compiled shared header did not load R02 stylesheet"

DESKTOP_CODE="$(curl -sS -o "${DESKTOP_HTML}" -w '%{http_code}' \
  -H 'Host: tg-h5-ui-r02.local' \
  -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)' \
  "http://127.0.0.1:${LOOPBACK_PORT}/")"
[ "${DESKTOP_CODE}" = "200" ] || fail "desktop guide HTTP ${DESKTOP_CODE}"
grep -Fq '请使用手机打开推广宝' "${DESKTOP_HTML}" || fail "desktop guide marker is absent from response"

for asset in \
  "source/plugin/xigua_hb/static/tgb-r02/light-grid-r02.css|Light Grid R02" \
  "source/plugin/xigua_hb/static/tgb-r02/brand-mark-r02.svg|<svg" \
  "source/plugin/xigua_hb/static/tgb-r02/chat-r02.svg|<svg"; do
  asset_path="${asset%%|*}"
  asset_marker="${asset#*|}"
  asset_body="${WORK_DIR}/$(basename "${asset_path}").response"
  asset_code="$(curl -sS -o "${asset_body}" -w '%{http_code}' \
    -H 'Host: tg-h5-ui-r02.local' \
    "http://127.0.0.1:${LOOPBACK_PORT}/${asset_path}")"
  [ "${asset_code}" = "200" ] || fail "asset ${asset_path} HTTP ${asset_code}"
  grep -Fq "${asset_marker}" "${asset_body}" ||
    fail "asset ${asset_path} returned fallback content"
done

printf '[R02-DEPLOY] PASS\n'
printf '[R02-DEPLOY] DEPLOY_ID=%s\n' "${DEPLOY_ID}"
printf '[R02-DEPLOY] ARCHIVE_SHA256=%s\n' "${ACTUAL_SHA256}"
printf '[R02-DEPLOY] MOBILE_HTTP=%s DESKTOP_HTTP=%s\n' "${MOBILE_CODE}" "${DESKTOP_CODE}"
printf '[R02-DEPLOY] BACKUP_DIR=%s\n' "${BACKUP_DIR}"
