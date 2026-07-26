#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

CLIENT_CONFIG="/www/staging/tg-h5-ui-r02/site/config/config_ucenter.php"
MAIN_CONFIG="/www/staging/tg-h5-ui-r02/site/config/config_global.php"

[ "$(id -u)" -eq 0 ] || {
  printf '[R02-LOGIN-CHECK] ABORT: root is required\n' >&2
  exit 1
}
[ -f "${CLIENT_CONFIG}" ] || {
  printf '[R02-LOGIN-CHECK] ABORT: staging UCenter client config is absent\n' >&2
  exit 1
}
[ -f "${MAIN_CONFIG}" ] || {
  printf '[R02-LOGIN-CHECK] ABORT: staging main config is absent\n' >&2
  exit 1
}

IFS= read -r TGB_TEST_USERNAME
IFS= read -r -s TGB_TEST_PASSWORD
TGB_TEST_USERNAME="${TGB_TEST_USERNAME%$'\r'}"
TGB_TEST_PASSWORD="${TGB_TEST_PASSWORD%$'\r'}"
export TGB_TEST_USERNAME TGB_TEST_PASSWORD CLIENT_CONFIG MAIN_CONFIG

php <<'PHP'
<?php
include getenv('CLIENT_CONFIG');

$username = getenv('TGB_TEST_USERNAME');
$plaintext = getenv('TGB_TEST_PASSWORD');
$database = @new mysqli(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, (int) UC_DBPORT);
if ($database->connect_errno) {
    fwrite(STDERR, "[R02-LOGIN-CHECK] ABORT: UCenter connection failed\n");
    exit(2);
}

$table = UC_DBTABLEPRE . 'members';
$statement = $database->prepare(
    "SELECT password, salt FROM " . $table . " WHERE username = ? LIMIT 1"
);
if (!$statement) {
    fwrite(STDERR, "[R02-LOGIN-CHECK] ABORT: credential query prepare failed\n");
    exit(3);
}
$statement->bind_param('s', $username);
$statement->execute();
$result = $statement->get_result();
$row = $result ? $result->fetch_assoc() : null;

if (!$row) {
    include getenv('MAIN_CONFIG');
    $mainConfig = $_config['db'][1];
    $main = @new mysqli(
        $mainConfig['dbhost'],
        $mainConfig['dbuser'],
        $mainConfig['dbpw'],
        $mainConfig['dbname'],
        (int) $mainConfig['dbport']
    );
    if ($main->connect_errno) {
        fwrite(STDERR, "[R02-LOGIN-CHECK] ABORT: main database connection failed\n");
        exit(6);
    }

    $schema = $mainConfig['dbname'];
    $columns = $main->prepare(
        "SELECT c.TABLE_NAME, c.COLUMN_NAME
         FROM information_schema.COLUMNS c
         WHERE c.TABLE_SCHEMA = ?
           AND (
             LOWER(c.COLUMN_NAME) LIKE '%mobile%'
             OR LOWER(c.COLUMN_NAME) LIKE '%phone%'
             OR LOWER(c.COLUMN_NAME) LIKE '%telephone%'
             OR LOWER(c.COLUMN_NAME) = 'tel'
             OR LOWER(c.COLUMN_NAME) LIKE '%account%'
             OR LOWER(c.COLUMN_NAME) LIKE '%login%'
           )
           AND EXISTS (
             SELECT 1 FROM information_schema.COLUMNS u
             WHERE u.TABLE_SCHEMA = c.TABLE_SCHEMA
               AND u.TABLE_NAME = c.TABLE_NAME
               AND u.COLUMN_NAME = 'uid'
           )
         ORDER BY CASE WHEN c.TABLE_NAME LIKE '%common_member_profile' THEN 0 ELSE 1 END,
                  c.TABLE_NAME, c.COLUMN_NAME"
    );
    $columns->bind_param('s', $schema);
    $columns->execute();
    $columnResult = $columns->get_result();
    $mappedUid = 0;
    while ($candidate = $columnResult->fetch_assoc()) {
        $tableName = $candidate['TABLE_NAME'];
        $columnName = $candidate['COLUMN_NAME'];
        if (!preg_match('/^[A-Za-z0-9_]+$/', $tableName . $columnName)) {
            continue;
        }
        $lookup = $main->prepare(
            "SELECT uid FROM `" . $tableName . "` WHERE `" . $columnName . "` = ? LIMIT 1"
        );
        if (!$lookup) {
            continue;
        }
        $lookup->bind_param('s', $username);
        $lookup->execute();
        $lookupResult = $lookup->get_result();
        $uidRow = $lookupResult ? $lookupResult->fetch_assoc() : null;
        if ($uidRow) {
            $mappedUid = (int) $uidRow['uid'];
            break;
        }
    }

    if ($mappedUid > 0) {
        echo "[R02-LOGIN-CHECK] PHONE_MAPPING_FOUND=YES\n";
        $memberTable = $mainConfig['tablepre'] . 'common_member';
        if (!preg_match('/^[A-Za-z0-9_]+$/', $memberTable)) {
            fwrite(STDERR, "[R02-LOGIN-CHECK] ABORT: unexpected member table name\n");
            exit(7);
        }
        $member = $main->prepare(
            "SELECT username FROM `" . $memberTable . "` WHERE uid = ? LIMIT 1"
        );
        $member->bind_param('i', $mappedUid);
        $member->execute();
        $memberResult = $member->get_result();
        $memberRow = $memberResult ? $memberResult->fetch_assoc() : null;
        if ($memberRow) {
            echo "[R02-LOGIN-CHECK] MAIN_MEMBER_FOUND=YES\n";
            $mappedUsername = $memberRow['username'];
            $statement->bind_param('s', $mappedUsername);
            $statement->execute();
            $result = $statement->get_result();
            $row = $result ? $result->fetch_assoc() : null;
            if ($row) {
                echo "[R02-LOGIN-CHECK] MAPPED_BY_PHONE=YES\n";
            }
        }
    }
}

if (!$row) {
    echo "[R02-LOGIN-CHECK] ACCOUNT_FOUND=NO\n";
    exit(4);
}

$candidate = md5(md5($plaintext) . $row['salt']);
$matches = hash_equals((string) $row['password'], $candidate);
echo "[R02-LOGIN-CHECK] ACCOUNT_FOUND=YES\n";
echo "[R02-LOGIN-CHECK] PASSWORD_MATCH=" . ($matches ? "YES" : "NO") . "\n";
exit($matches ? 0 : 5);
PHP
