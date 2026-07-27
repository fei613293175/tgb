#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

BASE="/www/staging/tg-h5-ui-r08"
SITE="${BASE}/site"
PRIVATE="${BASE}/private"
HOST="tg-h5-ui-r08.local"
PORT="18088"
ARCHIVE_PATH="${1:-}"
EXPECTED_SHA256="${2:-}"
FILES=(
  "done/app.html"
  "done/tgb-r08-app-download.css"
  "source/plugin/tb_cus_pipei/static/tgb-r08/dividend-light-grid-r08.css"
  "source/plugin/tb_cus_pipei/template/touch/main.htm"
  "source/plugin/view/module/site/sign.php"
  "source/plugin/view/static/tgb-r08/sign-light-grid-r08.css"
  "source/plugin/xigua_hh/static/tgb-r08/growth-light-grid-r08.css"
  "source/plugin/xigua_hh/template/touch/fans_li.php"
  "source/plugin/xigua_hh/template/touch/invite.php"
  "source/plugin/xigua_hh/template/touch/myfans.php"
)

fail() { printf '[R08-DEPLOY] ABORT: %s\n' "$1" >&2; exit 1; }
[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${SITE}/source/plugin" ] || fail "R08 staging is absent"
[ -d "${PRIVATE}" ] || fail "R08 private directory is absent"
[ -f "${ARCHIVE_PATH}" ] || fail "overlay archive is absent"
[[ "${EXPECTED_SHA256}" =~ ^[0-9a-f]{64}$ ]] || fail "expected SHA-256 is invalid"

PRE_POST="$(curl -sS -o /dev/null -w '%{http_code}' -X POST -H "Host: ${HOST}" "http://127.0.0.1:${PORT}/")"
[ "${PRE_POST}" = "405" ] || fail "pre-deploy POST guard is ${PRE_POST}"
ACTUAL_SHA256="$(sha256sum "${ARCHIVE_PATH}" | awk '{print $1}')"
[ "${ACTUAL_SHA256}" = "${EXPECTED_SHA256}" ] || fail "overlay SHA-256 mismatch"
mapfile -t ARCHIVE_FILES < <(tar -tzf "${ARCHIVE_PATH}" | sed '/\/$/d; s#^r08-site-overlay/##' | LC_ALL=C sort)
mapfile -t ALLOWED_FILES < <(printf '%s\n' "${FILES[@]}" | LC_ALL=C sort)
[ "${ARCHIVE_FILES[*]}" = "${ALLOWED_FILES[*]}" ] || fail "archive allowlist mismatch"

WORK_DIR="$(mktemp -d "${PRIVATE}/r08-overlay.XXXXXX")"
cleanup() { rm -rf -- "${WORK_DIR}"; }
trap cleanup EXIT
tar -xzf "${ARCHIVE_PATH}" --strip-components=1 --no-same-owner --no-same-permissions -C "${WORK_DIR}"
for relative in "${FILES[@]}"; do
  [ -f "${WORK_DIR}/${relative}" ] || fail "missing file ${relative}"
  [ ! -L "${WORK_DIR}/${relative}" ] || fail "symbolic links are forbidden"
  case "${relative}" in *.php) php -l "${WORK_DIR}/${relative}" >/dev/null || fail "PHP lint failed: ${relative}" ;; esac
done
if grep -RIEq ':has[[:space:]]*\(' "${WORK_DIR}"; then fail "unsupported :has selector remains"; fi

DEPLOY_ID="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP_DIR="${PRIVATE}/change-backups/${DEPLOY_ID}-click-proven-v1"
mkdir -p "${BACKUP_DIR}"
printf 'deploy_id=%s\narchive_sha256=%s\nscope=CLICK_PROVEN_R08\n' "${DEPLOY_ID}" "${ACTUAL_SHA256}" >"${BACKUP_DIR}/DEPLOYMENT.env"
for relative in "${FILES[@]}"; do
  target="${SITE}/${relative}"
  if [ -e "${target}" ]; then
    mkdir -p "${BACKUP_DIR}/$(dirname "${relative}")"
    cp -a -- "${target}" "${BACKUP_DIR}/${relative}"
    printf '%s  %s\n' "$(sha256sum "${target}" | awk '{print $1}')" "${relative}" >>"${BACKUP_DIR}/BEFORE_SHA256.txt"
  else
    printf '%s\n' "${relative}" >>"${BACKUP_DIR}/BEFORE_ABSENT.txt"
  fi
done
for relative in "${FILES[@]}"; do
  target="${SITE}/${relative}"
  install -d -o www -g www -m 0755 "$(dirname "${target}")"
  install -o www -g www -m 0644 "${WORK_DIR}/${relative}" "${target}"
  cmp "${WORK_DIR}/${relative}" "${target}" || fail "install mismatch: ${relative}"
done

find "${SITE}/data/template" -mindepth 1 -maxdepth 1 -type f -delete
HOME_CODE="$(curl -sS -L --max-redirs 5 -o "${WORK_DIR}/home.html" -w '%{http_code}' --resolve "${HOST}:${PORT}:127.0.0.1" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' "http://${HOST}:${PORT}/plugin.php?id=xigua_hb&mobile=2")"
APP_CODE="$(curl -sS -o "${WORK_DIR}/app.html" -w '%{http_code}' --resolve "${HOST}:${PORT}:127.0.0.1" "http://${HOST}:${PORT}/done/app.html")"
[ "${HOME_CODE}" = "200" ] || fail "staging home HTTP ${HOME_CODE}"
[ "${APP_CODE}" = "200" ] || fail "staging app landing HTTP ${APP_CODE}"
POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' -X POST -H "Host: ${HOST}" "http://127.0.0.1:${PORT}/")"
[ "${POST_CODE}" = "405" ] || fail "post-deploy POST guard is ${POST_CODE}"
for relative in "${FILES[@]}"; do
  printf '%s  %s\n' "$(sha256sum "${SITE}/${relative}" | awk '{print $1}')" "${relative}" >>"${BACKUP_DIR}/AFTER_SHA256.txt"
done
chmod -R a-w "${BACKUP_DIR}"

printf '[R08-DEPLOY] PASS\n'
printf '[R08-DEPLOY] DEPLOY_ID=%s ARCHIVE_SHA256=%s FILES=10 HOME=%s APP=%s POST=%s\n' "${DEPLOY_ID}" "${ACTUAL_SHA256}" "${HOME_CODE}" "${APP_CODE}" "${POST_CODE}"
printf '[R08-DEPLOY] BACKUP_DIR=%s\n' "${BACKUP_DIR}"
