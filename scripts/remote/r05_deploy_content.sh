#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

STAGING_BASE="/www/staging/tg-h5-ui-r05"
STAGING_SITE="${STAGING_BASE}/site"
STAGING_PRIVATE="${STAGING_BASE}/private"
STAGING_HOST="tg-h5-ui-r05.local"
LOOPBACK_PORT="18085"
ARCHIVE_PATH="${1:-}"
EXPECTED_SHA256="${2:-}"

EXPECTED_FILES=(
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
  "source/plugin/xigua_hb/template/touch/manage.php"
  "source/plugin/xigua_hb/template/touch/member_fav_li.php"
  "source/plugin/xigua_hb/template/touch/member_li.php"
  "source/plugin/xigua_hb/template/touch/member_new.php"
  "source/plugin/xigua_hb/template/touch/mycomment.php"
  "source/plugin/xigua_hb/template/touch/mycover.php"
  "source/plugin/xigua_hb/template/touch/mypub.php"
  "source/plugin/xigua_hb/template/touch/mypub_item.php"
  "source/plugin/xigua_hb/template/touch/mypub_item_new.php"
  "source/plugin/xigua_hb/template/touch/pub.php"
  "source/plugin/xigua_hb/template/touch/pub_selects.php"
  "source/plugin/xigua_hb/template/touch/pub_twoselects.php"
  "source/plugin/xigua_hj/static/tgb-r05/report-light-grid-r05.css"
  "source/plugin/xigua_hj/template/touch/index.php"
)

fail() {
  printf '[R05-DEPLOY] ABORT: %s\n' "$1" >&2
  exit 1
}

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${STAGING_SITE}/source/plugin" ] || fail "R05 staging site is absent"
[ -d "${STAGING_PRIVATE}" ] || fail "R05 private directory is absent"
[ -d "${STAGING_SITE}/data/template" ] || fail "Discuz template cache is absent"
[ -w "${STAGING_SITE}/data/template" ] || fail "Discuz template cache is not writable"
[ ! -e "${STAGING_SITE}/__r05_auth__" ] || fail "authentication bridge must be OFF"
[ ! -e "${STAGING_PRIVATE}/browser-origin-active" ] || fail "browser-origin mode must be OFF"
[ -f "${ARCHIVE_PATH}" ] || fail "overlay archive is absent"
[[ "${EXPECTED_SHA256}" =~ ^[0-9a-f]{64}$ ]] || fail "expected SHA-256 is invalid"
command -v php >/dev/null || fail "php is unavailable"
command -v curl >/dev/null || fail "curl is unavailable"

PRE_POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' -X POST \
  -H "Host: ${STAGING_HOST}" "http://127.0.0.1:${LOOPBACK_PORT}/")"
[ "${PRE_POST_CODE}" = "405" ] || fail "pre-deploy POST guard is ${PRE_POST_CODE}"

ACTUAL_SHA256="$(sha256sum "${ARCHIVE_PATH}" | awk '{print $1}')"
[ "${ACTUAL_SHA256}" = "${EXPECTED_SHA256}" ] || fail "overlay SHA-256 mismatch"

mapfile -t ARCHIVE_FILES < <(tar -tzf "${ARCHIVE_PATH}" | sed '/\/$/d' | LC_ALL=C sort)
mapfile -t ALLOWED_FILES < <(printf '%s\n' "${EXPECTED_FILES[@]}" | LC_ALL=C sort)
[ "${ARCHIVE_FILES[*]}" = "${ALLOWED_FILES[*]}" ] || fail "archive allowlist mismatch"

WORK_DIR="$(mktemp -d "${STAGING_PRIVATE}/r05-deploy-work.XXXXXX")"
cleanup() {
  rm -rf -- "${WORK_DIR}"
}
trap cleanup EXIT
tar -xzf "${ARCHIVE_PATH}" --no-same-owner --no-same-permissions -C "${WORK_DIR}"

for relative_path in "${EXPECTED_FILES[@]}"; do
  [ -f "${WORK_DIR}/${relative_path}" ] || fail "missing regular file ${relative_path}"
  [ ! -L "${WORK_DIR}/${relative_path}" ] || fail "symbolic links are forbidden"
done

while IFS= read -r -d '' template; do
  php -l "${template}" >/dev/null || fail "PHP lint failed: ${template#${WORK_DIR}/}"
done < <(find "${WORK_DIR}" -type f -name '*.php' -print0)

if grep -RIEq ':has[[:space:]]*\(' "${WORK_DIR}"; then
  fail "unsupported :has selector remains"
fi
if grep -RIFq 'metadata normalized for UTF-8 tooling' "${WORK_DIR}"; then
  fail "non-UI metadata drift remains"
fi
if grep -RIEq 'cdn\.tailwindcss\.com|cdn\.jsdelivr\.net|cdnjs\.cloudflare\.com|unpkg\.com|fonts\.googleapis\.com|use\.fontawesome\.com' \
  "${WORK_DIR}"; then
  fail "new public UI CDN dependency remains"
fi

