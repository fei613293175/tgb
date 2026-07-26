#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

STAGING_SITE="/www/staging/tg-h5-ui-r04/site"
STAGING_PRIVATE="/www/staging/tg-h5-ui-r04/private"
STAGING_HOST="tg-h5-ui-r04.local"
LOOPBACK_PORT="18084"
BASE_URL="http://127.0.0.1:${LOOPBACK_PORT}"
ANDROID_UA="TuiGuangBaoAndroid/1.0.0 Android"

fail() {
  printf '[R04-RUNTIME] FAIL: %s\n' "$1" >&2
  exit 1
}

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${STAGING_SITE}/source/plugin/xigua_hb" ] || fail "R04 staging site is absent"
[ -d "${STAGING_PRIVATE}" ] || fail "R04 private directory is absent"
[ -f "${STAGING_SITE}/__r04_auth__/index.php" ] || fail "authentication bridge is not active"

WORK_DIR="$(mktemp -d "${STAGING_PRIVATE}/r04-runtime.XXXXXX")"
cleanup() {
  rm -rf -- "${WORK_DIR}"
}
trap cleanup EXIT

cat >"${WORK_DIR}/business-counters.php" <<'PHP'
<?php
$_SERVER['HTTP_HOST'] = 'tg-h5-ui-r04.local';
$_SERVER['SERVER_NAME'] = 'tg-h5-ui-r04.local';
$_SERVER['SERVER_PORT'] = '18084';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';
chdir('/www/staging/tg-h5-ui-r04/site');
require '/www/staging/tg-h5-ui-r04/site/source/class/class_core.php';
$discuz = C::app();
$discuz->init();
$counters = array(
    'pub_rows' => intval(DB::result_first('SELECT COUNT(*) FROM %t', array('xigua_hb_pub'))),
    'pub_views' => intval(DB::result_first('SELECT COALESCE(SUM(views), 0) FROM %t', array('xigua_hb_pub'))),
    'pub_votes' => intval(DB::result_first('SELECT COALESCE(SUM(votes), 0) FROM %t', array('xigua_hb_pub'))),
    'vote_log_rows' => intval(DB::result_first('SELECT COUNT(*) FROM %t', array('xigua_hb_votelog'))),
);
ksort($counters);
echo json_encode($counters, JSON_UNESCAPED_SLASHES), "\n";
PHP

php "${WORK_DIR}/business-counters.php" >"${WORK_DIR}/before.json"
grep -Eq '^\{"pub_rows":[0-9]+,"pub_views":[0-9]+,"pub_votes":[0-9]+,"vote_log_rows":[0-9]+\}$' \
  "${WORK_DIR}/before.json" || fail "business counter snapshot is invalid"

COOKIE_JAR="${WORK_DIR}/cookies.txt"
request() {
  local name="$1"
  local url="$2"
  curl -sS -L --max-redirs 5 \
    -c "${COOKIE_JAR}" -b "${COOKIE_JAR}" \
    -H "Host: ${STAGING_HOST}" \
    -H "User-Agent: ${ANDROID_UA}" \
    -o "${WORK_DIR}/${name}.body" \
    -w '%{http_code}' \
    "${url}"
}

HOME_CODE="$(request home "${BASE_URL}/__r04_auth__/")"
[ "${HOME_CODE}" = "200" ] || fail "home HTTP ${HOME_CODE}"
grep -Fq 'discovery-light-grid-r04.css?v=20260726-r04-2' "${WORK_DIR}/home.body" ||
  fail "home R04 CSS link is absent"
grep -Fq 'discovery-r04.js?v=20260726-r04-2' "${WORK_DIR}/home.body" ||
  fail "home R04 script link is absent"
for category_id in 5 14 15 13; do
  grep -Fq "data-id=\"${category_id}\"" "${WORK_DIR}/home.body" ||
    fail "hidden conditional category protocol ${category_id} is absent"
done

SEARCH_POPULATED_CODE="$(request search-populated \
  "${BASE_URL}/plugin.php?id=xigua_hb&ac=cat&keyword=%E9%A1%B9%E7%9B%AE&mobile=2")"
