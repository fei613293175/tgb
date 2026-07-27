param(
    [string]$RepoRoot = (Split-Path -Parent $PSScriptRoot)
)

$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.Drawing
$RepoRoot = [IO.Path]::GetFullPath($RepoRoot)

function Fail([string]$Message) { throw "[R09-GLOBAL-CLOSEOUT] FAIL: $Message" }
function Assert-True([bool]$Condition, [string]$Message) { if (-not $Condition) { Fail $Message } }
function Resolve-RepoPath([string]$Relative) { return Join-Path $RepoRoot $Relative }
function Read-Json([string]$Relative) {
    $path = Resolve-RepoPath $Relative
    Assert-True (Test-Path -LiteralPath $path -PathType Leaf) "missing JSON: $Relative"
    return Get-Content -LiteralPath $path -Raw | ConvertFrom-Json
}
function Assert-PngSize([string]$Relative, [int]$Width, [int]$Height) {
    $path = Resolve-RepoPath $Relative
    Assert-True (Test-Path -LiteralPath $path -PathType Leaf) "missing PNG: $Relative"
    $bitmap = [Drawing.Bitmap]::new($path)
    try {
        Assert-True ($bitmap.Width -eq $Width -and $bitmap.Height -eq $Height) "PNG size mismatch: $Relative expected=${Width}x${Height} actual=$($bitmap.Width)x$($bitmap.Height)"
    }
    finally { $bitmap.Dispose() }
}
function Assert-BluePixels([string]$Relative, $Box, [string]$ControlName) {
    $path = Resolve-RepoPath $Relative
    $bitmap = [Drawing.Bitmap]::new($path)
    try {
        $left = [Math]::Max(0, [int][Math]::Floor($Box.left))
        $top = [Math]::Max(0, [int][Math]::Floor($Box.top))
        $right = [Math]::Min($bitmap.Width, [int][Math]::Ceiling($Box.right))
        $bottom = [Math]::Min($bitmap.Height, [int][Math]::Ceiling($Box.bottom))
        Assert-True ($right -gt $left -and $bottom -gt $top) "$ControlName pixel box is outside screenshot: $Relative"
        $bluePixels = 0
        for ($y = $top; $y -lt $bottom; $y++) {
            for ($x = $left; $x -lt $right; $x++) {
                $pixel = $bitmap.GetPixel($x, $y)
                if ($pixel.B -ge 170 -and $pixel.G -ge 70 -and $pixel.R -le 100 -and ($pixel.B - $pixel.R) -ge 90) {
                    $bluePixels++
                }
            }
        }
        Assert-True ($bluePixels -ge 5) "$ControlName is not visibly captured: $Relative blue_pixels=$bluePixels"
    }
    finally { $bitmap.Dispose() }
}

$ledgerPath = Resolve-RepoPath '03_PAGE_LEDGER.csv'
$clickGraphPath = Resolve-RepoPath '17_RUNTIME_CLICK_GRAPH.csv'
Assert-True (Test-Path -LiteralPath $ledgerPath -PathType Leaf) 'page ledger missing'
Assert-True (Test-Path -LiteralPath $clickGraphPath -PathType Leaf) 'runtime click graph missing'
$ledger = @(Import-Csv -LiteralPath $ledgerPath)
$clickGraph = @(Import-Csv -LiteralPath $clickGraphPath)
$nonAndroidInScope = @($ledger | Where-Object { $_.scope -eq 'IN_SCOPE' -and $_.family -ne 'android' })
$businessH5InScope = @($nonAndroidInScope | Where-Object { $_.page_id -ne 'DESKTOP-SPLASH' })
Assert-True ($nonAndroidInScope.Count -eq 39) "authoritative non-Android IN_SCOPE count drifted: $($nonAndroidInScope.Count)"
Assert-True ($businessH5InScope.Count -eq 38) "authoritative business H5 IN_SCOPE count drifted: $($businessH5InScope.Count)"
$evidenceMapPath = Resolve-RepoPath 'evidence/R09/global-closeout/R09_GLOBAL_VISUAL_EVIDENCE_MAP.csv'
Assert-True (Test-Path -LiteralPath $evidenceMapPath -PathType Leaf) 'global visual evidence map missing'
$evidenceMap = @(Import-Csv -LiteralPath $evidenceMapPath)
Assert-True ($evidenceMap.Count -eq 39) "global visual evidence map count drifted: $($evidenceMap.Count)"
Assert-True (@($evidenceMap | Group-Object page_id | Where-Object Count -ne 1).Count -eq 0) 'global visual evidence map contains duplicate page IDs'
$ledgerIds = @($nonAndroidInScope.page_id | Sort-Object)
$evidenceIds = @($evidenceMap.page_id | Sort-Object)
Assert-True (($ledgerIds -join "`n") -ceq ($evidenceIds -join "`n")) 'global visual evidence map does not exactly match H5 IN_SCOPE ledger'
foreach ($entry in $evidenceMap) {
    Assert-True ($entry.status -eq 'PASS') "visual evidence map item is not PASS: $($entry.page_id)"
    Assert-True (Test-Path -LiteralPath (Resolve-RepoPath $entry.primary_evidence) -PathType Leaf) "visual evidence file missing: $($entry.page_id) -> $($entry.primary_evidence)"
}
$notVerified = @($nonAndroidInScope | Where-Object { $_.evidence_status -ne 'REDESIGNED_VERIFIED' })
Assert-True ($notVerified.Count -eq 0) "non-Android IN_SCOPE entries not REDESIGNED_VERIFIED: $($notVerified.page_id -join ', ')"

