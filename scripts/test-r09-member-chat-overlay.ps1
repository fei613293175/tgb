param(
    [string]$RepositoryRoot = (Split-Path -Parent $PSScriptRoot)
)

$ErrorActionPreference = 'Stop'

$originalRoot = Join-Path $RepositoryRoot 'r09-member-chat-baseline-selected\source\plugin'
$overlayRoot = Join-Path $RepositoryRoot 'r09-member-chat-overlay\source\plugin'

$memberOriginal = Join-Path $originalRoot 'xigua_hb\template\touch\member_new.php'
$memberOverlay = Join-Path $overlayRoot 'xigua_hb\template\touch\member_new.php'
$memberHeaderOriginal = Join-Path $originalRoot 'xigua_hb\template\touch\wdk_header.php'
$memberHeaderOverlay = Join-Path $overlayRoot 'xigua_hb\template\touch\wdk_header.php'
$chatOriginal = Join-Path $originalRoot 'xigua_lt\template\touch\chat.php'
$chatOverlay = Join-Path $overlayRoot 'xigua_lt\template\touch\chat.php'
$memberCss = Join-Path $overlayRoot 'xigua_hb\static\tgb-r09\member-detail-light-grid-r09.css'
$chatCss = Join-Path $overlayRoot 'xigua_lt\static\tgb-r09\chat-detail-light-grid-r09.css'
$deployScript = Join-Path $RepositoryRoot 'scripts\remote\r09_deploy_member_chat.sh'
$productionDeployScript = Join-Path $RepositoryRoot 'scripts\remote\r09_deploy_member_chat_production.sh'

$requiredFiles = @($memberOriginal, $memberOverlay, $memberHeaderOriginal, $memberHeaderOverlay, $chatOriginal, $chatOverlay, $memberCss, $chatCss, $deployScript, $productionDeployScript)
foreach ($file in $requiredFiles) {
    if (-not (Test-Path -LiteralPath $file -PathType Leaf)) {
        throw "Required file is missing: $file"
    }
}

$expectedOriginalHashes = @{
    $memberOriginal = 'E787A81AB9306A0DC5D4B97E82DE585D37F71831BEC8AE31603EB0E5C41AFBF8'
    $memberHeaderOriginal = '209171C81201545EF8CE680B255C4E8E36BEAE56C6328C2DEE73B3B68F8E8D3A'
    $chatOriginal = 'B0E370EBCB8AEE006C88E4C26DBB6A1AD57693FD9A245303A20181B51F8857BB'
}

foreach ($entry in $expectedOriginalHashes.GetEnumerator()) {
    $actual = (Get-FileHash -LiteralPath $entry.Key -Algorithm SHA256).Hash
    if ($actual -ne $entry.Value) {
        throw "Original baseline hash drift: $($entry.Key) expected=$($entry.Value) actual=$actual"
    }
}

function Read-NormalizedText([string]$Path) {
    return ([System.IO.File]::ReadAllText($Path)).Replace("`r`n", "`n").TrimEnd("`r", "`n")
}

function Normalize-MemberOverlay([string]$Text) {
    $normalized = $Text
    $normalized = $normalized.Replace('<link rel="stylesheet" href="source/plugin/xigua_hb/static/tgb-r09/member-detail-light-grid-r09.css?v=20260727-r09-3" />' + "`n", '')
    $normalized = $normalized.Replace("<script>document.documentElement.classList.add('tgb-r09-member-detail-page');</script>" + "`n", '')
    $normalized = $normalized.Replace('<div class="page__bd tgb-r09-member-detail">', '<div class="page__bd">')
    $normalized = $normalized.Replace('<a class="yu_sidectrl mem_ctrl1" href="javascript:void(0)" aria-label="更多操作"><i class="iconfont icon-gengduo1" aria-hidden="true"></i></a>', '<a class="yu_sidectrl mem_ctrl1" href="javascript:void(0)"><img style="margin-top:30px;" src="https://img.imehui.com/20250131/1738254658679ba942bd984.png" alt=""></a>')
    $normalized = $normalized.Replace('<div class="tgb-r09-member-cover" aria-hidden="true"><span></span></div>', '<img style="width:100%;height:100%;" src="https://img.imehui.com/20250919/175822898968cc71fdea9f8.jpeg">')
    return $normalized
}

function Normalize-ChatOverlay([string]$Text) {
    $normalized = $Text
    $normalized = $normalized.Replace('<link rel="stylesheet" href="source/plugin/xigua_lt/static/tgb-r09/chat-detail-light-grid-r09.css?v=20260727-r09-4">' + "`n", '')
    $normalized = $normalized.Replace("<script>document.documentElement.classList.add('tgb-r09-chat-detail-page');</script>" + "`n", '')
    $normalized = $normalized.Replace('<div class="page__bd tgb-r09-chat-detail" style="margin-top:35px;">', '<div class="page__bd" style="margin-top:35px;">')
    $normalized = $normalized.Replace('<a class="tgb-r09-chat-report" href="$SCRITPTNAME?id=xigua_hj" style=', '<a href="$SCRITPTNAME?id=xigua_hj" style=')
    return $normalized
}

