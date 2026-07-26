#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

STAGING_BASE="/www/staging/tg-h5-ui-r04"
STAGING_SITE="${STAGING_BASE}/site"
STAGING_PRIVATE="${STAGING_BASE}/private"
STAGING_HOST="tg-h5-ui-r04.local"
LOOPBACK_PORT="18084"
ARCHIVE_PATH="${1:-}"
EXPECTED_SHA256="${2:-}"

EXPECTED_FILES=(
  "source/plugin/xigua_hb/static/tgb-r04/discovery-light-grid-r04.css"
  "source/plugin/xigua_hb/static/tgb-r04/discovery-r04.js"
  "source/plugin/xigua_hb/template/touch/cat.php"
  "source/plugin/xigua_hb/template/touch/index.php"
  "source/plugin/xigua_hb/template/touch/tab1.php"
)

fail() {
  printf '[R04-DEPLOY] ABORT: %s\n' "$1" >&2
  exit 1
}

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${STAGING_SITE}/source/plugin/xigua_hb" ] || fail "R04 staging site is absent"
[ -d "${STAGING_PRIVATE}" ] || fail "R04 private directory is absent"
[ -d "${STAGING_SITE}/data/template" ] || fail "Discuz template cache is absent"
[ -w "${STAGING_SITE}/data/template" ] || fail "Discuz template cache is not writable"
[ -f "${STAGING_SITE}/__r04_auth__/index.php" ] || fail "loopback authentication bridge is not active"
[ -f "${ARCHIVE_PATH}" ] || fail "overlay archive is absent"
[[ "${EXPECTED_SHA256}" =~ ^[0-9a-f]{64}$ ]] || fail "expected SHA-256 is invalid"

PRE_POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' -X POST \
  -H "Host: ${STAGING_HOST}" "http://127.0.0.1:${LOOPBACK_PORT}/")"
[ "${PRE_POST_CODE}" = "405" ] || fail "pre-deploy POST guard is ${PRE_POST_CODE}"

ACTUAL_SHA256="$(sha256sum "${ARCHIVE_PATH}" | awk '{print $1}')"
[ "${ACTUAL_SHA256}" = "${EXPECTED_SHA256}" ] || fail "overlay archive SHA-256 mismatch"

WORK_DIR="$(mktemp -d "${STAGING_PRIVATE}/r04-deploy-work.XXXXXX")"
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
  "source/plugin/xigua_hb/template/touch/index.php" \
  "source/plugin/xigua_hb/template/touch/cat.php" \
  "source/plugin/xigua_hb/template/touch/tab1.php"; do
  php -l "${WORK_DIR}/${template}" >/dev/null
done

CSS_RELATIVE="source/plugin/xigua_hb/static/tgb-r04/discovery-light-grid-r04.css"
CSS_LINK="source/plugin/xigua_hb/static/tgb-r04/discovery-light-grid-r04.css?v=20260726-r04-2"
JS_RELATIVE="source/plugin/xigua_hb/static/tgb-r04/discovery-r04.js"
JS_LINK="source/plugin/xigua_hb/static/tgb-r04/discovery-r04.js?v=20260726-r04-2"
grep -Fq 'Light Grid R04 discovery pages' "${WORK_DIR}/${CSS_RELATIVE}" ||
  fail "R04 CSS marker is absent"
grep -Fq "${CSS_LINK}" \
  "${WORK_DIR}/source/plugin/xigua_hb/template/touch/index.php" ||
  fail "home R04 CSS link is absent"
grep -Fq "${CSS_LINK}" \
  "${WORK_DIR}/source/plugin/xigua_hb/template/touch/cat.php" ||
  fail "category R04 CSS link is absent"
grep -Fq "${JS_LINK}" \
  "${WORK_DIR}/source/plugin/xigua_hb/template/touch/index.php" ||
  fail "home R04 script link is absent"
grep -Fq "${JS_LINK}" \
  "${WORK_DIR}/source/plugin/xigua_hb/template/touch/cat.php" ||
  fail "category R04 script link is absent"
grep -Fq "noMore.classList.remove('hidden')" "${WORK_DIR}/${JS_RELATIVE}" ||
  fail "empty-state fallback is absent"
if grep -Eqi 'XMLHttpRequest|\bfetch[[:space:]]*\(|\.ajax[[:space:]]*\(' \
  "${WORK_DIR}/${JS_RELATIVE}"; then
  fail "R04 visual helper issues a network request"
fi
grep -Fq 'if (!tabs.length || !indicator || !activeTab)' \
  "${WORK_DIR}/source/plugin/xigua_hb/template/touch/index.php" ||
  fail "home indicator guard is absent"
if grep -Fq 'if (href && currentPath.includes' \
  "${WORK_DIR}/source/plugin/xigua_hb/template/touch/tab1.php"; then
  fail "orphan bottom-navigation matcher remains"
fi

if grep -Eqi 'cdn\.tailwindcss\.com|cdn\.jsdelivr\.net|cdnjs\.cloudflare\.com|unpkg\.com|fonts\.googleapis\.com' \
  "${WORK_DIR}/${CSS_RELATIVE}"; then
  fail "public UI CDN remains in R04 CSS"
