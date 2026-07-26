param(
    [string]$OverlayRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'r05-site-overlay-v5'),
    [string]$BaselineRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'r05-baseline-selected')
)

$ErrorActionPreference = 'Stop'
$OverlayRoot = [System.IO.Path]::GetFullPath($OverlayRoot)
$BaselineRoot = [System.IO.Path]::GetFullPath($BaselineRoot)
$expected = @(
    'source/plugin/xigua_hb/static/tgb-r04/discovery-light-grid-r04.css',
    'source/plugin/xigua_hb/template/touch/manage.php',
    'source/plugin/xigua_hb/template/touch/mypub.php',
    'source/plugin/xigua_hb/template/touch/mypub_item.php',
    'source/plugin/xigua_hb/template/touch/mypub_item_new.php',
    'source/plugin/xigua_hb/template/touch/pub.php',
    'source/plugin/xigua_hb/template/touch/pub_selects.php',
    'source/plugin/xigua_hb/template/touch/pub_twoselects.php',
    'source/plugin/xigua_hb/template/touch/jl_jy_v.php',
    'source/plugin/xigua_hb/static/tgb-r05/detail-light-grid-r05.css',
    'source/plugin/xigua_hj/template/touch/index.php',
    'source/plugin/xigua_hj/static/tgb-r05/report-light-grid-r05.css'
)

function Fail([string]$Message) { throw "[R05-SCOPE-OVERLAY] FAIL: $Message" }
function Assert-True([bool]$Condition, [string]$Message) { if (-not $Condition) { Fail $Message } }
function Read-Normalized([string]$Path) {
    Assert-True (Test-Path -LiteralPath $Path -PathType Leaf) "missing file: $Path"
    return (([System.IO.File]::ReadAllText($Path) -replace "`r`n", "`n").TrimEnd([char]10))
}
function Get-ControlProtocol([string]$Text) {
    $tags = [regex]::Matches($Text, '<(?<tag>form|input|button|select|textarea|a)\b(?<attrs>[^>]*)>', 'IgnoreCase,Singleline')
    $attributes = @('action','method','name','id','type','value','href','target','onclick','onchange','onsubmit','data-id','data-loadingurl','data-save','data-href','data-url')
    return @($tags | ForEach-Object {
        $parts = [System.Collections.Generic.List[string]]::new()
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
        foreach ($booleanAttribute in @('checked','required','disabled','multiple')) {
            if ([regex]::IsMatch($raw, "(?i)(?:^|\s)$booleanAttribute(?:\s|=|$)")) { $parts.Add($booleanAttribute) }
        }
        $parts -join '|'
    })
}
function Get-TemplateFlow([string]$Text) {
    $matches = [regex]::Matches($Text, '(?s)<!--\{\s*(?<kind>/?if|elseif|else|/?loop|eval|template|subtemplate|hook)\b.*?\}-->|\{\s*(?<plain>/?if|elseif|else|/?loop|eval|template|subtemplate|hook)\b.*?\}', 'IgnoreCase')
    return @($matches | ForEach-Object { $kind=$_.Groups['kind'].Value; if(-not $kind){$kind=$_.Groups['plain'].Value}; $kind.ToLowerInvariant() })
}
function Get-BusinessUrls([string]$Text) {
    return @([regex]::Matches($Text, '(?i)(?:plugin|forum|member|home|api|misc|connect|uc)\.php\?[^\s`"''<>()]+') | ForEach-Object Value)
}
function Get-BusinessScripts([string]$Text) {
    return @([regex]::Matches($Text, '(?is)<script\b[^>]*>.*?</script>') | ForEach-Object { $_.Value.Trim() -replace "`r`n", "`n" })
}

