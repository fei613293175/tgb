#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

STAGING_SITE="/www/staging/tg-h5-ui-r03/site"
STAGING_PRIVATE="/www/staging/tg-h5-ui-r03/private"
NGINX_CONFIG="/www/server/panel/vhost/nginx/tg-h5-ui-r03-loopback.conf"
LOOPBACK_PORT="18083"
STAGING_HOST="tg-h5-ui-r03.local"
BASE_URL="http://${STAGING_HOST}:${LOOPBACK_PORT}"
CURL_RESOLVE=(--resolve "${STAGING_HOST}:${LOOPBACK_PORT}:127.0.0.1")
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"
RUN_ID="$(date '+%Y%m%dT%H%M%S%z')"
RUN_DIR="${STAGING_PRIVATE}/controlled-auth/${RUN_ID}"
SECRET_PATH="${RUN_DIR}/fixture-secret.json"
SAFE_RESULT="${RUN_DIR}/RESULT.env"
ACTIVE_CONFIG="${RUN_DIR}/read-only.conf"
WINDOW_OPEN=0
FIXTURE_CREATED=0

fail() {
  printf '[R03-AUTH-TEST] ABORT: %s\n' "$1" >&2
  exit 1
}

probe_post_guard() {
  curl -sS -o /dev/null -w '%{http_code}' -X POST \
    "${CURL_RESOLVE[@]}" \
    -H "Host: ${STAGING_HOST}" \
    -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
    "${BASE_URL}/plugin.php?id=xigua_hb"
}