$clickedChildren = @($clickGraph | Where-Object { $_.clicked -eq 'true' } | Select-Object -ExpandProperty child_page_id -Unique)
$rootReached = @('DESKTOP-SPLASH', 'AUTH-HOME')
foreach ($page in $nonAndroidInScope) {
    if ($rootReached -contains $page.page_id) { continue }
    Assert-True ($clickedChildren -contains $page.page_id) "IN_SCOPE page lacks a clicked parent edge: $($page.page_id)"
}

$audit = Read-Json 'evidence/R09/global-closeout/r08-three-viewport/R08-THREE-VIEWPORT-AUDIT.json'
Assert-True ($audit.status -eq 'PASS') 'R08 three-viewport audit is not PASS'
Assert-True ($audit.capture_contract -eq 'CDP_PRIMED_EXPLICIT_CSS_CLIP') 'R08 screenshot capture contract missing or stale'
Assert-True (@($audit.results).Count -eq 15) "R08 audit result count is not 15: $(@($audit.results).Count)"
foreach ($result in @($audit.results)) {
    $relative = "evidence/R09/global-closeout/r08-three-viewport/$($result.png)"
    Assert-PngSize $relative ([int]$result.requested.width) ([int]$result.requested.height)
    Assert-True (-not [bool]$result.overflowX) "horizontal overflow: $($result.page_id) $($result.requested.width)x$($result.requested.height)"
    Assert-True (@($result.outOfBoundsVisibleElements).Count -eq 0) "out-of-bounds element: $($result.page_id) $($result.requested.width)x$($result.requested.height)"
    Assert-True (@($result.publicUiCdn).Count -eq 0) "public UI CDN: $($result.page_id)"
    Assert-True ([int]$result.oldBrandCount -eq 0) "old visible brand: $($result.page_id)"
    Assert-True ([int]$result.visibleBrokenImages -eq 0) "visible broken image: $($result.page_id)"
}

$notice = Read-Json 'evidence/R09/global-closeout/r08-three-viewport/R08-SIGN-NOTICE-FINAL.json'
Assert-True ($notice.status -eq 'PASS') 'sign notice audit is not PASS'
Assert-True ($notice.capture_contract -eq 'CDP_PRIMED_EXPLICIT_CSS_CLIP') 'sign notice screenshot capture contract missing or stale'
Assert-True (@($notice.results).Count -eq 3) "sign notice result count is not 3: $(@($notice.results).Count)"
foreach ($result in @($notice.results)) {
    $width = [int]$result.requested.width
    $height = [int]$result.requested.height
    $relative = "evidence/R09/global-closeout/r08-three-viewport/notice-after-final-${width}x${height}.png"
    Assert-PngSize $relative $width $height
    Assert-True (-not [bool]$result.root.overflowX) "sign notice root overflow: ${width}x${height}"
    Assert-True ($result.box.left -ge 0 -and $result.box.right -le $width -and $result.box.top -ge 0 -and $result.box.bottom -le $height) "sign notice modal outside viewport: ${width}x${height}"
    Assert-True ($result.close.width -ge 44 -and $result.close.height -ge 44 -and $result.close.right -le $width) "sign notice close target invalid: ${width}x${height}"
    Assert-True (@($result.buttons).Count -eq 2) "sign notice navigation count invalid: ${width}x${height}"
    foreach ($button in @($result.buttons)) {
        Assert-True ($button.height -ge 44 -and $button.left -ge 0 -and $button.right -le $width -and $button.bottom -le $height) "sign notice navigation target invalid: ${width}x${height}"
    }
    Assert-True ($result.content.scrollWidth -le $result.content.clientWidth) "sign notice content horizontal scroll: ${width}x${height}"
    Assert-BluePixels $relative $result.close 'close control'
    foreach ($button in @($result.buttons)) { Assert-BluePixels $relative $button 'notice navigation control' }
}

