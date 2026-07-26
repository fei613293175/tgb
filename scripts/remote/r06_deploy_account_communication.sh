#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

R06_BASE="/www/staging/tg-h5-ui-r06"
R06_SITE="${R06_BASE}/site"
R06_PRIVATE="${R06_BASE}/private"
R06_HOST="tg-h5-ui-r06.local"
R06_PORT="18086"
ARCHIVE_PATH="${1:-}"
EXPECTED_SHA256="${2:-}"

FILES=(
  "source/plugin/deluser/static/tgb-r06-cancel-light-grid.css"
  "source/plugin/deluser/template/touch/main.htm"
  "source/plugin/xiaomy_certification/static/tgb-r06-certification-light-grid.css"
  "source/plugin/xiaomy_certification/template/touch/webstressapipay.htm"
  "source/plugin/xigua_hb/static/tgb-r06/account-light-grid-r06.css"
  "source/plugin/xigua_hb/template/touch/my_new.php"
  "source/plugin/xigua_hb/template/touch/myaddr.php"
  "source/plugin/xigua_hb/template/touch/shezhi.php"
  "source/plugin/xigua_lt/static/tgb-r06/chats-list-light-grid-r06.css"
  "source/plugin/xigua_lt/template/touch/chats.php"
  "source/plugin/xigua_member/images/tgb-r06-profile-light-grid.css"
  "source/plugin/xigua_member/profile.inc.php"
  "template/comiis_app/touch/common/showmessage.php"
)

fail() { printf '[R06-DEPLOY] ABORT: %s\n' "$1" >&2; exit 1; }

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${R06_SITE}/source/plugin" ] || fail "R06 staging is absent"
[ -d "${R06_PRIVATE}" ] || fail "R06 private directory is absent"
[ -f "${ARCHIVE_PATH}" ] || fail "overlay archive is absent"
[[ "${EXPECTED_SHA256}" =~ ^[0-9a-f]{64}$ ]] || fail "expected SHA-256 is invalid"
[ ! -e "${R06_SITE}/__r06_auth__" ] || fail "authentication bridge must be OFF"

PRE_POST="$(curl -sS -o /dev/null -w '%{http_code}' -X POST -H "Host: ${R06_HOST}" "http://127.0.0.1:${R06_PORT}/")"
[ "${PRE_POST}" = "405" ] || fail "pre-deploy POST guard is ${PRE_POST}"
ACTUAL_SHA256="$(sha256sum "${ARCHIVE_PATH}" | awk '{print $1}')"
[ "${ACTUAL_SHA256}" = "${EXPECTED_SHA256}" ] || fail "overlay SHA-256 mismatch"

mapfile -t ARCHIVE_FILES < <(tar -tzf "${ARCHIVE_PATH}" | sed '/\/$/d; s#^r06-site-overlay/##' | LC_ALL=C sort)
mapfile -t ALLOWED_FILES < <(printf '%s\n' "${FILES[@]}" | LC_ALL=C sort)
[ "${ARCHIVE_FILES[*]}" = "${ALLOWED_FILES[*]}" ] || fail "archive allowlist mismatch"

WORK_DIR="$(mktemp -d "${R06_PRIVATE}/r06-overlay.XXXXXX")"
cleanup() { rm -rf -- "${WORK_DIR}"; }
trap cleanup EXIT
tar -xzf "${ARCHIVE_PATH}" --strip-components=1 --no-same-owner --no-same-permissions -C "${WORK_DIR}"

for relative in "${FILES[@]}"; do
  [ -f "${WORK_DIR}/${relative}" ] || fail "missing regular file ${relative}"
  [ ! -L "${WORK_DIR}/${relative}" ] || fail "symbolic links are forbidden"
  case "${relative}" in *.php) php -l "${WORK_DIR}/${relative}" >/dev/null || fail "PHP lint failed: ${relative}" ;; esac
done
if grep -RIEq ':has[[:space:]]*\(' "${WORK_DIR}"; then fail "unsupported :has selector remains"; fi
if grep -RIEq 'cdn\.tailwindcss\.com|cdn\.jsdelivr\.net|cdnjs\.cloudflare\.com|unpkg\.com|fonts\.googleapis\.com|use\.fontawesome\.com' "${WORK_DIR}" --include='*.css'; then
  fail "new CSS public UI CDN dependency remains"
fi

DEPLOY_ID="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP_DIR="${R06_PRIVATE}/change-backups/${DEPLOY_ID}-account-communication-v1"
[ ! -e "${BACKUP_DIR}" ] || fail "backup path already exists"
mkdir -p "${BACKUP_DIR}"
printf 'deploy_id=%s\narchive_sha256=%s\nscope=CLICK_PROVEN_R06\n' "${DEPLOY_ID}" "${ACTUAL_SHA256}" >"${BACKUP_DIR}/DEPLOYMENT.env"

for relative in "${FILES[@]}"; do
  target="${R06_SITE}/${relative}"
  if [ -e "${target}" ]; then
    mkdir -p "${BACKUP_DIR}/$(dirname "${relative}")"
    cp -a -- "${target}" "${BACKUP_DIR}/${relative}"
    printf '%s  %s\n' "$(sha256sum "${target}" | awk '{print $1}')" "${relative}" >>"${BACKUP_DIR}/BEFORE_SHA256.txt"
  else
    printf '%s\n' "${relative}" >>"${BACKUP_DIR}/BEFORE_ABSENT.txt"
  fi
done

for relative in "${FILES[@]}"; do
  target="${R06_SITE}/${relative}"
  install -d -o www -g www -m 0755 "$(dirname "${target}")"
  install -o www -g www -m 0644 "${WORK_DIR}/${relative}" "${target}"
  cmp "${WORK_DIR}/${relative}" "${target}" || fail "install mismatch: ${relative}"
done

find "${R06_SITE}/data/template" -mindepth 1 -maxdepth 1 -type f -delete
HOME_CODE="$(curl -sS -L --max-redirs 5 -o "${WORK_DIR}/home.html" -w '%{http_code}' --resolve "${R06_HOST}:${R06_PORT}:127.0.0.1" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' "http://${R06_HOST}:${R06_PORT}/plugin.php?id=xigua_hb&mobile=2")"
[ "${HOME_CODE}" = "200" ] || fail "staging home HTTP ${HOME_CODE}"
POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' -X POST -H "Host: ${R06_HOST}" "http://127.0.0.1:${R06_PORT}/")"
[ "${POST_CODE}" = "405" ] || fail "post-deploy POST guard is ${POST_CODE}"

for relative in "${FILES[@]}"; do
  printf '%s  %s\n' "$(sha256sum "${R06_SITE}/${relative}" | awk '{print $1}')" "${relative}" >>"${BACKUP_DIR}/AFTER_SHA256.txt"
done
chmod -R a-w "${BACKUP_DIR}"

printf '[R06-DEPLOY] PASS\n'
printf '[R06-DEPLOY] DEPLOY_ID=%s ARCHIVE_SHA256=%s FILES=13 PHP_LINT=PASS HOME=%s POST=%s\n' "${DEPLOY_ID}" "${ACTUAL_SHA256}" "${HOME_CODE}" "${POST_CODE}"
printf '[R06-DEPLOY] BACKUP_DIR=%s\n' "${BACKUP_DIR}"