extract_formhash() {
  local html_path="$1"
  "${PANEL_PYTHON}" - "${html_path}" <<'PY'
from html.parser import HTMLParser
from pathlib import Path
import sys

class FormHashParser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.value = None
    def handle_starttag(self, tag, attrs):
        if tag.lower() != "input":
            return
        values = dict(attrs)
        if values.get("name") == "formhash":
            self.value = values.get("value")

parser = FormHashParser()
parser.feed(Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace"))
if not parser.value or len(parser.value) > 32:
    raise SystemExit("formhash missing")
sys.stdout.write(parser.value)
PY
}

extract_login_action() {
  local html_path="$1"
  "${PANEL_PYTHON}" - "${html_path}" <<'PY'
from html.parser import HTMLParser
from html import unescape
from pathlib import Path
import sys

class LoginFormParser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.action = None
    def handle_starttag(self, tag, attrs):
        if tag.lower() != "form":
            return
        values = dict(attrs)
        if values.get("id") == "loginform":
            self.action = values.get("action")

parser = LoginFormParser()
parser.feed(Path(sys.argv[1]).read_text(encoding="utf-8", errors="replace"))
value = unescape(parser.action or "")
if not value.startswith("member.php?mod=logging&action=login") or len(value) > 512 or "://" in value:
    raise SystemExit("login action invalid")
sys.stdout.write(value)
PY
}

has_auth_cookie() {
  local cookie_jar="$1"
  grep -Eq '[[:space:]][^[:space:]]*auth[[:space:]]' "${cookie_jar}"
}

destroy_fixture() {
  [ "${FIXTURE_CREATED}" -eq 1 ] || return 0
  export TGB_R03_SECRET_PATH="${SECRET_PATH}"
  cd "${STAGING_SITE}"
  php <<'PHP'
<?php
define('CURSCRIPT', 'r03authcleanup');
$_SERVER['PHP_SELF'] = '/r03authcleanup.php';
$_SERVER['SCRIPT_NAME'] = '/r03authcleanup.php';
$_SERVER['SCRIPT_FILENAME'] = '/www/staging/tg-h5-ui-r03/site/r03authcleanup.php';
$_SERVER['REQUEST_URI'] = '/r03authcleanup.php';
$_SERVER['HTTP_HOST'] = 'tg-h5-ui-r03.local';
$_SERVER['SERVER_NAME'] = 'tg-h5-ui-r03.local';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
chdir('/www/staging/tg-h5-ui-r03/site');
require '/www/staging/tg-h5-ui-r03/site/source/class/class_core.php';
$discuz = C::app();
$discuz->init();
loaducenter();

$secretPath = getenv('TGB_R03_SECRET_PATH');
$secret = json_decode(file_get_contents($secretPath), true);
if (!is_array($secret) || empty($secret['username']) || empty($secret['phone'])) {
    fwrite(STDERR, "[R03-AUTH-TEST] cleanup secret invalid\n");
    exit(71);
}
$member = C::t('common_member')->fetch_by_username($secret['username']);
if ($member && !empty($member['uid'])) {
    $uid = (int) $member['uid'];
    DB::delete('tb_cus_mobilereg_userphone', array('uid' => $uid));
    DB::delete('tb_cus_mobilereg_code', array('mobile' => $secret['phone']));
    C::t('common_member')->delete_no_validate($uid);
    uc_user_delete($uid);
}
if (C::t('common_member')->fetch_by_username($secret['username']) ||
    uc_get_user($secret['username']) ||
    DB::result_first('SELECT COUNT(*) FROM %t WHERE mobile=%s', array('tb_cus_mobilereg_userphone', $secret['phone'])) ||
    DB::result_first('SELECT COUNT(*) FROM %t WHERE mobile=%s', array('tb_cus_mobilereg_code', $secret['phone']))) {
    fwrite(STDERR, "[R03-AUTH-TEST] fixture cleanup verification failed\n");
    exit(72);
}
unlink($secretPath);
echo "[R03-AUTH-TEST] FIXTURE_REMOVED parity=PASS\n";
PHP
  FIXTURE_CREATED=0
}

restore_window() {
  [ "${WINDOW_OPEN}" -eq 1 ] || return 0
  cp -a "${ACTIVE_CONFIG}" "${NGINX_CONFIG}"
  nginx -t >/dev/null
  nginx -s reload
  sleep 1
  local code
  code="$(probe_post_guard)"
  [ "${code}" = "405" ] || fail "POST guard restore failed with HTTP ${code}"
  WINDOW_OPEN=0
  printf '[R03-AUTH-TEST] POST_RESTORED=%s\n' "${code}"
}

cleanup() {
  local status=$?
  set +e
  restore_window
  local restore_status=$?
  destroy_fixture
  local fixture_status=$?
  if [ "${status}" -ne 0 ] || [ "${restore_status}" -ne 0 ] || [ "${fixture_status}" -ne 0 ]; then
    exit 1
  fi
}
trap cleanup EXIT

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${STAGING_SITE}/source/plugin/tb_cus_mobilereg" ] || fail "R03 staging plugin is absent"
[ -d "${STAGING_PRIVATE}" ] || fail "R03 private directory is absent"
[ -f "${NGINX_CONFIG}" ] || fail "R03 Nginx config is absent"
[ -x "${PANEL_PYTHON}" ] || fail "panel Python is unavailable"
[ "$(probe_post_guard)" = "405" ] || fail "POST guard is not initially closed"
[ ! -e "${RUN_DIR}" ] || fail "run directory already exists"
install -d -m 0700 "${RUN_DIR}"
cp -a "${NGINX_CONFIG}" "${ACTIVE_CONFIG}"
sha256sum "${NGINX_CONFIG}" >"${RUN_DIR}/BEFORE_NGINX_SHA256.txt"

export TGB_R03_SECRET_PATH="${SECRET_PATH}"
cd "${STAGING_SITE}"
php <<'PHP'
<?php
define('CURSCRIPT', 'r03authfixture');
$_SERVER['PHP_SELF'] = '/r03authfixture.php';
$_SERVER['SCRIPT_NAME'] = '/r03authfixture.php';
$_SERVER['SCRIPT_FILENAME'] = '/www/staging/tg-h5-ui-r03/site/r03authfixture.php';
$_SERVER['REQUEST_URI'] = '/r03authfixture.php';
$_SERVER['HTTP_HOST'] = 'tg-h5-ui-r03.local';
$_SERVER['SERVER_NAME'] = 'tg-h5-ui-r03.local';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
chdir('/www/staging/tg-h5-ui-r03/site');
require '/www/staging/tg-h5-ui-r03/site/source/class/class_core.php';
$discuz = C::app();
$discuz->init();
loaducenter();

$secretPath = getenv('TGB_R03_SECRET_PATH');
$username = 'tgr3_' . bin2hex(random_bytes(4));
$password = bin2hex(random_bytes(24));
$phone = '199' . (string) random_int(10000000, 99999999);
$smsCode = (string) random_int(10000000, 99999999);
$email = $username . '@example.com';
$memberCountBefore = (int) DB::result_first('SELECT COUNT(*) FROM %t', array('common_member'));
if (C::t('common_member')->fetch_by_username($username) || uc_get_user($username)) {
    throw new RuntimeException('fixture collision');
}
$uid = uc_user_register(addslashes($username), $password, $email, 0, '', '127.0.0.1');
if ($uid <= 0) {
    fwrite(STDERR, "[R03-AUTH-TEST] UCenter fixture create failed category={$uid}\n");
    exit(74);
}
try {
    $groupId = !empty($_G['setting']['regverify']) ? 8 : (int) $_G['setting']['newusergroupid'];
    C::t('common_member')->insert($uid, $username, md5(random(10)), $email, '127.0.0.1', $groupId, array(
        'credits' => explode(',', (string) $_G['setting']['initcredits']),
        'profile' => array(),
        'emailstatus' => 0,
    ));
    DB::insert('tb_cus_mobilereg_userphone', array('uid' => $uid, 'mobile' => $phone));
    DB::insert('tb_cus_mobilereg_code', array(
        'mobile' => $phone,
        'code' => $smsCode,
        'dateline' => TIMESTAMP,
        'ip' => '127.0.0.1',
        'ostatus' => 1,
    ));
    $secret = array(
        'username' => $username,
        'password' => $password,
        'phone' => $phone,
        'sms_code' => $smsCode,
        'member_count_before' => $memberCountBefore,
    );
    file_put_contents($secretPath, json_encode($secret, JSON_UNESCAPED_SLASHES) . "\n");
    chmod($secretPath, 0600);
    if (!C::t('common_member')->fetch($uid) || !uc_get_user($username) ||
        !DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d AND mobile=%s', array('tb_cus_mobilereg_userphone', $uid, $phone)) ||
        !DB::result_first('SELECT COUNT(*) FROM %t WHERE mobile=%s AND code=%s AND ostatus=1', array('tb_cus_mobilereg_code', $phone, $smsCode))) {
        throw new RuntimeException('fixture parity failed');
    }
    echo "[R03-AUTH-TEST] FIXTURE_CREATED core=PASS phone=PASS sms_mock=PASS\n";
} catch (Throwable $error) {
    DB::delete('tb_cus_mobilereg_userphone', array('uid' => $uid));
    DB::delete('tb_cus_mobilereg_code', array('mobile' => $phone));
    if (C::t('common_member')->fetch($uid)) {
        C::t('common_member')->delete_no_validate($uid);
    }
    uc_user_delete($uid);
    fwrite(STDERR, "[R03-AUTH-TEST] fixture create rolled back\n");
    exit(73);
}
PHP
[ -f "${SECRET_PATH}" ] || fail "fixture secret was not created"
FIXTURE_CREATED=1

"${PANEL_PYTHON}" - "${NGINX_CONFIG}" <<'PY'
from pathlib import Path
import sys

path = Path(sys.argv[1])
text = path.read_text(encoding="utf-8")
read_only = """    if ($request_method !~ ^(GET|HEAD)$) {
        return 405;
    }"""
controlled = """    # R03 controlled authentication window; restored by script trap.
    if ($request_method !~ ^(GET|HEAD|POST)$) {
        return 405;
    }"""
if text.count(read_only) != 1:
    raise SystemExit("read-only method guard not found exactly once")
with path.open("w", encoding="utf-8", newline="\n") as handle:
    handle.write(text.replace(read_only, controlled))
PY
if ! nginx -t >/dev/null; then
  cp -a "${ACTIVE_CONFIG}" "${NGINX_CONFIG}"
  nginx -t >/dev/null
  fail "controlled Nginx config invalid"
fi
nginx -s reload
sleep 1
WINDOW_OPEN=1
OPEN_POST_CODE="$(probe_post_guard)"
[ "${OPEN_POST_CODE}" != "405" ] || fail "controlled POST window did not open"
printf '[R03-AUTH-TEST] POST_WINDOW_HTTP=%s\n' "${OPEN_POST_CODE}"

export SECRET_PATH
eval "$("${PANEL_PYTHON}" - <<'PY'
import json, os, shlex
data = json.load(open(os.environ['SECRET_PATH'], encoding='utf-8'))
for key in ('username', 'password', 'phone', 'sms_code'):
    print(key.upper() + '=' + shlex.quote(data[key]))
PY
)"

