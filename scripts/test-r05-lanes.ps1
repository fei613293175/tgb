param(
    [string]$BaselineRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'r05-baseline-selected')
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$BaselineRoot = [System.IO.Path]::GetFullPath($BaselineRoot)
$laneRoots = @(
    (Join-Path $root 'r05-site-overlay-lane-a'),
    (Join-Path $root 'r05-site-overlay-lane-b'),
    (Join-Path $root 'r05-site-overlay-lane-c')
)
$expectedFiles = @{
    'r05-site-overlay-lane-a' = @(
        'source/plugin/xigua_hb/static/tgb-r05/lane-a-light-grid-r05.css',
        'source/plugin/xigua_hb/template/touch/hong_li.php',
        'source/plugin/xigua_hb/template/touch/hong_list.php',
        'source/plugin/xigua_hb/template/touch/jl_jy.php',
        'source/plugin/xigua_hb/template/touch/jl_jy_v.php',
        'source/plugin/xigua_hb/template/touch/member_li.php',
        'source/plugin/xigua_hb/template/touch/member_new.php'
    )
    'r05-site-overlay-lane-b' = @(
        'source/plugin/xigua_hb/template/touch/comment_li_01.php',
        'source/plugin/xigua_hb/template/touch/comment_li_01_sub.php',
        'source/plugin/xigua_hb/template/touch/fav.php',
        'source/plugin/xigua_hb/template/touch/manage.php',
        'source/plugin/xigua_hb/template/touch/member_fav_li.php',
        'source/plugin/xigua_hb/template/touch/mycomment.php',
        'source/plugin/xigua_hb/template/touch/mycover.php',
        'source/plugin/xigua_hb/template/touch/mypub.php',
        'source/plugin/xigua_hb/template/touch/mypub_item.php',
        'source/plugin/xigua_hb/template/touch/mypub_item_new.php',
        'source/plugin/xigua_hb/template/touch/pub.php',
        'source/plugin/xigua_hb/template/touch/pub_selects.php',
        'source/plugin/xigua_hb/template/touch/pub_twoselects.php'
    )
    'r05-site-overlay-lane-c' = @(
        'source/plugin/tb_cus_card/static/tgb-r05/card-light-grid-r05.css',
        'source/plugin/tb_cus_card/template/touch/add.htm',
        'source/plugin/tb_cus_card/template/touch/shownext.htm',
        'source/plugin/xigua_hj/static/tgb-r05/report-light-grid-r05.css',
        'source/plugin/xigua_hj/template/touch/index.php'
    )
}

