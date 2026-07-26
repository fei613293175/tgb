#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

STAGING_BASE="/www/staging/tg-h5-ui-r03"
STAGING_SITE="${STAGING_BASE}/site"
STAGING_PRIVATE="${STAGING_BASE}/private"
LOOPBACK_PORT="18083"
STAGING_HOST="tg-h5-ui-r03.local"
ARCHIVE_PATH="${1:-}"
EXPECTED_SHA256="${2:-}"

EXPECTED_FILES=(
  "m/fpsm.html"
  "m/gywm.html"
  "m/help.html"
  "m/hyxy.html"
  "m/template/css/tgb-r03-legal.css"
  "m/xfxy.html"
  "m/xy.html"
  "m/yhxy.html"
  "m/yszc.html"
  "source/plugin/tb_cus_mobilereg/template/touch/loginphone.htm"
  "source/plugin/xigua_hb/static/tgb-r03/auth-light-grid-r03.css"
  "source/plugin/xigua_hb/static/tgb-r03/auth-r03.css"
  "template/default/touch/member/login.htm"
  "template/default/touch/member/register.htm"
)

fail() {
  printf '[R03-DEPLOY] ABORT: %s\n' "$1" >&2
  exit 1
}

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${STAGING_SITE}/source/plugin/xigua_hb" ] || fail "R03 staging site is absent"
[ -d "${STAGING_PRIVATE}" ] || fail "R03 private directory is absent"
[ -d "${STAGING_SITE}/data/template" ] || fail "Discuz compiled-template directory is absent"
[ -w "${STAGING_SITE}/data/template" ] || fail "Discuz compiled-template directory is not writable"
[ -f "${ARCHIVE_PATH}" ] || fail "overlay archive is absent"
[[ "${EXPECTED_SHA256}" =~ ^[0-9a-f]{64}$ ]] || fail "expected SHA-256 is invalid"

PRE_POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' -X POST \
  -H "Host: ${STAGING_HOST}" "http://127.0.0.1:${LOOPBACK_PORT}/")"
[ "${PRE_POST_CODE}" = "405" ] || fail "pre-deploy POST guard is ${PRE_POST_CODE}"

ACTUAL_SHA256="$(sha256sum "${ARCHIVE_PATH}" | awk '{print $1}')"
[ "${ACTUAL_SHA256}" = "${EXPECTED_SHA256}" ] || fail "overlay archive SHA-256 mismatch"

WORK_DIR="$(mktemp -d "${STAGING_PRIVATE}/r03-deploy-work.XXXXXX")"
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
[ "${ARCHIVE_FILES[*]}" = "${ALLOWED_FILES[*]}" ] || fail "archive allowlist mismatch"

for relative_path in "${EXPECTED_FILES[@]}"; do
  [ -f "${WORK_DIR}/${relative_path}" ] || fail "missing ${relative_path}"
  [ ! -L "${WORK_DIR}/${relative_path}" ] || fail "symbolic links are not allowed"
done

for template in \
  "template/default/touch/member/login.htm" \
  "template/default/touch/member/register.htm" \
  "source/plugin/tb_cus_mobilereg/template/touch/loginphone.htm"; do
  php -l "${WORK_DIR}/${template}" >/dev/null
  grep -Fq '/source/plugin/xigua_hb/static/tgb-r03/auth-r03.css?v=20260726-r03' \
    "${WORK_DIR}/${template}" || fail "local auth CSS missing from ${template}"
  grep -Fq '/source/plugin/xigua_hb/static/tgb-r03/auth-light-grid-r03.css?v=20260726-r03' \
    "${WORK_DIR}/${template}" || fail "semantic auth CSS missing from ${template}"
  grep -Fq '/source/plugin/tb_cus_base/static/js/jquery-3.3.1.min.js' \
    "${WORK_DIR}/${template}" || fail "early local jQuery missing from ${template}"
  if grep -Eqi 'cdn\.tailwindcss\.com|cdn\.jsdelivr\.net|cdnjs\.cloudflare\.com|unpkg\.com' \
    "${WORK_DIR}/${template}"; then
    fail "public UI CDN remains in ${template}"
  fi
  if grep -Eq '(^|[^A-Za-z0-9_])\$[[:space:]]*\(' "${WORK_DIR}/${template}"; then
    fail "direct dollar alias call remains in ${template}"
  fi
done

grep -Fq '推广宝 - 登录账号' "${WORK_DIR}/template/default/touch/member/login.htm" ||
  fail "login brand marker is absent"
grep -Fq '推广宝 - 注册账号' "${WORK_DIR}/template/default/touch/member/register.htm" ||
  fail "register brand marker is absent"
grep -Fq '推广宝 - 验证码登录' \
  "${WORK_DIR}/source/plugin/tb_cus_mobilereg/template/touch/loginphone.htm" ||
  fail "SMS-login brand marker is absent"
