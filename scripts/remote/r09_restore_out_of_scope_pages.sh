#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

MODE="${1:-}"
ROLLBACK_ID="${2:-}"
ROOT="/www/wwwroot/tg.suewammes.com"
SOURCE_ROOT="/www/staging/tg-h5-ui-r08/private/production-release-backups/20260727T090142+0800/files"
PRIVATE_ROOT="/www/staging/tg-h5-ui-r08/private/production-scope-corrections"
FILES=("m/hyxy.html" "m/help.html" "m/gywm.html")

declare -A ORIGINAL_SHA=(
  ["m/hyxy.html"]="1b2182ba72e7d219a927731e063fffcfe5dcb1aec003f90a1aaedc128307dade"
  ["m/help.html"]="bbe0ff48d8731952b5f0a03faa714c9a4720b970ca890836b0739b4ee23f65f6"
  ["m/gywm.html"]="e7d451e6616a6be28b91b2e546402fe81cd1667a5b15726a808355fdc9912f55"
)
declare -A REDESIGNED_SHA=(
  ["m/hyxy.html"]="6684da61a179b7b997b72971bf1a1ff67389d11ee1b154910b1a859c31085c9b"
  ["m/help.html"]="20f04b81b2d24fe20550dd7b118bf9c744821c39055be957602687f48f2cb61a"
  ["m/gywm.html"]="845c2dd66c1c7ec96b16b0323bdfb86bf9567e95b121906ff649052268a56a4a"
)

fail() { printf '[R09-SCOPE-RESTORE] ABORT: %s\n' "$1" >&2; exit 1; }
hash_file() { sha256sum "$1" | awk '{print $1}'; }

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -d "${ROOT}/source/plugin" ] || fail "production root is invalid"
[ -d "${SOURCE_ROOT}/m" ] || fail "pre-R09 production backup is absent"

verify_sources() {
  local relative actual
  for relative in "${FILES[@]}"; do
    [ -f "${SOURCE_ROOT}/${relative}" ] || fail "backup source missing: ${relative}"
    [ ! -L "${SOURCE_ROOT}/${relative}" ] || fail "backup source is a symlink: ${relative}"
    actual="$(hash_file "${SOURCE_ROOT}/${relative}")"
    [ "${actual}" = "${ORIGINAL_SHA[${relative}]}" ] || fail "backup source hash mismatch: ${relative}"
  done
}

verify_originals_installed() {
  local relative actual
  for relative in "${FILES[@]}"; do
    actual="$(hash_file "${ROOT}/${relative}")"
    [ "${actual}" = "${ORIGINAL_SHA[${relative}]}" ] || fail "production original hash mismatch: ${relative}"
  done
}

smoke_visible_scope() {
  local ua code
  ua='Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36 TuiGuangBaoAndroid/1.0.0'
  for path in '/m/xy.html' '/m/yszc.html' '/m/fpsm.html'; do
    code="$(curl -sS -o /dev/null -w '%{http_code}' -A "${ua}" "https://tg.suewammes.com${path}")"
    [ "${code}" = "200" ] || fail "visible-scope smoke failed: ${path} HTTP ${code}"
  done
}

verify_sources
case "${MODE}" in
  verify-only)
    for relative in "${FILES[@]}"; do
      actual="$(hash_file "${ROOT}/${relative}")"
      if [ "${actual}" != "${REDESIGNED_SHA[${relative}]}" ] && [ "${actual}" != "${ORIGINAL_SHA[${relative}]}" ]; then
        fail "unexpected production hash: ${relative}"
      fi
    done
    printf '[R09-SCOPE-RESTORE] VERIFY=PASS\n'
    ;;
  apply)
    for relative in "${FILES[@]}"; do
      [ "$(hash_file "${ROOT}/${relative}")" = "${REDESIGNED_SHA[${relative}]}" ] ||
        fail "production is not the expected R09 redesigned file: ${relative}"
    done
    deploy_id="$(date '+%Y%m%dT%H%M%S%z')"
    backup_dir="${PRIVATE_ROOT}/${deploy_id}"
    [ ! -e "${backup_dir}" ] || fail "correction backup already exists"
    install -d -m 0700 "${backup_dir}/files"
    printf 'deploy_id=%s\nmode=restore-out-of-visual-scope\n' "${deploy_id}" >"${backup_dir}/DEPLOYMENT.env"
    for relative in "${FILES[@]}"; do
      install -d -m 0700 "${backup_dir}/files/$(dirname "${relative}")"
      cp -a -- "${ROOT}/${relative}" "${backup_dir}/files/${relative}"
      printf '%s  %s\n' "$(hash_file "${ROOT}/${relative}")" "${relative}" >>"${backup_dir}/BEFORE_SHA256.txt"
      install -o www -g www -m 0644 "${SOURCE_ROOT}/${relative}" "${ROOT}/${relative}"
      printf '%s  %s\n' "$(hash_file "${ROOT}/${relative}")" "${relative}" >>"${backup_dir}/AFTER_SHA256.txt"
    done
    chmod -R a-w "${backup_dir}"
    verify_originals_installed
    smoke_visible_scope
    printf '[R09-SCOPE-RESTORE] APPLY=PASS DEPLOY_ID=%s BACKUP=%s\n' "${deploy_id}" "${backup_dir}"
    ;;
  rollback)
    [[ "${ROLLBACK_ID}" =~ ^[0-9]{8}T[0-9]{6}[+-][0-9]{4}$ ]] || fail "rollback id is invalid"
    backup_dir="${PRIVATE_ROOT}/${ROLLBACK_ID}"
    [ -d "${backup_dir}/files/m" ] || fail "rollback backup is absent"
    verify_originals_installed
    for relative in "${FILES[@]}"; do
      [ "$(hash_file "${backup_dir}/files/${relative}")" = "${REDESIGNED_SHA[${relative}]}" ] ||
        fail "rollback source hash mismatch: ${relative}"
      install -o www -g www -m 0644 "${backup_dir}/files/${relative}" "${ROOT}/${relative}"
    done
    smoke_visible_scope
    printf '[R09-SCOPE-RESTORE] ROLLBACK=PASS ID=%s\n' "${ROLLBACK_ID}"
    ;;
  *)
    fail "usage: r09_restore_out_of_scope_pages.sh verify-only|apply|rollback [rollback-id]"
    ;;
esac
