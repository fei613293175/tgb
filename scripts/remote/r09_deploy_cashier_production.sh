#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
ARCHIVE="${2:-/tmp/r09-cashier-overlay.tar.gz}"
EXPECTED_ARCHIVE_SHA="${3:-}"
ROLLBACK_ID="${4:-}"
SITE="/www/wwwroot/tg.suewammes.com"
BACKUP_ROOT="/www/staging/tg-h5-ui-r08/private/production-cashier-backups"
TEMPLATE_REL="source/plugin/tb_pay/template/touch/main.htm"
CSS_REL="source/plugin/tb_pay/static/tgb-r09/cashier-light-grid-r09.css"
TRANSFORM_REL="tools/r09_transform_cashier_template.py"
EXPECTED_TEMPLATE_SHA="ccae4d5f80d1c8f7ff71803c99ddd01644a76601c4c588acededdd78cd65fc82"
EXPECTED_OUTPUT_SHA="83661a7871894331bdf6f6543f792c236e871f3267ac787291eaab80e70abe86"
EXPECTED_CSS_SHA="0345def448c65a92a147bf94ceefbb6f2aa32b7762c8ffc0f439e50b80eb523c"
PYTHON="/www/server/panel/pyenv/bin/python3"
EXPECTED_TB_PAY_INC_SHA="7754c28fbe2d251b5ab305f3c65fc05ddff4fbad7f34f84e01db13024d9fea64"
EXPECTED_MAIN_PHP_SHA="835aff76bc6c85af3fce71683cfce8f488464b06fa903b8db00f4364db4257af"
EXPECTED_PAY_PHP_SHA="d0b19011633ef1a7619c7daf2eba5ef01db1b3b2839a7394b3a338e7aa7e7d4e"
TMP=""
BACKUP=""
DEPLOYED=0

fail() { printf '[R09-CASHIER-PRODUCTION] ABORT: %s\n' "$1" >&2; exit 1; }
cleanup() { [ -z "${TMP}" ] || rm -rf -- "${TMP}"; }
sha() { sha256sum "$1" | awk '{print $1}'; }

assert_site() {
  [ "$(id -u)" -eq 0 ] || fail 'root is required'
  [ -d "${SITE}/source/plugin/tb_pay" ] || fail 'production tb_pay plugin is absent'
  [ -x "${PYTHON}" ] || fail 'panel Python runtime is unavailable'
  grep -Fq 'tg_suewammes_com' "${SITE}/config/config_global.php" || fail 'not the production database configuration'
  [ "$(sha "${SITE}/source/plugin/tb_pay/tb_pay.inc.php")" = "${EXPECTED_TB_PAY_INC_SHA}" ] || fail 'tb_pay entry controller hash drift'
  [ "$(sha "${SITE}/source/plugin/tb_pay/module/main.php")" = "${EXPECTED_MAIN_PHP_SHA}" ] || fail 'cashier controller hash drift'
  [ "$(sha "${SITE}/source/plugin/tb_pay/module/pay.php")" = "${EXPECTED_PAY_PHP_SHA}" ] || fail 'payment controller hash drift'
}

verify_archive() {
  [ -n "${EXPECTED_ARCHIVE_SHA}" ] || fail 'expected archive SHA-256 is required'
  [ -f "${ARCHIVE}" ] || fail 'overlay archive is missing'
  local actual expected_list actual_list
  actual="$(sha "${ARCHIVE}")"
  [ "${actual}" = "${EXPECTED_ARCHIVE_SHA,,}" ] || fail 'archive SHA-256 mismatch'
  expected_list="$(printf '%s\n' "${CSS_REL}" "${TRANSFORM_REL}")"
  actual_list="$(tar -tzf "${ARCHIVE}" | sed 's#^\./##' | sed '/\/$/d' | sort)"
  [ "${actual_list}" = "${expected_list}" ] || fail 'archive file list is not the two-file minimal overlay'
  if tar -tzf "${ARCHIVE}" | grep -Eq '(^/|(^|/)\.\.(/|$))'; then
    fail 'archive contains an unsafe path'
  fi
  printf '%s' "${actual}"
}