LOGIN_HTML="${RUN_DIR}/login.html"
LOGIN_JAR="${RUN_DIR}/login.cookies"
curl -sS -L --max-redirs 5 "${CURL_RESOLVE[@]}" -c "${LOGIN_JAR}" -b "${LOGIN_JAR}" \
  -H "Host: ${STAGING_HOST}" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  "${BASE_URL}/plugin.php?id=xigua_hb" >"${LOGIN_HTML}"
FORMHASH="$(extract_formhash "${LOGIN_HTML}")"
LOGIN_ACTION="$(extract_login_action "${LOGIN_HTML}")"
curl -sS "${CURL_RESOLVE[@]}" -c "${LOGIN_JAR}" -b "${LOGIN_JAR}" -o "${RUN_DIR}/login-success.html" \
  -H "Host: ${STAGING_HOST}" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  --data-urlencode "formhash=${FORMHASH}" \
  --data-urlencode 'referer=forum.php?mobile=2' \
  --data-urlencode 'fastloginfield=username' \
  --data-urlencode 'cookietime=2592000' \
  --data-urlencode "username=${USERNAME}" \
  --data-urlencode "password=${PASSWORD}" \
  "${BASE_URL}/${LOGIN_ACTION}"
has_auth_cookie "${LOGIN_JAR}" || fail "password login did not issue auth cookie"
curl -sS -L --max-redirs 5 "${CURL_RESOLVE[@]}" -c "${LOGIN_JAR}" -b "${LOGIN_JAR}" -o "${RUN_DIR}/login-home.html" \
  -H "Host: ${STAGING_HOST}" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  "${BASE_URL}/plugin.php?id=xigua_hb&mobile=2"
