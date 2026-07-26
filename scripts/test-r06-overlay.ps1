param(
    [string]$OverlayRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'r06-site-overlay'),
    [string]$BaselineRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'r06-baseline-selected')
)

$ErrorActionPreference = 'Stop'
$OverlayRoot = [System.IO.Path]::GetFullPath($OverlayRoot)
$BaselineRoot = [System.IO.Path]::GetFullPath($BaselineRoot)
$expected = @(
    'source/plugin/deluser/static/tgb-r06-cancel-light-grid.css',
    'source/plugin/deluser/template/touch/main.htm',
    'source/plugin/xiaomy_certification/static/tgb-r06-certification-light-grid.css',
    'source/plugin/xiaomy_certification/template/touch/webstressapipay.htm',
    'source/plugin/xigua_hb/static/tgb-r06/account-light-grid-r06.css',
    'source/plugin/xigua_hb/template/touch/my_new.php',
    'source/plugin/xigua_hb/template/touch/myaddr.php',
    'source/plugin/xigua_hb/template/touch/shezhi.php',
    'source/plugin/xigua_lt/static/tgb-r06/chats-list-light-grid-r06.css',
    'source/plugin/xigua_lt/template/touch/chats.php',
    'source/plugin/xigua_member/images/tgb-r06-profile-light-grid.css',
    'source/plugin/xigua_member/profile.inc.php',
    'template/comiis_app/touch/common/showmessage.php'
)

function Fail([string]$Message) { throw "[R06-OVERLAY] FAIL: $Message" }
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
    return @([regex]::Matches($Text, '(?is)<script\b[^>]*>.*?</script>') |
        Where-Object { $_.Value -notmatch '(?i)cdn\.tailwindcss\.com|tailwind\.config|document\.title\s*=' } |
        ForEach-Object { $_.Value.Trim() -replace "`r`n", "`n" })
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
foreach ($forbidden in @('myset.php', 'chat.php', 'mycomment.php', 'mycover.php', 'hong_list.php', 'member_fav_li.php')) {
    Assert-True (-not $combined.Contains($forbidden)) "out-of-scope marker remains: $forbidden"
}

$cssFiles = @($files | Where-Object Extension -eq '.css')
foreach ($cssFile in $cssFiles) {
    $css = Read-Normalized $cssFile.FullName
    Assert-True (-not [regex]::IsMatch($css, '(?i)https?://|@import')) "R06 CSS must remain local: $($cssFile.Name)"
    Assert-True (-not [regex]::IsMatch($css, '(?i)#ff7b00|#ff8c00|#f97316|#ea580c|#d35400|#fff5e6|#fef3e2|#fdf0db')) "legacy warm palette remains: $($cssFile.Name)"
    Assert-True (-not [regex]::IsMatch($css, '(?i)border-radius:\s*(?:1[0-9]|[2-9][0-9])px')) "oversized functional rounding remains: $($cssFile.Name)"
}

$scopeChecks = [ordered]@{
    'source/plugin/xigua_hb/template/touch/my_new.php' = 'tgb-r06-account-page'
    'source/plugin/xigua_hb/template/touch/shezhi.php' = 'tgb-r06-settings-page'
    'source/plugin/xigua_hb/template/touch/myaddr.php' = 'tgb-r06-address-page'
    'source/plugin/xigua_member/profile.inc.php' = 'tgb-r06-profile-light-grid.css'
    'template/comiis_app/touch/common/showmessage.php' = 'body.pg_tb_credit .message-card'
    'source/plugin/xiaomy_certification/template/touch/webstressapipay.htm' = 'tgb-r06-certification-light-grid.css'
    'source/plugin/deluser/template/touch/main.htm' = 'tgb-r06-cancel-light-grid.css'
    'source/plugin/xigua_lt/template/touch/chats.php' = 'tgb-r06-chat-list'
}
foreach ($item in $scopeChecks.GetEnumerator()) {
    $template = Read-Normalized (Join-Path $OverlayRoot $item.Key)
    Assert-True ($template.Contains($item.Value)) "page scope is absent: $($item.Key) / $($item.Value)"
}

Write-Host '[R06-OVERLAY] PASS'
Write-Host '[R06-OVERLAY] files=13 pages=8 scope=CLICK_PROVEN_ONLY protocol=UNCHANGED flow=UNCHANGED urls=UNCHANGED scripts=UNCHANGED'
Write-Host '[R06-OVERLAY] account=SCOPED forms=SCOPED chat_list=SCOPED chat_detail=EXCLUDED public_ui_cdn=NO_INCREASE'
