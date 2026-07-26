param(
    [string]$BaselineRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) '..\_r04_baseline')
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$overlay = Join-Path $root 'r04-site-overlay'
$BaselineRoot = [System.IO.Path]::GetFullPath($BaselineRoot)

function Fail([string]$Message) {
    throw "[R04-STATIC] FAIL: $Message"
}

function Assert-True([bool]$Condition, [string]$Message) {
    if (-not $Condition) { Fail $Message }
}

function Read-Normalized([string]$Path) {
    if (-not (Test-Path -LiteralPath $Path -PathType Leaf)) {
        Fail "missing file: $Path"
    }
    return (([System.IO.File]::ReadAllText($Path) -replace "`r`n", "`n").TrimEnd([char]10))
}

function Get-ControlProtocol([string]$Text) {
    $tags = [regex]::Matches(
        $Text,
        '<(?<tag>form|input|button|select|textarea|a)\b(?<attrs>[^>]*)>',
        [System.Text.RegularExpressions.RegexOptions]::IgnoreCase -bor
            [System.Text.RegularExpressions.RegexOptions]::Singleline
    )
    $attributes = @(
        'action', 'method', 'name', 'id', 'type', 'value', 'href',
        'target', 'onclick', 'data-id', 'data-loadingurl', 'data-save'
    )
    return @($tags | ForEach-Object {
        $parts = [System.Collections.Generic.List[string]]::new()
        $parts.Add($_.Groups['tag'].Value.ToLowerInvariant())
        $raw = $_.Groups['attrs'].Value
        foreach ($attribute in $attributes) {
            $match = [regex]::Match(
                $raw,
                "(?i)(?:^|\s)$([regex]::Escape($attribute))\s*=\s*(?:`"(?<dq>[^`"]*)`"|'(?<sq>[^']*)'|(?<bare>[^\s>]+))"
            )
            if ($match.Success) {
                $value = $match.Groups['dq'].Value
                if (-not $match.Groups['dq'].Success) { $value = $match.Groups['sq'].Value }
                if (-not $match.Groups['dq'].Success -and -not $match.Groups['sq'].Success) {
                    $value = $match.Groups['bare'].Value
                }
                $parts.Add("$attribute=$value")
            }
        }
        foreach ($booleanAttribute in @('checked', 'required', 'disabled', 'multiple')) {
            if ([regex]::IsMatch($raw, "(?i)(?:^|\s)$booleanAttribute(?:\s|=|$)")) {
                $parts.Add($booleanAttribute)
            }
        }
        $parts -join '|'
    })
}

Assert-True (Test-Path -LiteralPath $overlay -PathType Container) 'R04 overlay is absent'
Assert-True (Test-Path -LiteralPath $BaselineRoot -PathType Container) 'R04 baseline is absent'

$templateFiles = @(
    'source\plugin\xigua_hb\template\touch\index.php',
    'source\plugin\xigua_hb\template\touch\cat.php',
    'source\plugin\xigua_hb\template\touch\tab1.php'
)
$cssRelative = 'source\plugin\xigua_hb\static\tgb-r04\discovery-light-grid-r04.css'
$jsRelative = 'source\plugin\xigua_hb\static\tgb-r04\discovery-r04.js'
$expectedFiles = @($templateFiles) + $cssRelative + $jsRelative
$actualFiles = @(Get-ChildItem -LiteralPath $overlay -Recurse -File | ForEach-Object {
    $_.FullName.Substring($overlay.Length + 1)
})
Assert-True ($actualFiles.Count -eq $expectedFiles.Count) "overlay file count is $($actualFiles.Count), expected $($expectedFiles.Count)"
Assert-True (($actualFiles | Sort-Object) -join "`n" -ceq (($expectedFiles | Sort-Object) -join "`n")) 'overlay allowlist mismatch'

foreach ($relativePath in $templateFiles) {
    $baselineText = Read-Normalized (Join-Path $BaselineRoot $relativePath)
    $overlayText = Read-Normalized (Join-Path $overlay $relativePath)
    $baselineProtocol = @(Get-ControlProtocol $baselineText)
    $overlayProtocol = @(Get-ControlProtocol $overlayText)
    Assert-True (($baselineProtocol -join "`n") -ceq ($overlayProtocol -join "`n")) "control protocol changed: $relativePath"
}

$indexText = Read-Normalized (Join-Path $overlay $templateFiles[0])
$catText = Read-Normalized (Join-Path $overlay $templateFiles[1])
$tabText = Read-Normalized (Join-Path $overlay $templateFiles[2])
$cssPath = Join-Path $overlay $cssRelative
$cssText = Read-Normalized $cssPath
$jsPath = Join-Path $overlay $jsRelative
$jsText = Read-Normalized $jsPath
$cssLink = 'source/plugin/xigua_hb/static/tgb-r04/discovery-light-grid-r04.css?v=20260726-r04-2'
$jsLink = 'source/plugin/xigua_hb/static/tgb-r04/discovery-r04.js?v=20260726-r04-2'

Assert-True ($indexText.Contains($cssLink)) 'R04 CSS link missing from home'
Assert-True ($catText.Contains($cssLink)) 'R04 CSS link missing from category/search'
Assert-True ($indexText.Contains($jsLink)) 'R04 discovery script missing from home'
Assert-True ($catText.Contains($jsLink)) 'R04 discovery script missing from category/search'
Assert-True ($indexText.Contains('name="keyword"')) 'home search keyword field missing'
Assert-True ($indexText.Contains('name="ac" value="cat"')) 'home search route missing'
Assert-True ($indexText.Contains("ac=list_item&inajax=1&from=index")) 'home list AJAX route missing'
Assert-True ($catText.Contains("ac=list_item&inajax=1&pagesize=20&page=")) 'category list AJAX route missing'
Assert-True ($catText.Contains('name="keyword"')) 'category search keyword field missing'
Assert-True ($tabText.Contains('plugin.php?id=view&modac=sign')) 'sign route missing'
Assert-True ($tabText.Contains('plugin.php?id=tb_cus_pipei')) 'dividend route missing'
Assert-True ($tabText.Contains('plugin.php?id=xigua_hb&ac=my')) 'account route missing'
Assert-True (-not $tabText.Contains("if (href && currentPath.includes")) 'orphan tab URL matcher remains'
Assert-True ($indexText.Contains('if (!tabs.length || !indicator || !activeTab)')) 'home indicator null guard missing'
Assert-True (-not $cssText.Contains('[style*="height:0px"]')) 'legacy hidden home categories must remain hidden'

foreach ($required in @(
    '--tg-bg: #f4f7fb',
    '--tg-primary: #2764ff',
    '--tg-mint: #19b8a9',
    'safe-area-inset-top',
    'safe-area-inset-bottom',
    'font-size: 16px',
    'min-height: 44px',
    'prefers-reduced-motion',
    'overflow-x: hidden'
)) {
    Assert-True ($cssText.Contains($required)) "R04 CSS requirement missing: $required"
}
foreach ($forbidden in @('#ff7b00', '#f0b90b', '#fef9f0', '#fff5e6', 'border-radius: 999px')) {
    Assert-True (-not $cssText.Contains($forbidden)) "legacy discovery visual remains: $forbidden"
}
Assert-True ((Get-Item -LiteralPath $cssPath).Length -lt 32768) 'R04 CSS exceeds 32 KiB'
Assert-True ($jsText.Contains("document.getElementById('list')")) 'R04 list observer target missing'
Assert-True ($jsText.Contains("noMore.classList.remove('hidden')")) 'R04 empty-state fallback missing'
Assert-True ($jsText.Contains('MutationObserver')) 'R04 dynamic-fragment observer missing'
Assert-True (-not [regex]::IsMatch($jsText, '(?i)XMLHttpRequest|\bfetch\s*\(|\.ajax\s*\(')) 'R04 visual helper must not issue requests'
Assert-True ((Get-Item -LiteralPath $jsPath).Length -lt 4096) 'R04 discovery script exceeds 4 KiB'

$ownedText = $indexText + "`n" + $catText + "`n" + $tabText + "`n" + $cssText + "`n" + $jsText
Assert-True (-not [regex]::IsMatch($ownedText, '(?i)cdn\.tailwindcss\.com|cdn\.jsdelivr\.net|cdnjs\.cloudflare\.com|unpkg\.com|fonts\.googleapis\.com')) 'public UI CDN dependency detected'

Write-Output '[R04-STATIC] PASS'
Write-Output "[R04-STATIC] overlay_files=$($actualFiles.Count) css_bytes=$((Get-Item -LiteralPath $cssPath).Length)"
Write-Output '[R04-STATIC] protocol=UNCHANGED local_ui_assets=PASS home_js_guards=PASS'