rollback_on_error() {
  local code=$?
  if [ "${DEPLOYED}" -eq 1 ] && [ -n "${BACKUP}" ] && [ -f "${BACKUP}/files/${TEMPLATE_REL}" ]; then
    install -o www -g www -m 0777 "${BACKUP}/files/${TEMPLATE_REL}" "${SITE}/${TEMPLATE_REL}"
    rm -f -- "${SITE}/${CSS_REL}" "${SITE}/data/template/1_tb_pay_main.tpl.php"
    printf '[R09-CASHIER-PRODUCTION] AUTO_ROLLBACK=COMPLETE\n' >&2
  fi
  cleanup
  exit "${code}"
}
trap rollback_on_error ERR
trap cleanup EXIT

case "${MODE}" in
  --verify-only)
    assert_site
    actual_archive_sha="$(verify_archive)"
    current_template_sha="$(sha "${SITE}/${TEMPLATE_REL}")"
    if [ "${current_template_sha}" = "${EXPECTED_TEMPLATE_SHA}" ]; then
      [ ! -e "${SITE}/${CSS_REL}" ] || fail 'production cashier CSS exists against the baseline template'
      current_state="PREDEPLOY_BASELINE"
    elif [ "${current_template_sha}" = "${EXPECTED_OUTPUT_SHA}" ]; then
      [ -f "${SITE}/${CSS_REL}" ] || fail 'deployed cashier CSS is missing'
      [ "$(sha "${SITE}/${CSS_REL}")" = "${EXPECTED_CSS_SHA}" ] || fail 'deployed cashier CSS hash drift'
      current_state="DEPLOYED_VERIFIED"
    else
      fail 'production cashier template state is unknown'
    fi
    printf '[R09-CASHIER-PRODUCTION] ARCHIVE_SHA256=%s\n' "${actual_archive_sha}"
    printf '[R09-CASHIER-PRODUCTION] CURRENT_STATE=%s\n' "${current_state}"
    printf '[R09-CASHIER-PRODUCTION] CORE_PAYMENT_HASHES=PASS\n'
    printf '[R09-CASHIER-PRODUCTION] PREFLIGHT=PASS\n'
    ;;
  --apply-production)
    assert_site
    actual_archive_sha="$(verify_archive)"
    [ "$(sha "${SITE}/${TEMPLATE_REL}")" = "${EXPECTED_TEMPLATE_SHA}" ] || fail 'production cashier template predeploy hash drift'
    [ ! -e "${SITE}/${CSS_REL}" ] || fail 'production cashier CSS already exists'
    TMP="$(mktemp -d /tmp/r09-cashier-production.XXXXXX)"
    tar -xzf "${ARCHIVE}" -C "${TMP}"
    "${PYTHON}" "${TMP}/${TRANSFORM_REL}" "${SITE}/${TEMPLATE_REL}" "${TMP}/cashier-main.htm"
    deploy_id="$(date '+%Y%m%dT%H%M%S%z')"
    BACKUP="${BACKUP_ROOT}/${deploy_id}"
    [ ! -e "${BACKUP}" ] || fail 'production backup path already exists'
    install -d -m 0700 "${BACKUP}/files/$(dirname "${TEMPLATE_REL}")"
    cp -a -- "${SITE}/${TEMPLATE_REL}" "${BACKUP}/files/${TEMPLATE_REL}"
    printf 'css_predeploy=absent\n' >"${BACKUP}/STATE.env"
    install -d -o www -g www -m 0755 "$(dirname "${SITE}/${CSS_REL}")"
    install -o www -g www -m 0777 "${TMP}/cashier-main.htm" "${SITE}/${TEMPLATE_REL}"
    install -o www -g www -m 0644 "${TMP}/${CSS_REL}" "${SITE}/${CSS_REL}"
    rm -f -- "${SITE}/data/template/1_tb_pay_main.tpl.php"
    DEPLOYED=1
    grep -Fq '<title>推广宝收银台</title>' "${SITE}/${TEMPLATE_REL}" || fail 'production cashier brand title missing'
    [ "$(sha "${SITE}/${TEMPLATE_REL}")" = "${EXPECTED_OUTPUT_SHA}" ] || fail 'production transformed cashier template hash drift'
    grep -Fq 'cashier-light-grid-r09.css' "${SITE}/${TEMPLATE_REL}" || fail 'production cashier CSS link missing'
    grep -Fq "formdata.append('orderid', '{\$orderid}')" "${SITE}/${TEMPLATE_REL}" || fail 'production order protocol missing'
    grep -Fq 'window.location.href = data.msg' "${SITE}/${TEMPLATE_REL}" || fail 'production redirect protocol missing'
    [ "$(sha "${SITE}/source/plugin/tb_pay/tb_pay.inc.php")" = "${EXPECTED_TB_PAY_INC_SHA}" ] || fail 'production tb_pay entry changed during deploy'
    [ "$(sha "${SITE}/source/plugin/tb_pay/module/main.php")" = "${EXPECTED_MAIN_PHP_SHA}" ] || fail 'production cashier controller changed during deploy'
    [ "$(sha "${SITE}/source/plugin/tb_pay/module/pay.php")" = "${EXPECTED_PAY_PHP_SHA}" ] || fail 'production payment controller changed during deploy'
    runuser -u www -- test -r "${SITE}/${TEMPLATE_REL}" || fail 'production cashier template is not readable by PHP-FPM user'
    runuser -u www -- test -r "${SITE}/${CSS_REL}" || fail 'production cashier CSS is not readable by PHP-FPM user'
    cat >"${BACKUP}/ROLLBACK.txt" <<EOF