grep -Fq 'Light Grid R03 authenticated-entry pages' \
  "${WORK_DIR}/source/plugin/xigua_hb/static/tgb-r03/auth-light-grid-r03.css" ||
  fail "auth CSS marker is absent"
grep -Fq 'Light Grid R03 legal, help and about pages' \
  "${WORK_DIR}/m/template/css/tgb-r03-legal.css" ||
  fail "legal CSS marker is absent"
grep -Fq '<title>推广宝 - 帮助中心</title>' "${WORK_DIR}/m/help.html" ||
  fail "help UI brand marker is absent"
grep -Fq '<title>推广宝 - 关于我们</title>' "${WORK_DIR}/m/gywm.html" ||
  fail "about UI brand marker is absent"
grep -Fq 'copyright 2024-2025 创脉引擎 版权所有' "${WORK_DIR}/m/gywm.html" ||
  fail "frozen about copyright is absent"

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
  source_file="${WORK_DIR}/${relative_path}"
  target="${STAGING_SITE}/${relative_path}"
  target_directory="$(dirname "${target}")"
  if [ ! -d "${target_directory}" ]; then
    install -d -o www -g www -m 0755 "${target_directory}"
  fi
  install -o www -g www -m 0644 "${source_file}" "${target}"
  printf '%s  %s\n' "$(sha256sum "${target}" | awk '{print $1}')" "${relative_path}" \
    >>"${BACKUP_DIR}/AFTER_SHA256.txt"
done
chmod -R a-w "${BACKUP_DIR}"

probe_page() {
  local path="$1"
  local marker="$2"
  local output="${WORK_DIR}/page-$(printf '%s' "${path}" | sha256sum | cut -c1-12).html"
  local code
  code="$(curl -sS -L --max-redirs 5 -o "${output}" -w '%{http_code}' \
    -H "Host: ${STAGING_HOST}" \
    -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
    "http://127.0.0.1:${LOOPBACK_PORT}${path}")"
  [ "${code}" = "200" ] || fail "page ${path} HTTP ${code}"
  grep -Fq "${marker}" "${output}" || fail "page ${path} marker is absent"
}

probe_page '/plugin.php?id=xigua_hb' '推广宝 - 登录账号'
probe_page '/member.php?mod=register&mobile=2' '推广宝 - 注册账号'
probe_page '/plugin.php?id=tb_cus_mobilereg:mobilelogin' '推广宝 - 验证码登录'
probe_page '/m/xy.html' '平台用户服务协议'
probe_page '/m/yszc.html' 'MKT用户隐私政策'
probe_page '/m/yhxy.html' 'MKT用户协议'
probe_page '/m/xfxy.html' 'MKT用户消费协议'
probe_page '/m/fpsm.html' '用户防骗提醒与免责声明'
probe_page '/m/hyxy.html' '会员充值开通用户协议'
probe_page '/m/help.html' '推广宝 - 帮助中心'
probe_page '/m/gywm.html' '推广宝 - 关于我们'

for asset in \
  'source/plugin/xigua_hb/static/tgb-r03/auth-r03.css|tailwindcss v3.4.17|text/css' \
  'source/plugin/xigua_hb/static/tgb-r03/auth-light-grid-r03.css|Light Grid R03 authenticated-entry pages|text/css' \
  'm/template/css/tgb-r03-legal.css|Light Grid R03 legal, help and about pages|text/css'; do
  asset_path="${asset%%|*}"
  remainder="${asset#*|}"
  marker="${remainder%%|*}"
  expected_type="${remainder#*|}"
  body="${WORK_DIR}/$(basename "${asset_path}").response"
  headers="${body}.headers"
  code="$(curl -sS -D "${headers}" -o "${body}" -w '%{http_code}' \
    -H "Host: ${STAGING_HOST}" "http://127.0.0.1:${LOOPBACK_PORT}/${asset_path}")"
  [ "${code}" = "200" ] || fail "asset ${asset_path} HTTP ${code}"
  grep -Fq "${marker}" "${body}" || fail "asset ${asset_path} returned fallback content"
  grep -Eqi "^Content-Type:[[:space:]]*${expected_type}" "${headers}" ||
    fail "asset ${asset_path} MIME mismatch"
done

POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' -X POST \
  -H "Host: ${STAGING_HOST}" "http://127.0.0.1:${LOOPBACK_PORT}/")"
[ "${POST_CODE}" = "405" ] || fail "post-deploy POST guard is ${POST_CODE}"

printf '[R03-DEPLOY] PASS\n'
printf '[R03-DEPLOY] DEPLOY_ID=%s\n' "${DEPLOY_ID}"
printf '[R03-DEPLOY] ARCHIVE_SHA256=%s\n' "${ACTUAL_SHA256}"
printf '[R03-DEPLOY] FILES=%s PAGES=%s POST=%s\n' "${#EXPECTED_FILES[@]}" '11' "${POST_CODE}"
printf '[R03-DEPLOY] BACKUP_DIR=%s\n' "${BACKUP_DIR}"
