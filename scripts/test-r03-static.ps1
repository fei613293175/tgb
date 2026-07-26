param(
    [string]$BaselineRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) '..\_r03_baseline')
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$overlay = Join-Path $root 'r03-site-overlay'
$BaselineRoot = [System.IO.Path]::GetFullPath($BaselineRoot)

function Fail([string]$Message) {
    throw "[R03-STATIC] FAIL: $Message"
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

function Replace-Once([string]$Text, [string]$Current, [string]$Original, [string]$Label) {
    $matches = ([regex]::Matches($Text, [regex]::Escape($Current))).Count
    Assert-True ($matches -eq 1) "$Label must occur exactly once, found $matches"
    return $Text.Replace($Current, $Original)
}

function Get-ProtocolSignature([string]$Text) {
    $tags = [regex]::Matches(
        $Text,
        '<(?<tag>form|input|button|select|textarea)\b(?<attrs>[^>]*)>',
        [System.Text.RegularExpressions.RegexOptions]::IgnoreCase -bor
            [System.Text.RegularExpressions.RegexOptions]::Singleline
    )
    $protocolAttributes = @(
        'action', 'method', 'name', 'id', 'type', 'value', 'onclick',
        'onchange', 'onsubmit', 'autocomplete'
    )
    $signature = foreach ($match in $tags) {
        $attrs = $match.Groups['attrs'].Value
        $parts = [System.Collections.Generic.List[string]]::new()
        $parts.Add($match.Groups['tag'].Value.ToLowerInvariant())
        foreach ($attribute in $protocolAttributes) {
            $attributeMatch = [regex]::Match(
                $attrs,
                "(?i)(?:^|\s)$([regex]::Escape($attribute))\s*=\s*(?:`"(?<dq>[^`"]*)`"|'(?<sq>[^']*)'|(?<bare>[^\s>]+))"
            )
            if ($attributeMatch.Success) {
                $value = $attributeMatch.Groups['dq'].Value
                if (-not $attributeMatch.Groups['dq'].Success) { $value = $attributeMatch.Groups['sq'].Value }
                if (-not $attributeMatch.Groups['dq'].Success -and -not $attributeMatch.Groups['sq'].Success) {
                    $value = $attributeMatch.Groups['bare'].Value
                }
                $parts.Add("$attribute=$value")
            }
        }
        foreach ($booleanAttribute in @('checked', 'required', 'disabled', 'multiple')) {
            if ([regex]::IsMatch($attrs, "(?i)(?:^|\s)$booleanAttribute(?:\s|=|$)")) {
                $parts.Add($booleanAttribute)
            }
        }
        $parts -join '|'
    }
    return @($signature)
}

Assert-True (Test-Path -LiteralPath $overlay -PathType Container) 'R03 overlay is absent'
Assert-True (Test-Path -LiteralPath $BaselineRoot -PathType Container) 'R03 baseline directory is absent'

$authFiles = @(
    'template\default\touch\member\login.htm',
    'template\default\touch\member\register.htm',
    'source\plugin\tb_cus_mobilereg\template\touch\loginphone.htm'
)
$legalFiles = @('xy.html', 'yszc.html', 'yhxy.html', 'xfxy.html', 'fpsm.html', 'hyxy.html', 'help.html', 'gywm.html')
$expectedFiles = @($authFiles) + @(
    'source\plugin\xigua_hb\static\tgb-r03\auth-r03.css',
    'source\plugin\xigua_hb\static\tgb-r03\auth-light-grid-r03.css',
    'm\template\css\tgb-r03-legal.css'
) + @($legalFiles | ForEach-Object { "m\$_" })
$actualFiles = @(Get-ChildItem -LiteralPath $overlay -Recurse -File | ForEach-Object {
    $_.FullName.Substring($overlay.Length + 1)
})
Assert-True ($actualFiles.Count -eq $expectedFiles.Count) "overlay file count is $($actualFiles.Count), expected $($expectedFiles.Count)"
Assert-True (($actualFiles | Sort-Object) -join "`n" -ceq (($expectedFiles | Sort-Object) -join "`n")) 'overlay allowlist mismatch'

$allAuth = ''
foreach ($relativePath in $authFiles) {
    $baselineText = Read-Normalized (Join-Path $BaselineRoot $relativePath)
    $overlayText = Read-Normalized (Join-Path $overlay $relativePath)
    $baselineProtocol = @(Get-ProtocolSignature $baselineText)
    $overlayProtocol = @(Get-ProtocolSignature $overlayText)
    Assert-True (($baselineProtocol -join "`n") -ceq ($overlayProtocol -join "`n")) "form/control protocol changed: $relativePath"
    Assert-True ($overlayText.Contains('/source/plugin/xigua_hb/static/tgb-r03/auth-r03.css?v=20260726-r03')) "compiled local CSS missing: $relativePath"
    Assert-True ($overlayText.Contains('/source/plugin/xigua_hb/static/tgb-r03/auth-light-grid-r03.css?v=20260726-r03')) "semantic local CSS missing: $relativePath"
    Assert-True ($overlayText.Contains('/source/plugin/tb_cus_base/static/js/jquery-3.3.1.min.js')) "early local jQuery missing: $relativePath"
    Assert-True (-not [regex]::IsMatch($overlayText, '(?i)cdn\.tailwindcss\.com|cdn\.jsdelivr\.net|cdnjs\.cloudflare\.com|unpkg\.com')) "public UI CDN remains: $relativePath"
    Assert-True (-not [regex]::IsMatch($overlayText, '(?m)(?<![A-Za-z0-9_])\$\s*\(')) "direct dollar alias call remains: $relativePath"
    Assert-True ($overlayText.Contains('推广宝')) "推广宝 brand missing: $relativePath"
    $allAuth += $overlayText
}
Assert-True ($allAuth.Contains('member.php?mod=logging&action=login&loginsubmit=yes&loginhash=$loginhash&mobile=2')) 'password-login action missing'
Assert-True ($allAuth.Contains('member.php?mod={$_G[setting][regname]}')) 'registration action missing'
Assert-True ($allAuth.Contains('plugin.php?id=tb_cus_mobilereg:mobilelogin')) 'SMS-login action missing'
Assert-True ($allAuth.Contains('plugin.php?id=tb_cus_mobilereg:mobilereg&sendphone=')) 'SMS endpoint missing'

$authCss = Read-Normalized (Join-Path $overlay 'source\plugin\xigua_hb\static\tgb-r03\auth-light-grid-r03.css')
$compiledCssPath = Join-Path $overlay 'source\plugin\xigua_hb\static\tgb-r03\auth-r03.css'
Assert-True ((Get-Item -LiteralPath $compiledCssPath).Length -lt 32768) 'compiled auth CSS exceeds 32 KiB'
foreach ($required in @('#f4f7fb', '#2764ff', 'safe-area-inset-top', 'font-size: 16px', 'min-height: 48px', 'prefers-reduced-motion')) {
    Assert-True ($authCss.Contains($required)) "auth CSS requirement missing: $required"
}
foreach ($forbidden in @('#ff6b35', '#f5f0eb', 'blur-3xl')) {
    Assert-True (-not $authCss.Contains($forbidden)) "legacy auth visual remains: $forbidden"
}

$legalCssLink = '        <link rel="stylesheet" href="./template/css/tgb-r03-legal.css?v=20260726-r03">' + "`n"
foreach ($fileName in @('xy.html', 'yszc.html', 'yhxy.html', 'xfxy.html', 'fpsm.html', 'hyxy.html')) {
    $baselineText = Read-Normalized (Join-Path $BaselineRoot "m\$fileName")
    $overlayText = Read-Normalized (Join-Path $overlay "m\$fileName")
    $restored = Replace-Once $overlayText $legalCssLink '' "$fileName local CSS link"
    Assert-True ($restored -ceq $baselineText) "frozen legal document changed: $fileName"
}

$helpBaseline = Read-Normalized (Join-Path $BaselineRoot 'm\help.html')
$helpOverlay = Read-Normalized (Join-Path $overlay 'm\help.html')
$helpRestored = Replace-Once $helpOverlay $legalCssLink '' 'help local CSS link'
$helpRestored = Replace-Once $helpRestored '<title>推广宝 - 帮助中心</title>' '<title>创脉引擎帮助中心</title>' 'help UI title'
Assert-True ($helpRestored -ceq $helpBaseline) 'frozen help body changed'

$aboutBaseline = Read-Normalized (Join-Path $BaselineRoot 'm\gywm.html')
$aboutOverlay = Read-Normalized (Join-Path $overlay 'm\gywm.html')
$aboutRestored = Replace-Once $aboutOverlay $legalCssLink '' 'about local CSS link'
$aboutRestored = Replace-Once $aboutRestored '<title>推广宝 - 关于我们</title>' '<title>关于我们</title>' 'about UI title'
$aboutRestored = Replace-Once $aboutRestored '<img style="border-radius:12px;width:80px;height:80px;" src="/source/plugin/xigua_hb/static/tgb-r02/brand-mark-r02.svg" alt="">' '<img style="border-radius:50%;width:80px;height:80px;" src="https://img.imehui.com/20250203/173857437767a08a29ba117.png">' 'about product logo'
$aboutRestored = Replace-Once $aboutRestored "                        推广宝`n" "                        关于我们`n" 'about product heading'
$aboutRestored = Replace-Once $aboutRestored '                        关于我们 · Version 0.0.8' '                        Version 0.0.8' 'about product description'
Assert-True ($aboutRestored -ceq $aboutBaseline) 'about page changed outside approved UI brand positions'
Assert-True ($aboutOverlay.Contains('copyright 2024-2025 创脉引擎 版权所有')) 'frozen about copyright is missing'

$legalCss = Read-Normalized (Join-Path $overlay 'm\template\css\tgb-r03-legal.css')
foreach ($required in @('#f4f7fb', '#ffffff', 'font-size: 16px', 'line-height: 1.75', 'safe-area-inset-top', 'overflow-x: hidden')) {
    Assert-True ($legalCss.Contains($required)) "legal CSS requirement missing: $required"
}
Assert-True (-not $legalCss.Contains('#ff6b35')) 'legacy warm primary remains in legal CSS'
Assert-True ((Get-Item -LiteralPath (Join-Path $overlay 'm\template\css\tgb-r03-legal.css')).Length -lt 16384) 'legal CSS exceeds 16 KiB'

Write-Output '[R03-STATIC] PASS'
Write-Output "[R03-STATIC] overlay_files=$($actualFiles.Count) auth_pages=$($authFiles.Count) legal_pages=$($legalFiles.Count)"
Write-Output "[R03-STATIC] auth_css_bytes=$((Get-Item -LiteralPath $compiledCssPath).Length) legal_css_bytes=$((Get-Item -LiteralPath (Join-Path $overlay 'm\template\css\tgb-r03-legal.css')).Length)"
Write-Output '[R03-STATIC] protocol=UNCHANGED legal_copy=FROZEN local_ui_assets=PASS'
