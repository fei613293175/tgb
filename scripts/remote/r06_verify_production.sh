#!/usr/bin/env bash
set -Eeuo pipefail

ROOT="/www/wwwroot/tg.suewammes.com"
EXPECTED="${1:-}"
BASELINE_MANIFEST="${2:-}"
[[ "${EXPECTED}" =~ ^[0-9a-f]{64}$ ]] || {
  printf '[R06-PRODUCTION] ABORT: invalid expected SHA-256\n' >&2
  exit 2
}

TMP_MANIFEST="$(mktemp /tmp/r06-production-manifest.XXXXXX)"
trap 'rm -f "${TMP_MANIFEST}"' EXIT

find "${ROOT}" -xdev -type f \
  ! -path "${ROOT}/operation.log" \
  ! -path "${ROOT}/data/attachment/*" \
  ! -path "${ROOT}/data/cache/*" \
  ! -path "${ROOT}/data/log/*" \
  ! -path "${ROOT}/data/sysdata/*" \
  ! -path "${ROOT}/data/template/*" \
  ! -path "${ROOT}/source/plugin/xigua_hb/pics/*" \
  ! -path "${ROOT}/uc_server/data/avatar/*" \
  ! -path "${ROOT}/uc_server/data/cache/*" \
  ! -path "${ROOT}/uc_server/data/logs/*" \
  -print0 | sort -z | xargs -0 sha256sum >"${TMP_MANIFEST}"

ACTUAL="$(sha256sum "${TMP_MANIFEST}" | awk '{print $1}')"
[ "${ACTUAL}" = "${EXPECTED}" ] || {
  printf '[R06-PRODUCTION] FAIL expected=%s actual=%s\n' "${EXPECTED}" "${ACTUAL}" >&2
  if [ -f "${BASELINE_MANIFEST}" ]; then
    DIFF_FILE="$(mktemp /tmp/r06-production-diff.XXXXXX)"
    diff -u "${BASELINE_MANIFEST}" "${TMP_MANIFEST}" >"${DIFF_FILE}" || true
    sed -n '1,120p' "${DIFF_FILE}" >&2
    rm -f "${DIFF_FILE}"
  fi
  exit 3
}

printf '[R06-PRODUCTION] PASS SHA256=%s\n' "${ACTUAL}"
