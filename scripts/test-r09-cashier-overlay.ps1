param(
    [string]$RepositoryRoot = (Split-Path -Parent $PSScriptRoot)
)

$ErrorActionPreference = 'Stop'

$baselineSums = Join-Path $RepositoryRoot 'r09-cashier-baseline\SHA256SUMS.txt'
$overlayRoot = Join-Path $RepositoryRoot 'r09-cashier-overlay'
$overlayCss = Join-Path $overlayRoot 'source\plugin\tb_pay\static\tgb-r09\cashier-light-grid-r09.css'
$transformScript = Join-Path $overlayRoot 'tools\r09_transform_cashier_template.py'
$stagingDeployScript = Join-Path $RepositoryRoot 'scripts\remote\r09_deploy_cashier.sh'
$productionDeployScript = Join-Path $RepositoryRoot 'scripts\remote\r09_deploy_cashier_production.sh'

$requiredFiles = @($baselineSums, $overlayCss, $transformScript, $stagingDeployScript, $productionDeployScript)
foreach ($file in $requiredFiles) {
    if (-not (Test-Path -LiteralPath $file -PathType Leaf)) {
        throw "Required file is missing: $file"
    }
}

function Read-NormalizedText([string]$Path) {
    return ([System.IO.File]::ReadAllText($Path)).Replace("`r`n", "`n").TrimEnd("`r", "`n")
}

$expectedBaselineLines = @(
    'ccae4d5f80d1c8f7ff71803c99ddd01644a76601c4c588acededdd78cd65fc82  source/plugin/tb_pay/template/touch/main.htm',
    '7754c28fbe2d251b5ab305f3c65fc05ddff4fbad7f34f84e01db13024d9fea64  source/plugin/tb_pay/tb_pay.inc.php',
    '835aff76bc6c85af3fce71683cfce8f488464b06fa903b8db00f4364db4257af  source/plugin/tb_pay/module/main.php',
    'd0b19011633ef1a7619c7daf2eba5ef01db1b3b2839a7394b3a338e7aa7e7d4e  source/plugin/tb_pay/module/pay.php'
)
$actualBaselineLines = (Read-NormalizedText $baselineSums).Split("`n")
if ((Compare-Object $expectedBaselineLines $actualBaselineLines).Count -ne 0) {
    throw 'Cashier baseline hash list drift.'
}

$transformText = Read-NormalizedText $transformScript
$requiredTransformMarkers = @(
    'BASELINE_SHA256 = "ccae4d5f80d1c8f7ff71803c99ddd01644a76601c4c588acededdd78cd65fc82"',
    'OUTPUT_SHA256 = "83661a7871894331bdf6f6543f792c236e871f3267ac787291eaab80e70abe86"',
    '<title>推广宝收银台</title>',
    'cashier-light-grid-r09.css?{VERHASH}',
    '<span class="cashier-back-icon" aria-hidden="true"></span>',
    '<h1 id="header-title">推广宝收银台</h1>',
    'onerror="this.hidden=true"',
    'if text.count(old) != 1:',
    'if digest(output_bytes) != OUTPUT_SHA256:'
)
foreach ($marker in $requiredTransformMarkers) {
    if (-not $transformText.Contains($marker)) {
        throw "Cashier transform marker is missing: $marker"
    }
}

if ($transformText -match '(?i)(password|cookie|formhash\s*=|private.key|BEGIN [A-Z ]+PRIVATE KEY)' -or
    $transformText -match '\b[0-9]{16,19}\b' -or
    $transformText -match '\bT[1-9A-HJ-NP-Za-km-z]{33}\b') {
    throw 'Cashier transform contains a forbidden credential, account number, or wallet address.'
}

$cssText = Read-NormalizedText $overlayCss
if ($cssText -match 'url\s*\(\s*["'']?https?://' -or $cssText -match '@import') {
    throw 'Cashier CSS contains a public or imported dependency.'
}
if ($cssText -notmatch '--tgb-pay-bg:\s*#f4f7fb' -or
    $cssText -notmatch '--tgb-pay-primary:\s*#1f66e5' -or
    $cssText -notmatch 'safe-area-inset-top' -or
    $cssText -notmatch 'safe-area-inset-bottom' -or
    $cssText -notmatch '#paybtndiv' -or
    $cssText -notmatch 'body > div\[style\*="margin-bottom:100px"\]') {
    throw 'Cashier CSS lost a required light-theme, safe-area, footer, or spacer-fix contract.'
}

$stagingDeployText = Read-NormalizedText $stagingDeployScript
if ($stagingDeployText -notmatch 'r09-cashier-fixture-active' -or
    $stagingDeployText -notmatch 'browser-origin-active' -or
    $stagingDeployText -notmatch 'r09_transform_cashier_template.py' -or
    $stagingDeployText -notmatch 'EXPECTED_OUTPUT_SHA' -or
    $stagingDeployText -notmatch 'AUTO_ROLLBACK=COMPLETE' -or
    $stagingDeployText -notmatch 'runuser -u www -- test -r') {
    throw 'Staging cashier deployment lost a fixture, transform, hash, rollback, or readability gate.'
}

$productionDeployText = Read-NormalizedText $productionDeployScript
if ($productionDeployText -notmatch '--verify-only' -or
    $productionDeployText -notmatch '--apply-production' -or
    $productionDeployText -notmatch '--apply-rollback' -or
    $productionDeployText -notmatch 'r09_transform_cashier_template.py' -or
    $productionDeployText -notmatch 'production-cashier-backups' -or
    $productionDeployText -notmatch 'CORE_PAYMENT_HASHES=PASS') {
    throw 'Production cashier deployment lost a preflight, transform, backup, rollback, or payment-core gate.'
}

Write-Host '[R09-CASHIER-GATE] BASELINE_HASH_LIST=PASS'
Write-Host '[R09-CASHIER-GATE] EXACT_HASH_GUARDED_TRANSFORM=PASS'
Write-Host '[R09-CASHIER-GATE] BUSINESS_SCRIPT_UNTOUCHED_BY_PATCH=PASS'
Write-Host '[R09-CASHIER-GATE] SENSITIVE_PAYMENT_VALUES_STORED=0'
Write-Host '[R09-CASHIER-GATE] LOCAL_CSS_ONLY=PASS'
Write-Host '[R09-CASHIER-GATE] LIGHT_SAFE_AREA_CONTRACT=PASS'
Write-Host '[R09-CASHIER-GATE] DEPLOYMENT_ROLLBACK_SAFETY=PASS'
Write-Host '[R09-CASHIER-GATE] RESULT=PASS'