LANE_A_CSS="source/plugin/xigua_hb/static/tgb-r05/lane-a-light-grid-r05.css"
CARD_CSS="source/plugin/tb_cus_card/static/tgb-r05/card-light-grid-r05.css"
REPORT_CSS="source/plugin/xigua_hj/static/tgb-r05/report-light-grid-r05.css"
for css in "${LANE_A_CSS}" "${CARD_CSS}" "${REPORT_CSS}"; do
  grep -Fiq '#2764ff' "${WORK_DIR}/${css}" || fail "blue token missing: ${css}"
  grep -Fiq '#f4f7fb' "${WORK_DIR}/${css}" || fail "light canvas missing: ${css}"
  grep -Fiq 'safe-area-inset' "${WORK_DIR}/${css}" || fail "safe area missing: ${css}"
done

for template in hong_list.php jl_jy.php jl_jy_v.php member_new.php; do
  grep -Fq "${LANE_A_CSS}?20260727-r05-a1" \
    "${WORK_DIR}/source/plugin/xigua_hb/template/touch/${template}" ||
    fail "Lane A versioned CSS link missing: ${template}"
done
for template in add.htm shownext.htm; do
  grep -Fq "${CARD_CSS}?20260727-r05-c2" \
    "${WORK_DIR}/source/plugin/tb_cus_card/template/touch/${template}" ||
    fail "card versioned CSS link missing: ${template}"
done
grep -Fq "${REPORT_CSS}?20260727-r05-c2" \
  "${WORK_DIR}/source/plugin/xigua_hj/template/touch/index.php" ||
  fail "report versioned CSS link missing"
grep -Fq "source/plugin/xigua_hb/static/tgb-r02/light-grid-r02.css?v=20260727-r05-common1" \
  "${WORK_DIR}/source/plugin/xigua_hb/template/touch/common_header.php" ||
  fail "shared header versioned CSS link missing"

DEPLOY_ID="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP_DIR="${STAGING_PRIVATE}/change-backups/${DEPLOY_ID}"
[ ! -e "${BACKUP_DIR}" ] || fail "backup path already exists"
mkdir -p "${BACKUP_DIR}"
{
  printf 'deploy_id=%s\n' "${DEPLOY_ID}"
  printf 'archive_sha256=%s\n' "${ACTUAL_SHA256}"
} >"${BACKUP_DIR}/DEPLOYMENT.env"

for relative_path in "${EXPECTED_FILES[@]}"; do
  target="${STAGING_SITE}/${relative_path}"
  if [ -e "${target}" ]; then
    mkdir -p "${BACKUP_DIR}/$(dirname "${relative_path}")"
    cp -a -- "${target}" "${BACKUP_DIR}/${relative_path}"
    printf '%s  %s\n' "$(sha256sum "${target}" | awk '{print $1}')" "${relative_path}" \
      >>"${BACKUP_DIR}/BEFORE_SHA256.txt"
  else
    printf '%s\n' "${relative_path}" >>"${BACKUP_DIR}/CREATED_FILES.txt"
  fi
done

for relative_path in "${EXPECTED_FILES[@]}"; do
  target="${STAGING_SITE}/${relative_path}"
  install -d -o www -g www -m 0755 "$(dirname "${target}")"
  install -o www -g www -m 0644 "${WORK_DIR}/${relative_path}" "${target}"
  printf '%s  %s\n' "$(sha256sum "${target}" | awk '{print $1}')" "${relative_path}" \
    >>"${BACKUP_DIR}/AFTER_SHA256.txt"
done
chmod -R a-w "${BACKUP_DIR}"

HOME_CODE="$(curl -sS -L --max-redirs 5 -o "${WORK_DIR}/home.html" -w '%{http_code}' \
  --resolve "${STAGING_HOST}:${LOOPBACK_PORT}:127.0.0.1" \
  -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  "http://${STAGING_HOST}:${LOOPBACK_PORT}/plugin.php?id=xigua_hb&mobile=2")"
[ "${HOME_CODE}" = "200" ] || fail "staging home HTTP ${HOME_CODE}"

for css in "${LANE_A_CSS}" "${CARD_CSS}" "${REPORT_CSS}"; do
  safe_name="$(basename "${css}")"
  headers="${WORK_DIR}/${safe_name}.headers"
  code="$(curl -sS -D "${headers}" -o "${WORK_DIR}/${safe_name}" -w '%{http_code}' \
    -H "Host: ${STAGING_HOST}" "http://127.0.0.1:${LOOPBACK_PORT}/${css}")"
  [ "${code}" = "200" ] || fail "CSS HTTP ${code}: ${css}"
  grep -Eqi '^Content-Type:[[:space:]]*text/css' "${headers}" || fail "CSS MIME mismatch: ${css}"
  grep -Fiq '#2764ff' "${WORK_DIR}/${safe_name}" || fail "CSS body mismatch: ${css}"
done

POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' -X POST \
  -H "Host: ${STAGING_HOST}" "http://127.0.0.1:${LOOPBACK_PORT}/")"
[ "${POST_CODE}" = "405" ] || fail "post-deploy POST guard is ${POST_CODE}"

printf '[R05-DEPLOY] PASS\n'
printf '[R05-DEPLOY] DEPLOY_ID=%s\n' "${DEPLOY_ID}"
printf '[R05-DEPLOY] ARCHIVE_SHA256=%s\n' "${ACTUAL_SHA256}"
printf '[R05-DEPLOY] FILES=%s PHP_LINT=PASS HOME=%s POST=%s\n' \
  "${#EXPECTED_FILES[@]}" "${HOME_CODE}" "${POST_CODE}"
printf '[R05-DEPLOY] BACKUP_DIR=%s\n' "${BACKUP_DIR}"