install -o www -g www -m 0777 '${BACKUP}/files/${TEMPLATE_REL}' '${SITE}/${TEMPLATE_REL}'
rm -f -- '${SITE}/${CSS_REL}' '${SITE}/data/template/1_tb_pay_main.tpl.php'
EOF
    chmod 0400 "${BACKUP}/ROLLBACK.txt"
    find "${BACKUP}" -type f -exec chmod a-w {} +
    printf '[R09-CASHIER-PRODUCTION] DEPLOY_ID=%s\n' "${deploy_id}"
    printf '[R09-CASHIER-PRODUCTION] BACKUP=%s\n' "${BACKUP}"
    printf '[R09-CASHIER-PRODUCTION] ARCHIVE_SHA256=%s\n' "${actual_archive_sha}"
    printf '[R09-CASHIER-PRODUCTION] CORE_PAYMENT_HASHES=PASS\n'
    printf '[R09-CASHIER-PRODUCTION] FILES=2\n'
    printf '[R09-CASHIER-PRODUCTION] RESULT=PASS\n'
    ;;
  --apply-rollback)
    assert_site
    [[ "${ROLLBACK_ID}" =~ ^[0-9]{8}T[0-9]{6}[+-][0-9]{4}$ ]] || fail 'rollback ID format is invalid'
    BACKUP="${BACKUP_ROOT}/${ROLLBACK_ID}"
    [ -f "${BACKUP}/files/${TEMPLATE_REL}" ] || fail 'rollback template backup is missing'
    [ "$(sha "${BACKUP}/files/${TEMPLATE_REL}")" = "${EXPECTED_TEMPLATE_SHA}" ] || fail 'rollback template hash drift'
    install -o www -g www -m 0777 "${BACKUP}/files/${TEMPLATE_REL}" "${SITE}/${TEMPLATE_REL}"
    rm -f -- "${SITE}/${CSS_REL}" "${SITE}/data/template/1_tb_pay_main.tpl.php"
    [ "$(sha "${SITE}/${TEMPLATE_REL}")" = "${EXPECTED_TEMPLATE_SHA}" ] || fail 'rollback template restore failed'
    [ ! -e "${SITE}/${CSS_REL}" ] || fail 'rollback CSS cleanup failed'
    printf '[R09-CASHIER-PRODUCTION] ROLLBACK_ID=%s\n' "${ROLLBACK_ID}"
    printf '[R09-CASHIER-PRODUCTION] ROLLBACK=PASS\n'
    ;;
  *) fail 'usage: --verify-only archive sha | --apply-production archive sha | --apply-rollback _ _ deploy-id' ;;
esac