Assert-True (Test-Path -LiteralPath $OverlayRoot -PathType Container) 'overlay root is absent'
Assert-True (Test-Path -LiteralPath $BaselineRoot -PathType Container) 'baseline root is absent'
$prefix = $OverlayRoot.TrimEnd([System.IO.Path]::DirectorySeparatorChar) + [System.IO.Path]::DirectorySeparatorChar
$files = @(Get-ChildItem -LiteralPath $OverlayRoot -Recurse -File)
$actual = @($files | ForEach-Object { $_.FullName.Substring($prefix.Length).Replace('\','/') } | Sort-Object)
Assert-True (($actual -join "`n") -ceq (($expected | Sort-Object) -join "`n")) 'exact click-proven allowlist changed'

$homeCssPath = Join-Path $OverlayRoot 'source/plugin/xigua_hb/static/tgb-r04/discovery-light-grid-r04.css'
$homeCss = Read-Normalized $homeCssPath
Assert-True ($homeCss -match '(?s)\.fixbanner_in[^}]*display:\s*none\s*!important') 'hidden home categories must remain display none'
Assert-True ($homeCss -match '(?s)\.weui-navbar__item\.ajaxcat[^}]*pointer-events:\s*none\s*!important') 'hidden home categories must remain non-interactive'

$forbiddenDomains = @('cdn\.tailwindcss\.com','cdn\.jsdelivr\.net','cdnjs\.cloudflare\.com','unpkg\.com','fonts\.googleapis\.com','use\.fontawesome\.com')
foreach ($file in $files) {
    Assert-True (($file.Attributes -band [System.IO.FileAttributes]::ReparsePoint) -eq 0) "reparse point is forbidden: $($file.FullName)"
    $relative = $file.FullName.Substring($prefix.Length)
    $normalized = $relative.Replace('\','/')
    $after = Read-Normalized $file.FullName
    Assert-True (-not [regex]::IsMatch($after, ':has\s*\(', 'IgnoreCase')) "unsupported :has selector: $normalized"
    if ($file.Extension -in @('.php', '.htm', '.html')) {
        $baseline = Join-Path $BaselineRoot $relative
        $before = Read-Normalized $baseline
        Assert-True ($before -cne $after) "redundant unchanged template: $normalized"
        Assert-True (((Get-ControlProtocol $before) -join "`n") -ceq ((Get-ControlProtocol $after) -join "`n")) "control protocol changed: $normalized"
        Assert-True (((Get-TemplateFlow $before) -join "`n") -ceq ((Get-TemplateFlow $after) -join "`n")) "template flow changed: $normalized"
        Assert-True (((Get-BusinessUrls $before) -join "`n") -ceq ((Get-BusinessUrls $after) -join "`n")) "business URL sequence changed: $normalized"
        Assert-True (((Get-BusinessScripts $before) -join "`n---SCRIPT---`n") -ceq ((Get-BusinessScripts $after) -join "`n---SCRIPT---`n")) "business scripts changed: $normalized"
    } else {
        $before = ''
    }
    foreach ($domain in $forbiddenDomains) {
        $beforeCount = [regex]::Matches($before, $domain, 'IgnoreCase').Count
        $afterCount = [regex]::Matches($after, $domain, 'IgnoreCase').Count
        Assert-True ($afterCount -le $beforeCount) "public UI CDN count increased: $normalized / $domain"
    }
}

$combined = ($files | ForEach-Object { Read-Normalized $_.FullName }) -join "`n"
foreach ($forbidden in @('mycomment.php','mycover.php','member_fav_li.php','lane-a-light-grid-r05.css','card-light-grid-r05.css','tgb-r05-card-page')) {
    Assert-True (-not $combined.Contains($forbidden)) "out-of-scope marker remains: $forbidden"
}
foreach ($commentSelector in @('.cmt-wrap','.cmt-list','.view-content-comment-text')) {
    $manage = Read-Normalized (Join-Path $OverlayRoot 'source/plugin/xigua_hb/template/touch/manage.php')
    $mypub = Read-Normalized (Join-Path $OverlayRoot 'source/plugin/xigua_hb/template/touch/mypub.php')
    Assert-True (-not ($manage.Contains($commentSelector) -or $mypub.Contains($commentSelector))) "hidden comment selector remains: $commentSelector"
}

$detailTemplate = Read-Normalized (Join-Path $OverlayRoot 'source/plugin/xigua_hb/template/touch/jl_jy_v.php')
$reportTemplate = Read-Normalized (Join-Path $OverlayRoot 'source/plugin/xigua_hj/template/touch/index.php')
$detailCss = Read-Normalized (Join-Path $OverlayRoot 'source/plugin/xigua_hb/static/tgb-r05/detail-light-grid-r05.css')
$reportCss = Read-Normalized (Join-Path $OverlayRoot 'source/plugin/xigua_hj/static/tgb-r05/report-light-grid-r05.css')
Assert-True ($detailTemplate.Contains('detail-light-grid-r05.css?20260727-r05-v5a')) 'detail template does not load the audited v5 local stylesheet'
Assert-True ($detailTemplate.Contains('class="view tgb-r05-detail"')) 'detail template scope root is absent'
Assert-True ($reportTemplate.Contains('report-light-grid-r05.css?20260727-r05-c2')) 'report template does not load its local stylesheet'
Assert-True ($reportTemplate.Contains('page__bd tgb-r05-report-page')) 'report template scope root is absent'
Assert-True (-not [regex]::IsMatch($detailCss, '(?i)member-page|redpack-page|feed-page|hong_|cmt-|comment|vote|favorite|fav-|tb-cus-card|tgb-r05-card')) 'detail CSS contains an out-of-scope feature selector'
Assert-True (-not [regex]::IsMatch($detailCss, '(?i)\[style[^\]]*(display|visibility)|\.none\b|\.hide\b|\[hidden\]')) 'detail CSS may target a hidden-state marker'
Assert-True (-not [regex]::IsMatch($reportCss, '(?i)member-page|redpack-page|feed-page|hong_|cmt-|comment|vote|favorite|fav-|tb-cus-card|tgb-r05-card')) 'report CSS contains an out-of-scope feature selector'
Assert-True (-not $detailTemplate.Contains('img.imehui.com')) 'detail template still depends on an external imehui icon or badge'
$detailCssLinkOffset = $detailTemplate.LastIndexOf('detail-light-grid-r05.css?20260727-r05-v5a', [System.StringComparison]::Ordinal)
$lastInlineStyleOffset = $detailTemplate.LastIndexOf('</style>', [System.StringComparison]::OrdinalIgnoreCase)
Assert-True ($detailCssLinkOffset -gt $lastInlineStyleOffset) 'detail stylesheet must follow all legacy inline style blocks'
Assert-True ($detailCss.Contains('top: auto !important;')) 'floating warning top offset is not neutralized'
Assert-True ($detailCss.Contains('writing-mode: horizontal-tb !important;')) 'floating warning still risks vertical pill layout'
Assert-True ($detailCss.Contains('transform: none !important;')) 'legacy floating transform is not neutralized'
Assert-True ($detailCss.Contains('animation: none !important;')) 'legacy floating animation is not neutralized'
Assert-True (-not [regex]::IsMatch($detailCss, '(?i)#ff7b00|#e63946|#d35400|#b08968|#8b6f5c|#4a3020|#3d2b1a|#fff5e6|#fef3e2|#fdf0db')) 'detail CSS reintroduces the legacy warm palette'
Assert-True (-not [regex]::IsMatch($reportCss, '(?i)border-radius:\s*(?:1[0-9]|[2-9][0-9])px')) 'report CSS contains oversized pill/card rounding'
foreach ($css in @($detailCss, $reportCss)) {
    Assert-True (-not [regex]::IsMatch($css, '(?i)https?://|@import')) 'v5 CSS must remain local and self-contained'
}

Write-Host '[R05-V5-SCOPE-OVERLAY] PASS'
Write-Host '[R05-V5-SCOPE-OVERLAY] files=12 scope=CLICK_PROVEN_ONLY protocol=UNCHANGED flow=UNCHANGED urls=UNCHANGED scripts=UNCHANGED'
Write-Host '[R05-V5-SCOPE-OVERLAY] detail=SCOPED report=SCOPED hidden_features=NO_OVERRIDE public_ui_cdn=NO_INCREASE'