if grep -Fq '推广宝 - 登录账号' "${RUN_DIR}/login-home.html"; then
  fail "password login remained on public login page"
fi

FAIL_HTML="${RUN_DIR}/login-failure-form.html"
FAIL_JAR="${RUN_DIR}/login-failure.cookies"
curl -sS -L --max-redirs 5 "${CURL_RESOLVE[@]}" -c "${FAIL_JAR}" -b "${FAIL_JAR}" \
  -H "Host: ${STAGING_HOST}" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  "${BASE_URL}/plugin.php?id=xigua_hb" >"${FAIL_HTML}"
FAIL_FORMHASH="$(extract_formhash "${FAIL_HTML}")"
FAIL_LOGIN_ACTION="$(extract_login_action "${FAIL_HTML}")"
curl -sS "${CURL_RESOLVE[@]}" -c "${FAIL_JAR}" -b "${FAIL_JAR}" -o "${RUN_DIR}/login-failure.html" \
  -H "Host: ${STAGING_HOST}" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  --data-urlencode "formhash=${FAIL_FORMHASH}" \
  --data-urlencode 'fastloginfield=username' \
  --data-urlencode "username=${USERNAME}" \
  --data-urlencode 'password=definitely-wrong-r03' \
  "${BASE_URL}/${FAIL_LOGIN_ACTION}"