[ "${SEARCH_POPULATED_CODE}" = "200" ] ||
  fail "populated search HTTP ${SEARCH_POPULATED_CODE}"
grep -Fq 'id="list"' "${WORK_DIR}/search-populated.body" ||
  fail "populated search list hook is absent"

SEARCH_EMPTY_CODE="$(request search-empty \
  "${BASE_URL}/plugin.php?id=xigua_hb&ac=cat&keyword=__TGB_R04_NO_RESULT__&mobile=2")"
[ "${SEARCH_EMPTY_CODE}" = "200" ] || fail "empty search HTTP ${SEARCH_EMPTY_CODE}"
grep -Fq 'id="loading-none"' "${WORK_DIR}/search-empty.body" ||
  fail "empty search state hook is absent"

CATEGORY_CODE="$(request category \
  "${BASE_URL}/plugin.php?id=xigua_hb&ac=cat&cat_id=5&mobile=2")"
[ "${CATEGORY_CODE}" = "200" ] || fail "category HTTP ${CATEGORY_CODE}"
grep -Fq 'id="loading-none"' "${WORK_DIR}/category.body" ||
  fail "category empty-state hook is absent"

FILTER_RESULTS=()
for category_id in 5 14 15 13; do
  filter_code="$(request "filter-${category_id}" \
    "${BASE_URL}/plugin.php?id=xigua_hb&ac=list_item&inajax=1&cat_id=${category_id}&page=1")"
  [ "${filter_code}" = "200" ] || fail "category ${category_id} AJAX HTTP ${filter_code}"
  grep -Eq '<root|<!\[CDATA\[' "${WORK_DIR}/filter-${category_id}.body" ||
    fail "category ${category_id} AJAX shape changed"
  FILTER_RESULTS+=("${category_id}:${filter_code}")
done

ARTICLE_CODE="$(request article \
  "${BASE_URL}/plugin.php?id=xigua_hb&ac=article_li&from=index&inajax=1&page=1")"
[ "${ARTICLE_CODE}" = "200" ] || fail "article AJAX HTTP ${ARTICLE_CODE}"
grep -Eq '<root|<!\[CDATA\[' "${WORK_DIR}/article.body" ||
  fail "article AJAX shape changed"
ARTICLE_BYTES="$(wc -c <"${WORK_DIR}/article.body" | tr -d ' ')"
ARTICLE_ENTRIES="$({ grep -o 'class=\"marticle\"' "${WORK_DIR}/article.body" || true; } |
  wc -l | tr -d ' ')"

POST_CODE="$(curl -sS -o /dev/null -w '%{http_code}' -X POST \
  -H "Host: ${STAGING_HOST}" "${BASE_URL}/plugin.php?id=xigua_hb")"
[ "${POST_CODE}" = "405" ] || fail "POST guard HTTP ${POST_CODE}"

php "${WORK_DIR}/business-counters.php" >"${WORK_DIR}/after.json"
cmp "${WORK_DIR}/before.json" "${WORK_DIR}/after.json" ||
  fail "business counters changed during GET regression"

printf '[R04-RUNTIME] PASS\n'
printf '[R04-RUNTIME] HOME=%s SEARCH_POPULATED=%s SEARCH_EMPTY=%s CATEGORY=%s ARTICLE=%s POST=%s\n' \
  "${HOME_CODE}" "${SEARCH_POPULATED_CODE}" "${SEARCH_EMPTY_CODE}" \
  "${CATEGORY_CODE}" "${ARTICLE_CODE}" "${POST_CODE}"
printf '[R04-RUNTIME] HIDDEN_CONDITIONAL_ROUTES=%s ARTICLE_BYTES=%s ARTICLE_ENTRIES=%s\n' \
  "${FILTER_RESULTS[*]}" "${ARTICLE_BYTES}" "${ARTICLE_ENTRIES}"
printf '[R04-RUNTIME] BUSINESS_COUNTERS_UNCHANGED=PASS\n'
