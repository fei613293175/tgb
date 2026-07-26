param(
    [string]$OverlayRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'r07-site-overlay'),
    [string]$BaselineRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'r07-baseline-selected')
)

$ErrorActionPreference = 'Stop'
$OverlayRoot = [IO.Path]::GetFullPath($OverlayRoot)
$BaselineRoot = [IO.Path]::GetFullPath($BaselineRoot)
$expected = @(
    'source/plugin/tb_cus_xiguahh/static/tgb-r07/sign-wallet-light-grid-r07.css',
    'source/plugin/tb_cus_xiguahh/template/touch/tx.htm',
    'source/plugin/tb_toutiao/static/tgb-r07/promotion-light-grid-r07.css',
    'source/plugin/tb_toutiao/template/touch/main.htm',
    'source/plugin/tb_toutiao/template/touch/super_main.htm',
    'source/plugin/xigua_hb/static/tgb-r07/finance-light-grid-r07.css',
    'source/plugin/xigua_hb/static/tgb-r07/membership-light-grid-r07.css',
    'source/plugin/xigua_hb/template/touch/mytx.php',
    'source/plugin/xigua_hb/template/touch/qianbao.php',
    'source/plugin/xigua_hb/template/touch/refresh.php',
    'source/plugin/xigua_hb/template/touch/sxtc.php',
    'source/plugin/xigua_hb/template/touch/vip.php'
)

function Fail([string]$Message) { throw "[R07-OVERLAY] FAIL: $Message" }
function Assert-True([bool]$Condition, [string]$Message) { if (-not $Condition) { Fail $Message } }
function Read-Text([string]$Path) {
    Assert-True (Test-Path -LiteralPath $Path -PathType Leaf) "missing file: $Path"
    return ([IO.File]::ReadAllText($Path) -replace "`r`n", "`n")
}
function Get-Protocol([string]$Text) {
    $tags = [regex]::Matches($Text, '<(?<tag>form|input|button|select|textarea|a)\b(?<attrs>[^>]*)>', 'IgnoreCase,Singleline')
    $attributes = @('action','method','name','id','type','value','href','target','onclick','onchange','onsubmit','data-id','data-loadingurl','data-save','data-href','data-url')
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
function Get-Scripts([string]$Text) {
    $normalized = $Text.Replace(
        'https://code.jquery.com/jquery-3.6.0.min.js',
        '__R07_APPROVED_LOCAL_JQUERY__'
    ).Replace(
        'source/plugin/tb_cus_base/static/js/jquery-3.3.1.min.js',
        '__R07_APPROVED_LOCAL_JQUERY__'
    )
    return @([regex]::Matches($normalized, '(?is)<script\b[^>]*>.*?</script>') | ForEach-Object { $_.Value.Trim() -replace "`r`n", "`n" })
}

$prefix = $OverlayRoot.TrimEnd([IO.Path]::DirectorySeparatorChar) + [IO.Path]::DirectorySeparatorChar
$files = @(Get-ChildItem -LiteralPath $OverlayRoot -Recurse -File)
$actual = @($files | ForEach-Object { $_.FullName.Substring($prefix.Length).Replace('\','/') } | Sort-Object)
Assert-True (($actual -join "`n") -ceq (($expected | Sort-Object) -join "`n")) 'exact seven-page allowlist changed'

$templates = @($files | Where-Object Extension -in @('.php','.htm','.html'))
foreach ($file in $templates) {
    $relative = $file.FullName.Substring($prefix.Length)
    $before = Read-Text (Join-Path $BaselineRoot $relative)
    $after = Read-Text $file.FullName
    Assert-True ($before -cne $after) "unchanged template: $relative"
    Assert-True (((Get-Protocol $before) -join "`n") -ceq ((Get-Protocol $after) -join "`n")) "form/link protocol changed: $relative"
    Assert-True (((Get-Flow $before) -join "`n") -ceq ((Get-Flow $after) -join "`n")) "template flow changed: $relative"
    Assert-True (((Get-Scripts $before) -join "`n---SCRIPT---`n") -ceq ((Get-Scripts $after) -join "`n---SCRIPT---`n")) "business script changed: $relative"
}

$combined = ($files | ForEach-Object { Read-Text $_.FullName }) -join "`n"
foreach ($forbidden in @('cdn.tailwindcss.com','cdn.jsdelivr.net','cdnjs.cloudflare.com','unpkg.com','fonts.googleapis.com','use.fontawesome.com')) {
    Assert-True (-not $combined.Contains($forbidden)) "public UI CDN remains: $forbidden"
}
foreach ($cssFile in @($files | Where-Object Extension -eq '.css')) {
    $css = Read-Text $cssFile.FullName
    Assert-True (-not [regex]::IsMatch($css, '(?i)https?://|@import|:has\s*\(')) "non-local or unsupported CSS: $($cssFile.Name)"
}

Write-Host '[R07-OVERLAY] PASS'
Write-Host '[R07-OVERLAY] files=12 pages=7 scope=CLICK_PROVEN_ONLY protocol=UNCHANGED flow=UNCHANGED scripts=UNCHANGED'
