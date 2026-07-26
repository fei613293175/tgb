#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

STAGING_SITE="/www/staging/tg-h5-ui-r02/site"
STAGING_PRIVATE="/www/staging/tg-h5-ui-r02/private"
CLIENT_CONFIG="${STAGING_SITE}/config/config_ucenter.php"
SERVER_CONFIG="${STAGING_SITE}/uc_server/data/config.inc.php"
PANEL_PYTHON="/www/server/panel/pyenv/bin/python3"

fail() {
  printf '[R02-UCENTER] ABORT: %s\n' "$1" >&2
  exit 1
}

[ "$(id -u)" -eq 0 ] || fail "root is required"
[ -f "${CLIENT_CONFIG}" ] || fail "UCenter client config is absent"
[ -f "${SERVER_CONFIG}" ] || fail "UCenter server config is absent"
[ -x "${PANEL_PYTHON}" ] || fail "panel Python is unavailable"

REPAIR_ID="$(date '+%Y%m%dT%H%M%S%z')"
BACKUP_DIR="${STAGING_PRIVATE}/ucenter-client-backups/${REPAIR_ID}"
mkdir -p "${BACKUP_DIR}"
cp -a "${CLIENT_CONFIG}" "${BACKUP_DIR}/config_ucenter.php"
sha256sum "${CLIENT_CONFIG}" > "${BACKUP_DIR}/BEFORE_SHA256.txt"

"${PANEL_PYTHON}" - "${CLIENT_CONFIG}" "${SERVER_CONFIG}" <<'PY'
from pathlib import Path
import re
import sys

client_path = Path(sys.argv[1])
server_path = Path(sys.argv[2])
client = client_path.read_text(encoding="utf-8")
server = server_path.read_text(encoding="utf-8")

for key in ("UC_DBUSER", "UC_DBPW", "UC_DBNAME"):
    source_match = re.search(
        rf"define\('{key}',\s*'((?:\\'|[^'])*)'\);", server
    )
    if source_match is None:
        raise SystemExit(f"missing {key} in UCenter server config")
    source_value = source_match.group(1)
    client, count = re.subn(
        rf"define\('{key}',\s*'(?:\\'|[^'])*'\);",
        f"define('{key}', '{source_value}');",
        client,
        count=1,
    )
    if count != 1:
        raise SystemExit(f"expected one {key} in UCenter client config")

db_match = re.search(r"define\('UC_DBNAME',\s*'((?:\\'|[^'])*)'\);", server)
prefix_match = re.search(r"define\('UC_DBTABLEPRE',\s*'((?:\\'|[^'])*)'\);", server)
if db_match is None or prefix_match is None:
    raise SystemExit("missing UCenter database name or table prefix")
client_prefix = f"`{db_match.group(1)}`.{prefix_match.group(1)}"
client, count = re.subn(
    r"define\('UC_DBTABLEPRE',\s*'(?:\\'|[^'])*'\);",
    f"define('UC_DBTABLEPRE', '{client_prefix}');",
    client,
    count=1,
)
if count != 1:
    raise SystemExit("expected one UC_DBTABLEPRE in UCenter client config")

with client_path.open("w", encoding="utf-8", newline="\n") as handle:
    handle.write(client)
PY

chown www:www "${CLIENT_CONFIG}"
chmod 0777 "${CLIENT_CONFIG}"
php -l "${CLIENT_CONFIG}" >/dev/null
sha256sum "${CLIENT_CONFIG}" > "${BACKUP_DIR}/AFTER_SHA256.txt"

php -r '
include $argv[1];
$db = @new mysqli(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, (int) UC_DBPORT);
if ($db->connect_errno) { exit(2); }
$table = UC_DBTABLEPRE . "vars";
$result = $db->query("SELECT value FROM " . $table . " WHERE name = \"noteexists1\" LIMIT 1");
if (!$result) { exit(3); }
' "${CLIENT_CONFIG}" || fail "UCenter client database probe failed"

chmod -R a-w "${BACKUP_DIR}"
printf '[R02-UCENTER] PASS repair_id=%s\n' "${REPAIR_ID}"
printf '[R02-UCENTER] BACKUP_DIR=%s\n' "${BACKUP_DIR}"