if has_auth_cookie "${FAIL_JAR}"; then fail "wrong password issued auth cookie"; fi

EMPTY_JAR="${RUN_DIR}/login-empty.cookies"
curl -sS "${CURL_RESOLVE[@]}" -c "${EMPTY_JAR}" -b "${EMPTY_JAR}" -o "${RUN_DIR}/login-empty.html" \
  -H "Host: ${STAGING_HOST}" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  --data-urlencode "formhash=${FAIL_FORMHASH}" \
  --data-urlencode 'fastloginfield=username' \
  --data-urlencode 'username=' --data-urlencode 'password=' \
  "${BASE_URL}/${FAIL_LOGIN_ACTION}"
if has_auth_cookie "${EMPTY_JAR}"; then fail "empty login issued auth cookie"; fi

SMS_HTML="${RUN_DIR}/sms.html"
SMS_JAR="${RUN_DIR}/sms.cookies"
curl -sS "${CURL_RESOLVE[@]}" -c "${SMS_JAR}" -b "${SMS_JAR}" \
  -H "Host: ${STAGING_HOST}" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  "${BASE_URL}/plugin.php?id=tb_cus_mobilereg:mobilelogin" >"${SMS_HTML}"
SMS_FORMHASH="$(extract_formhash "${SMS_HTML}")"
curl -sS "${CURL_RESOLVE[@]}" -c "${SMS_JAR}" -b "${SMS_JAR}" -o "${RUN_DIR}/sms-wrong.html" \
  -H "Host: ${STAGING_HOST}" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  --data-urlencode "formhash=${SMS_FORMHASH}" --data-urlencode 'action=login' \
  --data-urlencode "pnumber=${PHONE}" --data-urlencode 'mobilecode=00000000' \
  "${BASE_URL}/plugin.php?id=tb_cus_mobilereg:mobilelogin"
if has_auth_cookie "${SMS_JAR}"; then fail "wrong SMS code issued auth cookie"; fi
grep -Fq '手机验证码错误' "${RUN_DIR}/sms-wrong.html" || fail "wrong SMS code state missing"
curl -sS "${CURL_RESOLVE[@]}" -c "${SMS_JAR}" -b "${SMS_JAR}" -o "${RUN_DIR}/sms-success.html" \
  -H "Host: ${STAGING_HOST}" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  --data-urlencode "formhash=${SMS_FORMHASH}" --data-urlencode 'action=login' \
  --data-urlencode "pnumber=${PHONE}" --data-urlencode "mobilecode=${SMS_CODE}" \
  "${BASE_URL}/plugin.php?id=tb_cus_mobilereg:mobilelogin"
has_auth_cookie "${SMS_JAR}" || fail "SMS login did not issue auth cookie"

REGISTER_HTML="${RUN_DIR}/register.html"
REGISTER_JAR="${RUN_DIR}/register.cookies"
curl -sS "${CURL_RESOLVE[@]}" -c "${REGISTER_JAR}" -b "${REGISTER_JAR}" \
  -H "Host: ${STAGING_HOST}" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  "${BASE_URL}/member.php?mod=register&mobile=2" >"${REGISTER_HTML}"
REGISTER_FORMHASH="$(extract_formhash "${REGISTER_HTML}")"
curl -sS "${CURL_RESOLVE[@]}" -c "${REGISTER_JAR}" -b "${REGISTER_JAR}" -o "${RUN_DIR}/register-empty.html" \
  -H "Host: ${STAGING_HOST}" -H 'User-Agent: TuiGuangBaoAndroid/1.0.0 Android' \
  --data-urlencode 'regsubmit=yes' --data-urlencode "formhash=${REGISTER_FORMHASH}" \
  --data-urlencode 'agreebbrule=' \
  "${BASE_URL}/member.php?mod=register&mobile=2"
