#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

R04_SITE="/www/staging/tg-h5-ui-r04/site"
R05_BASE="/www/staging/tg-h5-ui-r05"
R05_SITE="${R05_BASE}/site"
R05_PRIVATE="${R05_BASE}/private"
R05_HOST="tg-h5-ui-r05.local"
R05_PORT="18085"
ARCHIVE_PATH="${1:-}"
EXPECTED_SHA256="${2:-}"

KEEP_FILES=(
  "source/plugin/xigua_hb/template/touch/manage.php"
  "source/plugin/xigua_hb/template/touch/mypub.php"
  "source/plugin/xigua_hb/template/touch/mypub_item.php"
  "source/plugin/xigua_hb/template/touch/mypub_item_new.php"
  "source/plugin/xigua_hb/template/touch/pub.php"
  "source/plugin/xigua_hb/template/touch/pub_selects.php"
  "source/plugin/xigua_hb/template/touch/pub_twoselects.php"
)

RESTORE_FILES=(
  "source/plugin/tb_cus_card/static/tgb-r05/card-light-grid-r05.css"
  "source/plugin/tb_cus_card/template/touch/add.htm"
  "source/plugin/tb_cus_card/template/touch/shownext.htm"
  "source/plugin/xigua_hb/static/tgb-r02/light-grid-r02.css"
  "source/plugin/xigua_hb/static/tgb-r05/lane-a-light-grid-r05.css"
  "source/plugin/xigua_hb/template/touch/comment_li_01.php"
  "source/plugin/xigua_hb/template/touch/comment_li_01_sub.php"
  "source/plugin/xigua_hb/template/touch/common_header.php"
  "source/plugin/xigua_hb/template/touch/fav.php"
  "source/plugin/xigua_hb/template/touch/hong_li.php"
  "source/plugin/xigua_hb/template/touch/hong_list.php"
  "source/plugin/xigua_hb/template/touch/jl_jy.php"
  "source/plugin/xigua_hb/template/touch/jl_jy_v.php"
  "source/plugin/xigua_hb/template/touch/list_by_cat1.php"
  "source/plugin/xigua_hb/template/touch/member_fav_li.php"
  "source/plugin/xigua_hb/template/touch/member_li.php"
  "source/plugin/xigua_hb/template/touch/member_new.php"
  "source/plugin/xigua_hb/template/touch/mycomment.php"
  "source/plugin/xigua_hb/template/touch/mycover.php"
  "source/plugin/xigua_hj/static/tgb-r05/report-light-grid-r05.css"
  "source/plugin/xigua_hj/template/touch/index.php"
)

fail() { printf '[R05-V4-DEPLOY] ABORT: %s\n' "$1" >&2; exit 1; }

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${R04_SITE}/source/plugin" ] || fail "closed R04 source staging is absent"
[ -d "${R05_SITE}/source/plugin" ] || fail "R05 staging is absent"
[ -d "${R05_PRIVATE}" ] || fail "R05 private directory is absent"
[ ! -e "${R05_SITE}/__r05_auth__" ] || fail "authentication bridge must be OFF"
[ ! -e "${R05_PRIVATE}/browser-origin-active" ] || fail "browser-origin mode must be OFF"
[ -f "${ARCHIVE_PATH}" ] || fail "overlay archive is absent"
[[ "${EXPECTED_SHA256}" =~ ^[0-9a-f]{64}$ ]] || fail "expected SHA-256 is invalid"

PRE_POST="$(curl -sS -o /dev/null -w '%{http_code}' -X POST -H "Host: ${R05_HOST}" "http://127.0.0.1:${R05_PORT}/")"
[ "${PRE_POST}" = "405" ] || fail "pre-deploy POST guard is ${PRE_POST}"
ACTUAL_SHA256="$(sha256sum "${ARCHIVE_PATH}" | awk '{print $1}')"
[ "${ACTUAL_SHA256}" = "${EXPECTED_SHA256}" ] || fail "overlay SHA-256 mismatch"

mapfile -t ARCHIVE_FILES < <(tar -tzf "${ARCHIVE_PATH}" | sed '/\/$/d' | LC_ALL=C sort)
mapfile -t ALLOWED_FILES < <(printf '%s\n' "${KEEP_FILES[@]}" | LC_ALL=C sort)
[ "${ARCHIVE_FILES[*]}" = "${ALLOWED_FILES[*]}" ] || fail "archive allowlist mismatch"

WORK_DIR="$(mktemp -d "${R05_PRIVATE}/r05-v4-work.XXXXXX")"
cleanup() { rm -rf -- "${WORK_DIR}"; }
trap cleanup EXIT
tar -xzf "${ARCHIVE_PATH}" --no-same-owner --no-same-permissions -C "${WORK_DIR}"

for relative in "${KEEP_FILES[@]}"; do
  [ -f "${WORK_DIR}/${relative}" ] || fail "missing regular file ${relative}"
  [ ! -L "${WORK_DIR}/${relative}" ] || fail "symbolic links are forbidden"
  php -l "${WORK_DIR}/${relative}" >/dev/null || fail "PHP lint failed: ${relative}"
