#!/usr/bin/env bash
set -Eeuo pipefail

WORK_DIR="$(mktemp -d /tmp/r09-online.XXXXXX)"
trap 'rm -rf -- "${WORK_DIR}"' EXIT
UA='TuiGuangBaoAndroid/1.0.0 Android'

while IFS='|' read -r name url; do
  code="$(curl -sS -L --max-redirs 5 -A "${UA}" -o "${WORK_DIR}/${name}.html" -w '%{http_code}' "${url}")"
  [ "${code}" = '200' ] || { printf '[R09-ONLINE] FAIL %s HTTP=%s\n' "${name}" "${code}" >&2; exit 1; }
done <<'EOF'
home|https://tg.suewammes.com/plugin.php?id=xigua_hb&mobile=2
login|https://tg.suewammes.com/member.php?mod=logging&action=login&mobile=2
help|https://tg.suewammes.com/m/help.html
about|https://tg.suewammes.com/m/gywm.html
app|https://tg.suewammes.com/done/app.html
EOF

! grep -RIEq 'cdn\.tailwindcss|cdn\.jsdelivr|cdnjs\.cloudflare|unpkg\.com|fonts\.googleapis|use\.fontawesome' "${WORK_DIR}" || exit 1
! grep -RIEq '签米|创脉引擎' "${WORK_DIR}" || exit 1
grep -q '推广宝' "${WORK_DIR}/home.html"
grep -q '推广宝' "${WORK_DIR}/app.html"

printf '[R09-ONLINE] PASS PAGES=5 HTTP=200 CDN=0 OLD_BRAND=0 BRAND=PASS\n'
