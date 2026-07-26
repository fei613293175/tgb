param(
    [string]$OverlayRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'r05-site-overlay')
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$OverlayRoot = [System.IO.Path]::GetFullPath($OverlayRoot)
$laneRoots = @(
    (Join-Path $root 'r05-site-overlay-lane-a'),
    (Join-Path $root 'r05-site-overlay-lane-b'),
    (Join-Path $root 'r05-site-overlay-lane-c')
)
$mainRoot = Join-Path $root 'r05-site-overlay-main'
$ownerRoots = @($laneRoots) + @($mainRoot)
$mainCorrections = @(
    'source/plugin/xigua_hb/static/tgb-r02/light-grid-r02.css',
    'source/plugin/xigua_hb/template/touch/common_header.php',
    'source/plugin/xigua_hb/template/touch/list_by_cat1.php'
)

function Fail([string]$Message) {
    throw "[R05-OVERLAY] FAIL: $Message"
}

& (Join-Path $PSScriptRoot 'test-r05-lanes.ps1')

if (-not (Test-Path -LiteralPath $OverlayRoot -PathType Container)) {
    Fail "overlay root is absent: $OverlayRoot"
}
if (-not (Test-Path -LiteralPath $mainRoot -PathType Container)) {
    Fail "main-thread correction root is absent: $mainRoot"
}

$mainFiles = @(Get-ChildItem -LiteralPath $mainRoot -Recurse -File)
if ($mainFiles.Count -ne 3) {
    Fail "main-thread correction file count is $($mainFiles.Count), expected 3"
}
$mainPrefix = $mainRoot.TrimEnd([System.IO.Path]::DirectorySeparatorChar) +
    [System.IO.Path]::DirectorySeparatorChar