function Fail([string]$Message) {
    throw "[R05-LANES] FAIL: $Message"
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
        'action', 'method', 'name', 'id', 'type', 'value', 'href', 'target',
        'onclick', 'onchange', 'onsubmit', 'data-id', 'data-loadingurl',
        'data-save', 'data-href', 'data-url'
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

function Get-TemplateFlow([string]$Text) {
    $matches = [regex]::Matches(
        $Text,
        '(?s)<!--\{\s*(?<kind>/?if|elseif|else|/?loop|eval|template|subtemplate|hook)\b.*?\}-->|\{\s*(?<plain>/?if|elseif|else|/?loop|eval|template|subtemplate|hook)\b.*?\}',
        [System.Text.RegularExpressions.RegexOptions]::IgnoreCase
    )
    return @($matches | ForEach-Object {
        $kind = $_.Groups['kind'].Value
        if (-not $kind) { $kind = $_.Groups['plain'].Value }
        $kind.ToLowerInvariant()
    })
}

function Get-BusinessUrls([string]$Text) {
    return @([regex]::Matches(
        $Text,
        '(?i)(?:plugin|forum|member|home|api|misc|connect|uc)\.php\?[^\s`"''<>()]+'
    ) | ForEach-Object Value)
}

function Get-BusinessScripts([string]$Text) {
    return @([regex]::Matches(
        $Text,
        '(?is)<script\b[^>]*>.*?</script>'
    ) | ForEach-Object {
        $script = $_.Value.Trim()
        $allowedScopeScript = '(?is)^<script>\s*(?:document\.(?:body|documentElement)\.classList\.add\(["'']tgb-r05-[a-z0-9-]+["'']\);\s*)+(?:document\.title\s*=\s*["'']推广宝["''];\s*)?</script>$'
        if ($script -notmatch $allowedScopeScript) {
            $script -replace "`r`n", "`n"
        }
    })
}

function Count-Pattern([string]$Text, [string]$Pattern) {
    return [regex]::Matches($Text, $Pattern, [System.Text.RegularExpressions.RegexOptions]::IgnoreCase).Count
}

Assert-True (Test-Path -LiteralPath $BaselineRoot -PathType Container) 'baseline root is absent'
foreach ($lane in $laneRoots) {
    Assert-True (Test-Path -LiteralPath $lane -PathType Container) "lane root is absent: $lane"
}

$templateCount = 0
$cssCount = 0
$forbiddenDomains = @(
    'cdn\.tailwindcss\.com', 'cdn\.jsdelivr\.net', 'cdnjs\.cloudflare\.com',
    'unpkg\.com', 'fonts\.googleapis\.com', 'use\.fontawesome\.com'
)

foreach ($lane in $laneRoots) {
    $laneName = Split-Path -Leaf $lane
    $lanePrefix = $lane.TrimEnd([System.IO.Path]::DirectorySeparatorChar) + [System.IO.Path]::DirectorySeparatorChar
    $laneFiles = @(Get-ChildItem -LiteralPath $lane -Recurse -File)
    $actualFiles = @($laneFiles | ForEach-Object {
        $_.FullName.Substring($lanePrefix.Length).Replace('\', '/')
    } | Sort-Object)
    $ownedFiles = @($expectedFiles[$laneName] | Sort-Object)
    Assert-True (($actualFiles -join "`n") -ceq ($ownedFiles -join "`n")) "exact file ownership changed: $laneName"

    foreach ($file in $laneFiles) {
        $relative = $file.FullName.Substring($lanePrefix.Length)
        $normalizedRelative = $relative.Replace('\', '/')
        Assert-True ($normalizedRelative.StartsWith('source/plugin/')) "file escaped plugin ownership: $normalizedRelative"
        Assert-True (-not $normalizedRelative.Contains('/include/')) "controller edit is forbidden: $normalizedRelative"

        $overlayText = Read-Normalized $file.FullName
        if ($file.Extension -in @('.php', '.htm', '.html')) {
            $templateCount++
            $baselinePath = Join-Path $BaselineRoot $relative
            $baselineText = Read-Normalized $baselinePath

            $baselineProtocol = @(Get-ControlProtocol $baselineText)
            $overlayProtocol = @(Get-ControlProtocol $overlayText)
            Assert-True (($baselineProtocol -join "`n") -ceq ($overlayProtocol -join "`n")) "control protocol changed: $normalizedRelative"

            $baselineFlow = @(Get-TemplateFlow $baselineText)
            $overlayFlow = @(Get-TemplateFlow $overlayText)
            Assert-True (($baselineFlow -join "`n") -ceq ($overlayFlow -join "`n")) "template condition flow changed: $normalizedRelative"

            $baselineUrls = @(Get-BusinessUrls $baselineText)
            $overlayUrls = @(Get-BusinessUrls $overlayText)
            Assert-True (($baselineUrls -join "`n") -ceq ($overlayUrls -join "`n")) "business URL sequence changed: $normalizedRelative"

            $baselineScripts = @(Get-BusinessScripts $baselineText)
            $overlayScripts = @(Get-BusinessScripts $overlayText)
            Assert-True (($baselineScripts -join "`n---SCRIPT---`n") -ceq ($overlayScripts -join "`n---SCRIPT---`n")) "business script sequence changed: $normalizedRelative"

            Assert-True (-not $overlayText.Contains('metadata normalized for UTF-8 tooling')) "non-UI metadata drift: $normalizedRelative"
            Assert-True (-not [regex]::IsMatch($overlayText, ':has\s*\(', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)) "unsupported :has selector: $normalizedRelative"

            $ownedAssetRefs = @([regex]::Matches($overlayText, '(?i)source/plugin/[^\s`"''<>]+/static/tgb-r05/[^\s`"''<>]+'))
            foreach ($assetRef in $ownedAssetRefs) {
                Assert-True ($assetRef.Value -match '\?20260727-r05-[abc][0-9]+$') "R05 asset lacks explicit version key: $normalizedRelative / $($assetRef.Value)"
            }

            foreach ($domain in $forbiddenDomains) {
                Assert-True ((Count-Pattern $overlayText $domain) -le (Count-Pattern $baselineText $domain)) "public UI CDN count increased: $normalizedRelative / $domain"
            }
        } elseif ($file.Extension -eq '.css') {
            $cssCount++
            foreach ($required in @('#2764ff', '#f4f7fb', 'safe-area-inset', 'min-height: 44px', 'overflow-x')) {
                Assert-True ($overlayText.IndexOf($required, [System.StringComparison]::OrdinalIgnoreCase) -ge 0) "CSS requirement missing: $normalizedRelative / $required"
            }
            foreach ($domain in $forbiddenDomains) {
                Assert-True (-not [regex]::IsMatch($overlayText, $domain, [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)) "public UI CDN in CSS: $normalizedRelative"
            }
            foreach ($forbiddenSelector in @(
                '[style*="display:none"]', "[style*='display:none']",
                '[style*="height:0"]', "[style*='height:0']",
                'input[type="hidden"]', "input[type='hidden']"
            )) {
                Assert-True (-not $overlayText.Contains($forbiddenSelector)) "hidden control override is forbidden: $normalizedRelative / $forbiddenSelector"
            }
            Assert-True (-not [regex]::IsMatch($overlayText, '(?is)\.none\s*\{[^}]*display\s*:\s*(block|flex|grid)')) "generic .none override is forbidden: $normalizedRelative"
        } else {
            Fail "unexpected lane file type: $normalizedRelative"
        }
    }
}

Assert-True ($templateCount -eq 22) "template count is $templateCount, expected 22"
Assert-True ($cssCount -eq 3) "CSS file count is $cssCount, expected 3"

Write-Host '[R05-LANES] PASS'
Write-Host "[R05-LANES] templates=$templateCount css=$cssCount protocol=UNCHANGED flow=UNCHANGED urls=UNCHANGED"
Write-Host '[R05-LANES] files=EXACT scripts=UNCHANGED assets=LOCAL_VERSIONED'
Write-Host '[R05-LANES] hidden_controls=GUARDED public_ui_cdn=NO_INCREASE'