$memberBefore = Read-NormalizedText $memberOriginal
$memberAfter = Normalize-MemberOverlay (Read-NormalizedText $memberOverlay)
if ($memberBefore -cne $memberAfter) {
    throw 'Member template contains changes outside the approved visual delta.'
}

$memberHeaderOverlayHash = (Get-FileHash -LiteralPath $memberHeaderOverlay -Algorithm SHA256).Hash
if ($memberHeaderOverlayHash -ne 'EA08D382518E7C2ECD4D192708D0F097623FF0396F99340060C4430980CB128C') {
    throw 'Member header contains an unapproved visual delta.'
}
$memberHeaderText = Read-NormalizedText $memberHeaderOverlay
if ($memberHeaderText -notmatch 'javascript:window\.history\.go\(-1\);' -or
    $memberHeaderText -notmatch 'tgb-r09-member-back' -or
    $memberHeaderText -notmatch 'icon-fanhuijiantou') {
    throw 'Member header lost its frozen back-navigation protocol.'
}

$chatBefore = Read-NormalizedText $chatOriginal
$chatAfter = Normalize-ChatOverlay (Read-NormalizedText $chatOverlay)
if ($chatBefore -cne $chatAfter) {
    throw 'Chat template contains changes outside the approved visual delta.'
}

$overlayText = (Read-NormalizedText $memberOverlay) + "`n" + $memberHeaderText + "`n" + (Read-NormalizedText $chatOverlay)
if ($overlayText -match 'img\.imehui\.com|cdn\.tailwindcss\.com|cdnjs\.cloudflare\.com') {
    throw 'A redesigned template still references a forbidden public UI asset host.'
}

if ($overlayText -notmatch 'name="mesasgae"' -or
    $overlayText -notmatch 'accept="image/\*"' -or
    $overlayText -notmatch 'accept="video/\*"' -or
    $overlayText -notmatch "id=xigua_lt&ac=chatcmt&do=comment" -or
    $overlayText -notmatch "id=xigua_lt&ac=chat&do=fetchpm" -or
    $overlayText -notmatch "id=xigua_hb&ac=fav&fav=user" -or
    $overlayText -notmatch "dolahei") {
    throw 'A frozen chat or member business protocol marker is missing.'
}

foreach ($cssFile in @($memberCss, $chatCss)) {
    $cssText = Read-NormalizedText $cssFile
    if ($cssText -match 'url\s*\(\s*["'']?https?://') {
        throw "Public URL found in CSS: $cssFile"
    }
    if ($cssText -match '(?m)^\s*\.(?!tgb-r09)[^{]+\{') {
        throw "Unscoped top-level selector found in CSS: $cssFile"
    }
}

$deployText = Read-NormalizedText $deployScript
if ($deployText -match 'cp -a -- "\$\{BACKUP\}/files/\." "\$\{SITE\}/"' -or
    $deployText -match "cp -a -- '\$\{BACKUP\}/files/\.' '\$\{SITE\}/'") {
    throw 'Rollback must not copy private backup directory metadata into the public site tree.'
}
if ($deployText -notmatch 'member touch directory permission drift' -or
    $deployText -notmatch 'chat touch directory permission drift' -or
    $deployText -notmatch 'runuser -u www -- test -r') {
    throw 'Deployment script is missing the PHP-FPM template readability gate.'
}

$productionDeployText = Read-NormalizedText $productionDeployScript
if ($productionDeployText -notmatch '--verify-only' -or
    $productionDeployText -notmatch '--apply-production' -or
    $productionDeployText -notmatch '--apply-rollback' -or
    $productionDeployText -notmatch 'production-member-chat-backups' -or
    $productionDeployText -notmatch 'runuser -u www -- test -r') {
    throw 'Production deployment script is missing verify, backup, rollback, or readability gates.'
}

Write-Host '[R09-MEMBER-CHAT-GATE] ORIGINAL_HASHES=PASS'
Write-Host '[R09-MEMBER-CHAT-GATE] TEMPLATE_VISUAL_DELTA_ONLY=PASS'
Write-Host '[R09-MEMBER-CHAT-GATE] BUSINESS_PROTOCOL_MARKERS=PASS'
Write-Host '[R09-MEMBER-CHAT-GATE] PUBLIC_UI_CDN=0'
Write-Host '[R09-MEMBER-CHAT-GATE] DEPLOY_PERMISSION_SAFETY=PASS'
Write-Host '[R09-MEMBER-CHAT-GATE] RESULT=PASS'