$publish = Read-Json 'evidence/R09/global-closeout/publish-three-viewport/R09-PUBLISH-THREE-VIEWPORT-AUDIT.json'
Assert-True ($publish.status -eq 'PASS') 'publish visual audit is not PASS'
Assert-True ($publish.capture_contract -eq 'BROWSER_VIEWPORT_ONLY_EXPLICIT_CDP_CLIP') 'publish screenshot capture contract missing or stale'
Assert-True ($publish.interaction -eq 'READ_ONLY_NO_FORM_SUBMIT') 'publish audit interaction boundary missing'
Assert-True (@($publish.results).Count -eq 3) "publish result count is not 3: $(@($publish.results).Count)"
foreach ($result in @($publish.results)) {
    $width = [int]$result.requested.width
    $height = [int]$result.requested.height
    $relative = "evidence/R09/global-closeout/publish-three-viewport/$($result.png)"
    Assert-PngSize $relative $width $height
    Assert-True (-not [bool]$result.overflowX) "publish horizontal overflow: ${width}x${height}"
    Assert-True (@($result.outOfBoundsVisibleElements).Count -eq 0) "publish out-of-bounds element: ${width}x${height}"
    Assert-True (@($result.publicUiCdn).Count -eq 0) "publish public UI CDN: ${width}x${height}"
    Assert-True ([int]$result.visibleBrokenImages -eq 0) "publish visible broken image: ${width}x${height}"
    Assert-True ([int]$result.visibleOldBrand -eq 0) "publish old visible brand: ${width}x${height}"
    Assert-True ([int]$result.headerCount -eq 1) "publish header count invalid: ${width}x${height}"
    Assert-True ([Math]::Abs([double]$result.header.height - 60) -lt 0.5) "publish header height invalid: ${width}x${height}"
    Assert-True ($result.submit.width -ge 64 -and $result.submit.height -ge 44 -and $result.submit.right -le $width) "publish submit target invalid: ${width}x${height}"
    Assert-True ($result.submitLabel.left -ge $result.submit.left -and $result.submitLabel.right -le $result.submit.right -and $result.submitLabel.top -ge $result.submit.top -and $result.submitLabel.bottom -le $result.submit.bottom) "publish submit label clipped: ${width}x${height}"
    Assert-True ([double]$result.leadingContentGap -le 40) "publish leading empty gap returned: ${width}x${height}"
    Assert-True ($result.bottomHit -eq 'page__bd') "publish screenshot bottom does not map to page body: ${width}x${height}"
    Assert-BluePixels $relative $result.submit 'publish submit control'
}

$invalidAtRoot = @(Get-ChildItem -LiteralPath (Resolve-RepoPath 'evidence/R09/global-closeout/r08-three-viewport') -File -Filter '*bordered-preview*')
Assert-True ($invalidAtRoot.Count -eq 0) 'rejected capture remains in final evidence root'
Assert-PngSize 'evidence/R09/global-closeout/r08-three-viewport/R08-THREE-VIEWPORT-FINAL-CONTACT-SHEET.png' 614 2210

Write-Host '[R09-GLOBAL-CLOSEOUT] PASS'
Write-Host '[R09-GLOBAL-CLOSEOUT] non_android_in_scope=39 business_h5=38 desktop_entry=1 redesigned_verified=39 evidence_map=39 r08_results=15 notice_viewports=3 publish_viewports=3'
