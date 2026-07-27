#!/usr/bin/env bash
set -Eeuo pipefail

MODE="${1:-}"
BACKUP_DIR="${2:-}"
ROOT="/www/wwwroot/tg.suewammes.com"
BACKUP_ROOT="/www/staging/tg-h5-ui-r08/private/production-release-backups"

fail() { printf '[R09-ROLLBACK] ABORT: %s\n' "$1" >&2; exit 1; }
[ "${MODE}" = "--apply-rollback" ] || fail "explicit --apply-rollback is required"
[ "$(id -u)" -eq 0 ] || fail "root is required"
case "${BACKUP_DIR}" in "${BACKUP_ROOT}"/*) ;; *) fail "backup path is outside the release backup root" ;; esac
[ -f "${BACKUP_DIR}/DEPLOYMENT.env" ] || fail "deployment metadata is absent"
[ -f "${BACKUP_DIR}/CANDIDATE_SHA256.txt" ] || fail "candidate manifest is absent"

while IFS= read -r relative; do
  [ -n "${relative}" ] || continue
  source_file="${BACKUP_DIR}/files/${relative}"
  [ -f "${source_file}" ] || fail "backup file is absent: ${relative}"
  install -d -o www -g www -m 0755 "$(dirname "${ROOT}/${relative}")"
  install -o www -g www -m 0644 "${source_file}" "${ROOT}/${relative}"
done < <(awk '{sub(/^[^ ]+  /, ""); print}' "${BACKUP_DIR}/BEFORE_SHA256.txt")

if [ -f "${BACKUP_DIR}/BEFORE_ABSENT.txt" ]; then
  while IFS= read -r relative; do
    [ -n "${relative}" ] || continue
    case "${ROOT}/${relative}" in "${ROOT}"/*) rm -f -- "${ROOT}/${relative}" ;; *) fail "invalid absent-file path" ;; esac
  done <"${BACKUP_DIR}/BEFORE_ABSENT.txt"
fi

(cd "${ROOT}" && sha256sum -c "${BACKUP_DIR}/BEFORE_SHA256.txt" >/dev/null) || fail "restored files differ from backup"
find "${ROOT}/data/template" -mindepth 1 -maxdepth 1 -type f -delete
printf '[R09-ROLLBACK] PASS BACKUP_DIR=%s\n' "${BACKUP_DIR}"