if has_auth_cookie "${REGISTER_JAR}"; then fail "empty registration issued auth cookie"; fi

export TGB_R03_SECRET_PATH="${SECRET_PATH}"
export TGB_R03_DB_VERIFIED_MARKER="${RUN_DIR}/DB_VERIFIED.marker"
cd "${STAGING_SITE}"
php <<'PHP'
<?php
define('CURSCRIPT', 'r03authverify');
$_SERVER['PHP_SELF'] = '/r03authverify.php';
$_SERVER['SCRIPT_NAME'] = '/r03authverify.php';
$_SERVER['SCRIPT_FILENAME'] = '/www/staging/tg-h5-ui-r03/site/r03authverify.php';
$_SERVER['REQUEST_URI'] = '/r03authverify.php';
$_SERVER['HTTP_HOST'] = 'tg-h5-ui-r03.local';
$_SERVER['SERVER_NAME'] = 'tg-h5-ui-r03.local';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
chdir('/www/staging/tg-h5-ui-r03/site');
require '/www/staging/tg-h5-ui-r03/site/source/class/class_core.php';
$discuz = C::app();
$discuz->init();
$secret = json_decode(file_get_contents(getenv('TGB_R03_SECRET_PATH')), true);
$markerPath = getenv('TGB_R03_DB_VERIFIED_MARKER');
$member = C::t('common_member')->fetch_by_username($secret['username']);
$phoneRows = DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d AND mobile=%s', array('tb_cus_mobilereg_userphone', $member['uid'], $secret['phone']));
$usedCodes = DB::result_first('SELECT COUNT(*) FROM %t WHERE mobile=%s AND code=%s AND ostatus=2', array('tb_cus_mobilereg_code', $secret['phone'], $secret['sms_code']));
$fixtureUsers = DB::result_first('SELECT COUNT(*) FROM %t WHERE username LIKE %s', array('common_member', 'tgr3_%'));
$memberCount = DB::result_first('SELECT COUNT(*) FROM %t', array('common_member'));
if (!$member || !$phoneRows || (int) $usedCodes !== 1 || (int) $fixtureUsers !== 1 ||
    (int) $memberCount !== ((int) $secret['member_count_before'] + 1)) {
    fwrite(STDERR, "[R03-AUTH-TEST] database assertions failed\n");
    exit(81);
}
file_put_contents($markerPath, "PASS\n");
chmod($markerPath, 0600);
echo "[R03-AUTH-TEST] DB_ASSERTIONS member=1 phone=1 sms_used=1 no_extra_registration=PASS\n";
PHP
[ -f "${RUN_DIR}/DB_VERIFIED.marker" ] || fail "database verification marker is absent"

cat >"${SAFE_RESULT}" <<RESULT
run_id=${RUN_ID}
password_login_success=PASS
password_login_wrong=PASS
password_login_empty=PASS
sms_outbound=MOCKED_BY_FIXTURE_CODE
sms_login_wrong=PASS
sms_login_success=PASS
registration_empty_no_create=PASS
production_touched=NO
RESULT
chmod 0600 "${SAFE_RESULT}"

restore_window
destroy_fixture
sha256sum "${NGINX_CONFIG}" >"${RUN_DIR}/RESTORED_NGINX_SHA256.txt"
if ! cmp -s "${RUN_DIR}/BEFORE_NGINX_SHA256.txt" "${RUN_DIR}/RESTORED_NGINX_SHA256.txt"; then
  fail "restored Nginx hash differs"
fi
chmod -R a-w "${RUN_DIR}"
trap - EXIT

printf '[R03-AUTH-TEST] PASS\n'
printf '[R03-AUTH-TEST] RUN_ID=%s\n' "${RUN_ID}"
printf '[R03-AUTH-TEST] PASSWORD=success,wrong,empty SMS=mock-success,mock-wrong REGISTER=empty-no-create\n'
printf '[R03-AUTH-TEST] POST=405 FIXTURE=REMOVED\n'