fi

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

COOKIE_JAR="${WORK_DIR}/cookies.txt"
AUTH_CODE="$(curl -sS -L --max-redirs 5 -c "${COOKIE_JAR}" -b "${COOKIE_JAR}" \
  -o "${WORK_DIR}/home.html" -w '%{http_code}' \
  -H "Host: ${STAGING_HOST}" \
  -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  "http://127.0.0.1:${LOOPBACK_PORT}/__r04_auth__/")"
[ "${AUTH_CODE}" = "200" ] || fail "authenticated home HTTP ${AUTH_CODE}"
grep -Fq "${CSS_LINK}" "${WORK_DIR}/home.html" || fail "home runtime CSS link is absent"
grep -Fq "${JS_LINK}" "${WORK_DIR}/home.html" || fail "home runtime script link is absent"
grep -Fq 'name="keyword"' "${WORK_DIR}/home.html" || fail "home search field is absent"

CAT_CODE="$(curl -sS -L --max-redirs 5 -b "${COOKIE_JAR}" \
  -o "${WORK_DIR}/cat.html" -w '%{http_code}' \
  -H "Host: ${STAGING_HOST}" \
  -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  "http://127.0.0.1:${LOOPBACK_PORT}/plugin.php?id=xigua_hb&ac=cat&cat_id=5&mobile=2")"
[ "${CAT_CODE}" = "200" ] || fail "category HTTP ${CAT_CODE}"
grep -Fq "${CSS_LINK}" "${WORK_DIR}/cat.html" || fail "category runtime CSS link is absent"
grep -Fq "${JS_LINK}" "${WORK_DIR}/cat.html" || fail "category runtime script link is absent"
grep -Fq 'ac=list_item&amp;inajax=1&amp;pagesize=20&amp;page=' "${WORK_DIR}/cat.html" ||
  grep -Fq 'ac=list_item&inajax=1&pagesize=20&page=' "${WORK_DIR}/cat.html" ||
  fail "category list AJAX route is absent"

ARTICLE_CODE="$(curl -sS -b "${COOKIE_JAR}" -o "${WORK_DIR}/article.xml" -w '%{http_code}' \
  -H "Host: ${STAGING_HOST}" \
  -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  "http://127.0.0.1:${LOOPBACK_PORT}/plugin.php?id=xigua_hb&ac=article_li&from=index&inajax=1&page=1")"
[ "${ARTICLE_CODE}" = "200" ] || fail "article-list AJAX HTTP ${ARTICLE_CODE}"
grep -Eq '<root|<!\[CDATA\[' "${WORK_DIR}/article.xml" || fail "article-list AJAX shape changed"

ASSET_HEADERS="${WORK_DIR}/asset.headers"
ASSET_CODE="$(curl -sS -D "${ASSET_HEADERS}" -o "${WORK_DIR}/asset.css" -w '%{http_code}' \
  -H "Host: ${STAGING_HOST}" \
  "http://127.0.0.1:${LOOPBACK_PORT}/${CSS_RELATIVE}")"
[ "${ASSET_CODE}" = "200" ] || fail "R04 CSS HTTP ${ASSET_CODE}"
grep -Fq 'Light Grid R04 discovery pages' "${WORK_DIR}/asset.css" ||
  fail "R04 CSS returned fallback content"
grep -Eqi '^Content-Type:[[:space:]]*text/css' "${ASSET_HEADERS}" ||
  fail "R04 CSS MIME mismatch"

JS_HEADERS="${WORK_DIR}/script.headers"
JS_CODE="$(curl -sS -D "${JS_HEADERS}" -o "${WORK_DIR}/script.js" -w '%{http_code}' \
  -H "Host: ${STAGING_HOST}" \
  "http://127.0.0.1:${LOOPBACK_PORT}/${JS_RELATIVE}")"
[ "${JS_CODE}" = "200" ] || fail "R04 script HTTP ${JS_CODE}"
grep -Fq "noMore.classList.remove('hidden')" "${WORK_DIR}/script.js" ||
  fail "R04 script returned fallback content"
grep -Eqi '^Content-Type:[[:space:]]*(application|text)/(javascript|x-javascript)' "${JS_HEADERS}" ||
  fail "R04 script MIME mismatch"

POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' -X POST \
  -H "Host: ${STAGING_HOST}" "http://127.0.0.1:${LOOPBACK_PORT}/")"
[ "${POST_CODE}" = "405" ] || fail "post-deploy POST guard is ${POST_CODE}"

printf '[R04-DEPLOY] PASS\n'
printf '[R04-DEPLOY] DEPLOY_ID=%s\n' "${DEPLOY_ID}"
printf '[R04-DEPLOY] ARCHIVE_SHA256=%s\n' "${ACTUAL_SHA256}"
printf '[R04-DEPLOY] FILES=%s HOME=%s CAT=%s ARTICLE=%s POST=%s\n' \
  "${#EXPECTED_FILES[@]}" "${AUTH_CODE}" "${CAT_CODE}" "${ARTICLE_CODE}" "${POST_CODE}"
printf '[R04-DEPLOY] BACKUP_DIR=%s\n' "${BACKUP_DIR}"
