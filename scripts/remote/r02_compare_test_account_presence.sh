#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

IFS= read -r TGB_TEST_PHONE
export TGB_TEST_PHONE

php <<'PHP'
<?php
function mainPresence(string $configPath, string $phone): array {
    $_config = array();
    include $configPath;
    $config = $_config['db'][1];
    $database = @new mysqli(
        $config['dbhost'],
        $config['dbuser'],
        $config['dbpw'],
        $config['dbname'],
        (int) $config['dbport']
    );
    if ($database->connect_errno) {
        throw new RuntimeException('database connection failed');
    }
    $phoneTable = $config['tablepre'] . 'tb_cus_mobilereg_userphone';
    $memberTable = $config['tablepre'] . 'common_member';
    if (!preg_match('/^[A-Za-z0-9_]+$/', $phoneTable . $memberTable)) {
        throw new RuntimeException('unexpected table name');
    }
    $statement = $database->prepare(
        "SELECT COUNT(*) FROM `" . $phoneTable . "` WHERE mobile = ?"
    );
    $statement->bind_param('s', $phone);
    $statement->execute();
    $phoneMapping = (int) $statement->get_result()->fetch_row()[0] > 0;
    $member = $database->prepare(
        "SELECT COUNT(*) FROM `" . $memberTable . "` WHERE username = ?"
    );
    $member->bind_param('s', $phone);
    $member->execute();
    $username = (int) $member->get_result()->fetch_row()[0] > 0;
    return array($phoneMapping, $username);
}

function ucenterPresence(string $configPath, string $phone): bool {
    include $configPath;
    $database = @new mysqli(UC_DBHOST, UC_DBUSER, UC_DBPW, UC_DBNAME, (int) UC_DBPORT);
    if ($database->connect_errno) {
        throw new RuntimeException('UCenter connection failed');
    }
    $table = UC_DBTABLEPRE . 'members';
    $statement = $database->prepare(
        "SELECT COUNT(*) FROM " . $table . " WHERE username = ?"
    );
    $statement->bind_param('s', $phone);
    $statement->execute();
    return (int) $statement->get_result()->fetch_row()[0] > 0;
}

$phone = getenv('TGB_TEST_PHONE');
try {
    $production = mainPresence(
        '/www/wwwroot/tg.suewammes.com/config/config_global.php',
        $phone
    );
    $staging = mainPresence(
        '/www/staging/tg-h5-ui-r02/site/config/config_global.php',
        $phone
    );
    $productionUc = ucenterPresence(
        '/www/wwwroot/tg.suewammes.com/config/config_ucenter.php',
        $phone
    );
    $stagingUc = ucenterPresence(
        '/www/staging/tg-h5-ui-r02/site/config/config_ucenter.php',
        $phone
    );
} catch (Throwable $error) {
    fwrite(STDERR, "[R02-ACCOUNT-PRESENCE] ABORT: read-only probe failed\n");
    exit(2);
}

echo "[R02-ACCOUNT-PRESENCE] PRODUCTION_PHONE_MAP=" . ($production[0] ? "YES" : "NO") . "\n";
echo "[R02-ACCOUNT-PRESENCE] PRODUCTION_MAIN_USERNAME=" . ($production[1] ? "YES" : "NO") . "\n";
echo "[R02-ACCOUNT-PRESENCE] PRODUCTION_UC_USERNAME=" . ($productionUc ? "YES" : "NO") . "\n";
echo "[R02-ACCOUNT-PRESENCE] STAGING_PHONE_MAP=" . ($staging[0] ? "YES" : "NO") . "\n";
echo "[R02-ACCOUNT-PRESENCE] STAGING_MAIN_USERNAME=" . ($staging[1] ? "YES" : "NO") . "\n";
echo "[R02-ACCOUNT-PRESENCE] STAGING_UC_USERNAME=" . ($stagingUc ? "YES" : "NO") . "\n";
PHP