done

if grep -RIEq ':has[[:space:]]*\(' "${WORK_DIR}"; then fail "unsupported :has selector remains"; fi
if grep -RIEq 'cdn\.tailwindcss\.com|cdn\.jsdelivr\.net|cdnjs\.cloudflare\.com|unpkg\.com|fonts\.googleapis\.com|use\.fontawesome\.com' "${WORK_DIR}"; then
  fail "new public UI CDN dependency remains"
fi
if grep -RIEq '\.cmt-wrap|\.cmt-list|\.view-content-comment-text|tgb-r05-card-page|tgb-r05-report-page' "${WORK_DIR}"; then
  fail "out-of-scope UI selector remains"
fi

DEPLOY_ID="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP_DIR="${R05_PRIVATE}/change-backups/${DEPLOY_ID}-click-proven-v4"
[ ! -e "${BACKUP_DIR}" ] || fail "backup path already exists"
mkdir -p "${BACKUP_DIR}"
printf 'deploy_id=%s\narchive_sha256=%s\nscope=CLICK_PROVEN_ONLY\n' "${DEPLOY_ID}" "${ACTUAL_SHA256}" >"${BACKUP_DIR}/DEPLOYMENT.env"

for relative in "${RESTORE_FILES[@]}" "${KEEP_FILES[@]}"; do
  target="${R05_SITE}/${relative}"
  if [ -e "${target}" ]; then
    mkdir -p "${BACKUP_DIR}/$(dirname "${relative}")"
    cp -a -- "${target}" "${BACKUP_DIR}/${relative}"
    printf '%s  %s\n' "$(sha256sum "${target}" | awk '{print $1}')" "${relative}" >>"${BACKUP_DIR}/BEFORE_SHA256.txt"
  else
    printf '%s\n' "${relative}" >>"${BACKUP_DIR}/BEFORE_ABSENT.txt"
  fi
done

for relative in "${RESTORE_FILES[@]}"; do
  source_path="${R04_SITE}/${relative}"
  target="${R05_SITE}/${relative}"
  if [ -f "${source_path}" ]; then
    install -d -o www -g www -m 0755 "$(dirname "${target}")"
    install -o www -g www -m 0644 "${source_path}" "${target}"
  else
    rm -f -- "${target}"
  fi
done

for relative in "${KEEP_FILES[@]}"; do
  target="${R05_SITE}/${relative}"
  install -d -o www -g www -m 0755 "$(dirname "${target}")"
  install -o www -g www -m 0644 "${WORK_DIR}/${relative}" "${target}"
done

for relative in "${RESTORE_FILES[@]}"; do
  source_path="${R04_SITE}/${relative}"
  target="${R05_SITE}/${relative}"
  if [ -f "${source_path}" ]; then
    cmp "${source_path}" "${target}" || fail "R04 restore mismatch: ${relative}"
  else
    [ ! -e "${target}" ] || fail "R05-only out-of-scope asset remains: ${relative}"
  fi
done
for relative in "${KEEP_FILES[@]}"; do
  cmp "${WORK_DIR}/${relative}" "${R05_SITE}/${relative}" || fail "v4 install mismatch: ${relative}"
done

find "${R05_SITE}/data/template" -mindepth 1 -maxdepth 1 -type f -delete

HOME_CODE="$(curl -sS -L --max-redirs 5 -o "${WORK_DIR}/home.html" -w '%{http_code}' --resolve "${R05_HOST}:${R05_PORT}:127.0.0.1" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' "http://${R05_HOST}:${R05_PORT}/plugin.php?id=xigua_hb&mobile=2")"
[ "${HOME_CODE}" = "200" ] || fail "staging home HTTP ${HOME_CODE}"
POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' -X POST -H "Host: ${R05_HOST}" "http://127.0.0.1:${R05_PORT}/")"
[ "${POST_CODE}" = "405" ] || fail "post-deploy POST guard is ${POST_CODE}"

for relative in "${RESTORE_FILES[@]}" "${KEEP_FILES[@]}"; do
  target="${R05_SITE}/${relative}"
  if [ -f "${target}" ]; then
    printf '%s  %s\n' "$(sha256sum "${target}" | awk '{print $1}')" "${relative}" >>"${BACKUP_DIR}/AFTER_SHA256.txt"
  else
    printf '%s\n' "${relative}" >>"${BACKUP_DIR}/AFTER_ABSENT.txt"
  fi
done
chmod -R a-w "${BACKUP_DIR}"

printf '[R05-V4-DEPLOY] PASS\n'
printf '[R05-V4-DEPLOY] DEPLOY_ID=%s ARCHIVE_SHA256=%s\n' "${DEPLOY_ID}" "${ACTUAL_SHA256}"
printf '[R05-V4-DEPLOY] KEEP=7 RESTORED_OR_REMOVED=21 PHP_LINT=PASS HOME=%s POST=%s\n' "${HOME_CODE}" "${POST_CODE}"
printf '[R05-V4-DEPLOY] BACKUP_DIR=%s\n' "${BACKUP_DIR}"