$mainRelative = @($mainFiles | ForEach-Object {
    $_.FullName.Substring($mainPrefix.Length).Replace('\', '/')
} | Sort-Object)
if (($mainRelative -join "`n") -cne (($mainCorrections | Sort-Object) -join "`n")) {
    Fail 'unexpected main-thread correction ownership'
}

$baselineRoot = Join-Path $root 'r05-baseline-selected'
$mainListPath = Join-Path $mainRoot 'source/plugin/xigua_hb/template/touch/list_by_cat1.php'
$baselinePath = Join-Path $baselineRoot 'source/plugin/xigua_hb/template/touch/list_by_cat1.php'
$baselineText = ([System.IO.File]::ReadAllText($baselinePath) -replace "`r`n", "`n").TrimEnd([char]10)
$mainText = ([System.IO.File]::ReadAllText($mainListPath) -replace "`r`n", "`n").TrimEnd([char]10)
$oldWrapper = '<div class="weui-cells  before_none after_none mt0" style="height:0px;">'
$newWrapper = '<div class="weui-cells before_none after_none mt0 tgb-home-legacy-tabs" style="display:none!important;height:0px;overflow:hidden;pointer-events:none;">'
if ([regex]::Matches($baselineText, [regex]::Escape($oldWrapper)).Count -ne 1) {
    Fail 'baseline hidden home wrapper signature changed'
}
if ($mainText -cne $baselineText.Replace($oldWrapper, $newWrapper)) {
    Fail 'main-thread home correction contains changes outside the frozen wrapper replacement'
}

$cssRelative = 'source/plugin/xigua_hb/static/tgb-r02/light-grid-r02.css'
$baselineCss = ([System.IO.File]::ReadAllText((Join-Path $baselineRoot $cssRelative)) -replace "`r`n", "`n").TrimEnd([char]10)
$mainCss = ([System.IO.File]::ReadAllText((Join-Path $mainRoot $cssRelative)) -replace "`r`n", "`n").TrimEnd([char]10)
$cssMarker = '/* Shared header */'
$cssAddition = @'
.tgb-light-grid .x_header_fix {
  width: 100% !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
}

.tgb-light-grid .x_header > span > a,
.tgb-light-grid .x_header > span > .navtitle {
  min-height: 44px;
  line-height: 44px !important;
}
'@
if ([regex]::Matches($baselineCss, [regex]::Escape($cssMarker)).Count -ne 1) {
    Fail 'baseline shared header marker changed'
}
if ($mainCss -cne $baselineCss.Replace($cssMarker, $cssMarker + "`n" + $cssAddition.TrimEnd([char]10) + "`n")) {
    Fail 'shared header correction contains changes outside the frozen insertion'
}

$headerRelative = 'source/plugin/xigua_hb/template/touch/common_header.php'
$baselineHeader = ([System.IO.File]::ReadAllText((Join-Path $baselineRoot $headerRelative)) -replace "`r`n", "`n").TrimEnd([char]10)
$mainHeader = ([System.IO.File]::ReadAllText((Join-Path $mainRoot $headerRelative)) -replace "`r`n", "`n").TrimEnd([char]10)
$oldAssetKey = 'light-grid-r02.css?v=20260726-r02d'
$newAssetKey = 'light-grid-r02.css?v=20260727-r05-common1'
if ([regex]::Matches($baselineHeader, [regex]::Escape($oldAssetKey)).Count -ne 1) {
    Fail 'baseline Light Grid asset key changed'
}
if ($mainHeader -cne $baselineHeader.Replace($oldAssetKey, $newAssetKey)) {
    Fail 'common header correction contains changes outside the asset version key'
}

$expected = [System.Collections.Generic.Dictionary[string, string]]::new(
    [System.StringComparer]::OrdinalIgnoreCase
)
foreach ($ownerRoot in $ownerRoots) {
    $prefix = $ownerRoot.TrimEnd([System.IO.Path]::DirectorySeparatorChar) +
        [System.IO.Path]::DirectorySeparatorChar
    foreach ($file in Get-ChildItem -LiteralPath $ownerRoot -Recurse -File) {
        $relative = $file.FullName.Substring($prefix.Length).Replace('\', '/')
        if ($expected.ContainsKey($relative)) {
            Fail "duplicate ownership: $relative"
        }
        $expected.Add($relative, $file.FullName)
    }
}

$overlayPrefix = $OverlayRoot.TrimEnd([System.IO.Path]::DirectorySeparatorChar) +
    [System.IO.Path]::DirectorySeparatorChar
$actual = @(Get-ChildItem -LiteralPath $OverlayRoot -Recurse -File)
$actualRelative = @($actual | ForEach-Object {
    $_.FullName.Substring($overlayPrefix.Length).Replace('\', '/')
} | Sort-Object)
$expectedRelative = @($expected.Keys | Sort-Object)

if (($actualRelative -join "`n") -cne ($expectedRelative -join "`n")) {
    Fail 'merged file allowlist differs from lane ownership'
}

foreach ($file in $actual) {
    if (($file.Attributes -band [System.IO.FileAttributes]::ReparsePoint) -ne 0) {
        Fail "reparse point is forbidden: $($file.FullName)"
    }
    $relative = $file.FullName.Substring($overlayPrefix.Length).Replace('\', '/')
    $sourceHash = (Get-FileHash -Algorithm SHA256 -LiteralPath $expected[$relative]).Hash
    $mergedHash = (Get-FileHash -Algorithm SHA256 -LiteralPath $file.FullName).Hash
    if ($sourceHash -cne $mergedHash) {
        Fail "merged bytes differ from owned lane: $relative"
    }
}

if ($actual.Count -ne 28) {
    Fail "merged file count is $($actual.Count), expected 28"
}

Write-Host '[R05-OVERLAY] PASS'
Write-Host '[R05-OVERLAY] files=28 ownership=EXACT bytes=OWNER_IDENTICAL reparse_points=0'
Write-Host '[R05-OVERLAY] home_legacy_tabs=HIDDEN_ZERO_HIT main_correction=FROZEN_REPLACEMENT'
Write-Host '[R05-OVERLAY] shared_header=NO_OVERFLOW_44PX asset_key=VERSIONED'
