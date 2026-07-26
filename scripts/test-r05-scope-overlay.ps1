param(
    [string]$OverlayRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'r05-site-overlay-v4'),
    [string]$BaselineRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'r05-baseline-selected')
)

$ErrorActionPreference = 'Stop'
$OverlayRoot = [System.IO.Path]::GetFullPath($OverlayRoot)
$BaselineRoot = [System.IO.Path]::GetFullPath($BaselineRoot)
$expected = @(
    'source/plugin/xigua_hb/template/touch/manage.php',
    'source/plugin/xigua_hb/template/touch/mypub.php',
    'source/plugin/xigua_hb/template/touch/mypub_item.php',
    'source/plugin/xigua_hb/template/touch/mypub_item_new.php',
    'source/plugin/xigua_hb/template/touch/pub.php',
    'source/plugin/xigua_hb/template/touch/pub_selects.php',
    'source/plugin/xigua_hb/template/touch/pub_twoselects.php'
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

$forbiddenDomains = @('cdn\.tailwindcss\.com','cdn\.jsdelivr\.net','cdnjs\.cloudflare\.com','unpkg\.com','fonts\.googleapis\.com','use\.fontawesome\.com')
foreach ($file in $files) {
    Assert-True (($file.Attributes -band [System.IO.FileAttributes]::ReparsePoint) -eq 0) "reparse point is forbidden: $($file.FullName)"
    $relative = $file.FullName.Substring($prefix.Length)
    $normalized = $relative.Replace('\','/')
    $baseline = Join-Path $BaselineRoot $relative
    $before = Read-Normalized $baseline
    $after = Read-Normalized $file.FullName
    Assert-True ($before -cne $after) "redundant unchanged file: $normalized"
    Assert-True (((Get-ControlProtocol $before) -join "`n") -ceq ((Get-ControlProtocol $after) -join "`n")) "control protocol changed: $normalized"
    Assert-True (((Get-TemplateFlow $before) -join "`n") -ceq ((Get-TemplateFlow $after) -join "`n")) "template flow changed: $normalized"
    Assert-True (((Get-BusinessUrls $before) -join "`n") -ceq ((Get-BusinessUrls $after) -join "`n")) "business URL sequence changed: $normalized"
    Assert-True (((Get-BusinessScripts $before) -join "`n---SCRIPT---`n") -ceq ((Get-BusinessScripts $after) -join "`n---SCRIPT---`n")) "business scripts changed: $normalized"
    Assert-True (-not [regex]::IsMatch($after, ':has\s*\(', 'IgnoreCase')) "unsupported :has selector: $normalized"
    foreach ($domain in $forbiddenDomains) {
        $beforeCount = [regex]::Matches($before, $domain, 'IgnoreCase').Count
        $afterCount = [regex]::Matches($after, $domain, 'IgnoreCase').Count
        Assert-True ($afterCount -le $beforeCount) "public UI CDN count increased: $normalized / $domain"
    }
}

$combined = ($files | ForEach-Object { Read-Normalized $_.FullName }) -join "`n"
foreach ($forbidden in @('mycomment.php','mycover.php','member_fav_li.php','lane-a-light-grid-r05.css','card-light-grid-r05.css','report-light-grid-r05.css','tgb-r05-card-page','tgb-r05-report-page')) {
    Assert-True (-not $combined.Contains($forbidden)) "out-of-scope marker remains: $forbidden"
}
foreach ($commentSelector in @('.cmt-wrap','.cmt-list','.view-content-comment-text')) {
    $manage = Read-Normalized (Join-Path $OverlayRoot 'source/plugin/xigua_hb/template/touch/manage.php')
    $mypub = Read-Normalized (Join-Path $OverlayRoot 'source/plugin/xigua_hb/template/touch/mypub.php')
    Assert-True (-not ($manage.Contains($commentSelector) -or $mypub.Contains($commentSelector))) "hidden comment selector remains: $commentSelector"
}

Write-Host '[R05-SCOPE-OVERLAY] PASS'
Write-Host '[R05-SCOPE-OVERLAY] files=7 scope=CLICK_PROVEN_ONLY protocol=UNCHANGED flow=UNCHANGED urls=UNCHANGED scripts=UNCHANGED'
Write-Host '[R05-SCOPE-OVERLAY] comments=NO_OVERRIDE cards=NO_OVERRIDE report=NO_OVERRIDE dormant=NO_OVERRIDE public_ui_cdn=NO_INCREASE'
