param(
    [string]$OverlayRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'r08-site-overlay'),
    [string]$BaselineRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'r08-baseline-selected')
)

$ErrorActionPreference = 'Stop'
$OverlayRoot = [IO.Path]::GetFullPath($OverlayRoot)
$BaselineRoot = [IO.Path]::GetFullPath($BaselineRoot)
$expected = @(
    'done/app.html',
    'done/tgb-r08-app-download.css',
    'source/plugin/tb_cus_pipei/static/tgb-r08/dividend-light-grid-r08.css',
    'source/plugin/tb_cus_pipei/template/touch/main.htm',
    'source/plugin/view/module/site/sign.php',
    'source/plugin/view/static/tgb-r08/sign-light-grid-r08.css',
    'source/plugin/xigua_hh/static/tgb-r08/growth-light-grid-r08.css',
    'source/plugin/xigua_hh/template/touch/fans_li.php',
    'source/plugin/xigua_hh/template/touch/invite.php',
    'source/plugin/xigua_hh/template/touch/myfans.php'
)

function Fail([string]$Message) { throw "[R08-OVERLAY] FAIL: $Message" }
function Assert-True([bool]$Condition, [string]$Message) { if (-not $Condition) { Fail $Message } }
function Read-Text([string]$Path) {
    Assert-True (Test-Path -LiteralPath $Path -PathType Leaf) "missing file: $Path"
    return ([IO.File]::ReadAllText($Path) -replace "`r`n", "`n")
}
function Get-Protocol([string]$Text) {
    $tags = [regex]::Matches($Text, '<(?<tag>form|input|button|select|textarea|a)\b(?<attrs>[^>]*)>', 'IgnoreCase,Singleline')
    $attributes = @('action','method','name','id','type','value','href','target','onclick','onchange','onsubmit','data-id','data-url')
    return @($tags | ForEach-Object {
        $parts = [Collections.Generic.List[string]]::new()
        $parts.Add($_.Groups['tag'].Value.ToLowerInvariant())
        $raw = $_.Groups['attrs'].Value
        foreach ($attribute in $attributes) {
            $match = [regex]::Match($raw, "(?i)(?:^|\s)$([regex]::Escape($attribute))\s*=\s*(?:`"(?<dq>[^`"]*)`"|'(?<sq>[^']*)'|(?<bare>[^\s>]+))")
            if ($match.Success) {
                $value = $match.Groups['dq'].Value
                if (-not $match.Groups['dq'].Success) { $value = $match.Groups['sq'].Value }
                if (-not $match.Groups['dq'].Success -and -not $match.Groups['sq'].Success) { $value = $match.Groups['bare'].Value }
                $parts.Add("$attribute=$value")
            }
        }
        $parts -join '|'
    })
}
function Get-Flow([string]$Text) {
    return @([regex]::Matches($Text, '(?s)<!--\{\s*(?<kind>/?if|elseif|else|/?loop|eval|template|subtemplate|hook)\b.*?\}-->|\{\s*(?<plain>/?if|elseif|else|/?loop|eval|template|subtemplate|hook)\b.*?\}', 'IgnoreCase') | ForEach-Object {
        $kind = $_.Groups['kind'].Value
        if (-not $kind) { $kind = $_.Groups['plain'].Value }
        $kind.ToLowerInvariant()
    })
}
function Get-BusinessScripts([string]$Text) {
    return @([regex]::Matches($Text, '(?is)<script\b[^>]*>.*?</script>') | ForEach-Object {
        $script = $_.Value.Trim() -replace "`r`n", "`n"
        if ($script -match 'cdn\.tailwindcss\.com|chart\.js|tailwind\.config') { return }
        $script
    })
}

$prefix = $OverlayRoot.TrimEnd([IO.Path]::DirectorySeparatorChar) + [IO.Path]::DirectorySeparatorChar
$files = @(Get-ChildItem -LiteralPath $OverlayRoot -Recurse -File)
$actual = @($files | ForEach-Object { $_.FullName.Substring($prefix.Length).Replace('\','/') } | Sort-Object)
Assert-True (($actual -join "`n") -ceq (($expected | Sort-Object) -join "`n")) 'exact five-page allowlist changed'

$templates = @($files | Where-Object Extension -in @('.php','.htm','.html'))
foreach ($file in $templates) {
    $relative = $file.FullName.Substring($prefix.Length)
    $before = Read-Text (Join-Path $BaselineRoot $relative)
    $after = Read-Text $file.FullName
    Assert-True ($before -cne $after) "unchanged template: $relative"
    Assert-True (((Get-Protocol $before) -join "`n") -ceq ((Get-Protocol $after) -join "`n")) "form/link protocol changed: $relative"
    Assert-True (((Get-Flow $before) -join "`n") -ceq ((Get-Flow $after) -join "`n")) "template flow changed: $relative"
    Assert-True (((Get-BusinessScripts $before) -join "`n---SCRIPT---`n") -ceq ((Get-BusinessScripts $after) -join "`n---SCRIPT---`n")) "business script changed: $relative"
}

$combined = ($files | ForEach-Object { Read-Text $_.FullName }) -join "`n"
foreach ($forbidden in @('cdn.tailwindcss.com','cdn.jsdelivr.net','cdnjs.cloudflare.com','unpkg.com','fonts.googleapis.com','use.fontawesome.com')) {
    Assert-True (-not $combined.Contains($forbidden)) "public UI CDN remains: $forbidden"
}
foreach ($cssFile in @($files | Where-Object Extension -eq '.css')) {
    $css = Read-Text $cssFile.FullName
    Assert-True (-not [regex]::IsMatch($css, '(?i)https?://|@import|:has\s*\(')) "non-local or unsupported CSS: $($cssFile.Name)"
}

$signCss = Read-Text (Join-Path $OverlayRoot 'source/plugin/view/static/tgb-r08/sign-light-grid-r08.css')
$signTemplate = Read-Text (Join-Path $OverlayRoot 'source/plugin/view/module/site/sign.php')
Assert-True ($signCss.Contains('.tgb-r08-sign-page .promo-highlight::after')) 'sign promotion badge containment selector missing'
Assert-True ($signCss.Contains('right: 4px !important')) 'sign promotion badge can escape the 360px viewport'
Assert-True ($signCss.Contains('animation: none !important')) 'sign promotion badge scale animation remains active'
Assert-True ($signCss.Contains('transform: none !important')) 'sign promotion badge transform remains active'
Assert-True ($signCss.Contains('.tgb-r08-sign-page #noticeModal .modal-box')) 'sign notice modal viewport containment missing'
Assert-True ($signCss.Contains('max-height: calc(100vh - 96px) !important')) 'sign notice modal height bound missing'
Assert-True ($signCss.Contains('.tgb-r08-sign-page #noticeModal .modal-close::before')) 'sign notice modal local close glyph missing'
Assert-True ($signCss.Contains('overflow-wrap: anywhere')) 'sign notice long-link wrapping missing'
Assert-True ($signTemplate.Contains('sign-light-grid-r08.css?v=20260727-r09-1')) 'sign badge repair cache key missing'

Write-Host '[R08-OVERLAY] PASS'
Write-Host '[R08-OVERLAY] files=10 pages=5 scope=CLICK_PROVEN_ONLY protocol=UNCHANGED flow=UNCHANGED business_scripts=UNCHANGED'
