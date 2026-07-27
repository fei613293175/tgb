#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

ARCHIVE="${1:-/tmp/r09-cashier-overlay.tar.gz}"
EXPECTED_ARCHIVE_SHA="${2:-}"
SITE="/www/staging/tg-h5-ui-r08/site"
PRIVATE="/www/staging/tg-h5-ui-r08/private"
BACKUP_ROOT="${PRIVATE}/change-backups"
EXPECTED_TEMPLATE_SHA="ccae4d5f80d1c8f7ff71803c99ddd01644a76601c4c588acededdd78cd65fc82"
TEMPLATE_REL="source/plugin/tb_pay/template/touch/main.htm"
CSS_REL="source/plugin/tb_pay/static/tgb-r09/cashier-light-grid-r09.css"
TRANSFORM_REL="tools/r09_transform_cashier_template.py"
EXPECTED_OUTPUT_SHA="83661a7871894331bdf6f6543f792c236e871f3267ac787291eaab80e70abe86"
PYTHON="/www/server/panel/pyenv/bin/python3"
TMP=""
BACKUP=""
DEPLOYED=0

fail() { printf '[R09-CASHIER-DEPLOY] ABORT: %s\n' "$1" >&2; exit 1; }
cleanup() { [ -z "${TMP}" ] || rm -rf -- "${TMP}"; }

rollback_on_error() {
  local code=$?
  if [ "${DEPLOYED}" -eq 1 ] && [ -n "${BACKUP}" ] && [ -f "${BACKUP}/files/${TEMPLATE_REL}" ]; then
    install -d -o www -g www -m 0755 "${SITE}/source/plugin/tb_pay/template/touch"
    install -o www -g www -m 0644 "${BACKUP}/files/${TEMPLATE_REL}" "${SITE}/${TEMPLATE_REL}"
    rm -f -- "${SITE}/${CSS_REL}" "${SITE}/data/template/1_tb_pay_main.tpl.php"
    printf '[R09-CASHIER-DEPLOY] AUTO_ROLLBACK=COMPLETE\n' >&2
  fi
  cleanup
  exit "${code}"
}
trap rollback_on_error ERR
trap cleanup EXIT

[ "$(id -u)" -eq 0 ] || fail 'root is required'
[ -n "${EXPECTED_ARCHIVE_SHA}" ] || fail 'expected archive SHA-256 is required'
[ -f "${ARCHIVE}" ] || fail 'overlay archive is missing'
[ -x "${PYTHON}" ] || fail 'panel Python runtime is unavailable'
[ -d "${SITE}/source/plugin" ] || fail 'R08 staging site is absent'
[ "$(stat -c '%a' "${PRIVATE}")" = '700' ] || fail 'R08 private directory mode is not 700'
grep -Fq 'tgb_stage_r08_main' "${SITE}/config/config_global.php" || fail 'not the R08 main database'
grep -Fq 'tgb_stage_r08_uc' "${SITE}/config/config_ucenter.php" || fail 'not the R08 UCenter database'
[ -d "${PRIVATE}/r09-cashier-fixture-active" ] || fail 'cashier fixture is not active'
[ -d "${SITE}/__r08_auth__" ] || fail 'authentication bridge is not active'
[ -d "${PRIVATE}/browser-origin-active" ] || fail 'browser origin bridge is not active'

actual_archive_sha="$(sha256sum "${ARCHIVE}" | awk '{print $1}')"
[ "${actual_archive_sha}" = "${EXPECTED_ARCHIVE_SHA,,}" ] || fail 'archive SHA-256 mismatch'

template_target="${SITE}/${TEMPLATE_REL}"
css_target="${SITE}/${CSS_REL}"
[ "$(sha256sum "${template_target}" | awk '{print $1}')" = "${EXPECTED_TEMPLATE_SHA}" ] || fail 'cashier template predeploy hash drift'
[ ! -e "${css_target}" ] || fail 'cashier CSS already exists before first deploy'

expected_list="$(printf '%s\n' "${CSS_REL}" "${TRANSFORM_REL}")"
actual_list="$(tar -tzf "${ARCHIVE}" | sed 's#^\./##' | sed '/\/$/d' | sort)"
[ "${actual_list}" = "${expected_list}" ] || fail 'archive file list is not the two-file minimal overlay'
if tar -tzf "${ARCHIVE}" | grep -Eq '(^/|(^|/)\.\.(/|$))'; then
  fail 'archive contains an unsafe path'
fi

TMP="$(mktemp -d /tmp/r09-cashier-deploy.XXXXXX)"
tar -xzf "${ARCHIVE}" -C "${TMP}"
"${PYTHON}" "${TMP}/${TRANSFORM_REL}" "${template_target}" "${TMP}/cashier-main.htm"

deploy_id="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP="${BACKUP_ROOT}/${deploy_id}-r09-cashier"
[ ! -e "${BACKUP}" ] || fail 'backup path already exists'
install -d -m 0700 "${BACKUP}/files/$(dirname "${TEMPLATE_REL}")"
cp -a -- "${template_target}" "${BACKUP}/files/${TEMPLATE_REL}"

install -d -o www -g www -m 0755 \
  "$(dirname "${template_target}")" \
  "$(dirname "${css_target}")"
install -o www -g www -m 0644 "${TMP}/cashier-main.htm" "${template_target}"
install -o www -g www -m 0644 "${TMP}/${CSS_REL}" "${css_target}"
rm -f -- "${SITE}/data/template/1_tb_pay_main.tpl.php"
DEPLOYED=1

grep -Fq '<title>推广宝收银台</title>' "${template_target}" || fail 'cashier brand title missing after deploy'
[ "$(sha256sum "${template_target}" | awk '{print $1}')" = "${EXPECTED_OUTPUT_SHA}" ] || fail 'cashier transformed template hash drift after deploy'
grep -Fq 'cashier-light-grid-r09.css' "${template_target}" || fail 'cashier CSS link missing after deploy'
grep -Fq "formdata.append('orderid', '{\$orderid}')" "${template_target}" || fail 'cashier order protocol missing after deploy'
grep -Fq "window.location.href = data.msg" "${template_target}" || fail 'cashier redirect protocol missing after deploy'
runuser -u www -- test -r "${template_target}" || fail 'cashier template is not readable by PHP-FPM user'
runuser -u www -- test -r "${css_target}" || fail 'cashier CSS is not readable by PHP-FPM user'
[ "$(stat -c '%a:%U:%G' "$(dirname "${template_target}")")" = '755:www:www' ] || fail 'cashier touch directory permission drift'

cat >"${BACKUP}/ROLLBACK.txt" <<EOF
install -d -o www -g www -m 0755 '${SITE}/source/plugin/tb_pay/template/touch'
install -o www -g www -m 0644 '${BACKUP}/files/${TEMPLATE_REL}' '${SITE}/${TEMPLATE_REL}'
rm -f -- '${SITE}/${CSS_REL}' '${SITE}/data/template/1_tb_pay_main.tpl.php'
EOF
chmod 0400 "${BACKUP}/ROLLBACK.txt"
find "${BACKUP}" -type f -exec chmod a-w {} +

printf '[R09-CASHIER-DEPLOY] DEPLOY_ID=%s\n' "${deploy_id}"
printf '[R09-CASHIER-DEPLOY] BACKUP=%s\n' "${BACKUP}"
printf '[R09-CASHIER-DEPLOY] ARCHIVE_SHA256=%s\n' "${actual_archive_sha}"
printf '[R09-CASHIER-DEPLOY] FILES=2\n'
printf '[R09-CASHIER-DEPLOY] RESULT=PASS\n'
