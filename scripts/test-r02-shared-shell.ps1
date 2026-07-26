[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$overlay = Join-Path $root 'r02-site-overlay'
$static = Join-Path $overlay 'source\plugin\xigua_hb\static\tgb-r02'
$header = Join-Path $overlay 'source\plugin\xigua_hb\template\touch\common_header.php'
$nav = Join-Path $overlay 'source\plugin\xigua_hb\template\touch\common_nav.php'
$index = Join-Path $overlay 'index.php'
$css = Join-Path $static 'light-grid-r02.css'
$logo = Join-Path $static 'brand-mark-r02.svg'
$chat = Join-Path $static 'chat-r02.svg'
$screenshot = Join-Path $root 'evidence\R02\after\DESKTOP-SPLASH-1265x712.jpg'
$responsivePreview = Join-Path $root 'tests\r02\light-grid-preview.html'
$responsiveFrame = Join-Path $root 'tests\r02\light-grid-preview-frame.html'
$responsiveScreenshots = @(
    @{
        Path = Join-Path $root 'evidence\R02\after\R02-LIGHT-GRID-360x800.jpg'
        Width = 360
        Height = 800
        Sha256 = '700736c843a98bed1a9f289ad72368f58f01f7f62578ce2d2374871f45dbbbfb'
    },
    @{
        Path = Join-Path $root 'evidence\R02\after\R02-LIGHT-GRID-390x844.jpg'
        Width = 390
        Height = 844
        Sha256 = '21a0613c060c1ccf75a8fc6f83a2c94284f21e28cd448f8aad82283d2fc0205b'
    },
    @{
        Path = Join-Path $root 'evidence\R02\after\R02-LIGHT-GRID-430x932.jpg'
        Width = 430
        Height = 932
        Sha256 = '9d2362ba7b2027ff465f1300b3b3ce83b598155eef757354a7115c24d7534272'
    }
)

function Assert-True {
    param([bool]$Condition, [string]$Message)
    if (-not $Condition) {
        throw "[R02-STATIC] FAIL: $Message"
    }
}

$expectedFiles = @($index, $header, $nav, $css, $logo, $chat, $responsivePreview, $responsiveFrame)
foreach ($file in $expectedFiles) {
    Assert-True (Test-Path -LiteralPath $file -PathType Leaf) "missing $file"
}

$actualOverlayFiles = @(Get-ChildItem -LiteralPath $overlay -Recurse -File)
Assert-True ($actualOverlayFiles.Count -eq 6) 'overlay must contain exactly six allowlisted files'

$indexText = Get-Content -Raw -LiteralPath $index -Encoding UTF8
$headerText = Get-Content -Raw -LiteralPath $header -Encoding UTF8
$navText = Get-Content -Raw -LiteralPath $nav -Encoding UTF8
$cssText = Get-Content -Raw -LiteralPath $css -Encoding UTF8
$logoText = Get-Content -Raw -LiteralPath $logo -Encoding UTF8
$chatText = Get-Content -Raw -LiteralPath $chat -Encoding UTF8
$newText = @($indexText, $headerText, $navText, $cssText, $logoText, $chatText) -join "`n"

Assert-True ($indexText.Contains('请使用手机打开推广宝')) 'desktop guidance marker is absent'
Assert-True ($indexText.ToLowerInvariant().Contains('tuiguangbaoandroid')) 'Android user-agent path is absent'
Assert-True ($indexText.Contains('/plugin.php?id=xigua_hb')) 'mobile business route is absent'
Assert-True ($headerText.Contains('class="tgb-light-grid"')) 'shared opt-in class is absent'
Assert-True ($headerText.Contains('light-grid-r02.css?v=20260726-r02b')) 'versioned CSS link is absent'
Assert-True ($headerText.Contains("str_replace('签米', '推广宝'")) 'UI title brand replacement is absent'
Assert-True ($navText.Contains('tgb-channel-row')) 'shared channel row class is absent'
Assert-True ($navText.Contains('tgb-r02/chat-r02.svg?v=20260726-r02c')) 'local chat SVG is absent'
Assert-True ($navText.Contains('name="keyword"')) 'search field name changed or disappeared'
Assert-True ($navText.Contains('name="id" value="xigua_hb"')) 'search plugin route changed or disappeared'
Assert-True ($navText.Contains('name="ac" value="cat"')) 'search action route changed or disappeared'

foreach ($token in @(
    '--tgb-bg: #f4f7fb',
    '--tgb-surface: #ffffff',
    '--tgb-primary: #2764ff',
    '--tgb-mint: #19b8a9',
    '--tgb-radius-card: 16px'
)) {
    Assert-True ($cssText.Contains($token)) "missing visual token $token"
}

Assert-True ($cssText.Contains('@media (prefers-reduced-motion: reduce)')) 'reduced-motion gate is absent'
Assert-True ($cssText.Contains('env(safe-area-inset-bottom')) 'bottom safe-area gate is absent'
Assert-True ($cssText.Contains('@media (display-mode: browser)')) 'browser safe-area scope is absent'
Assert-True ($logoText.TrimStart().StartsWith('<svg')) 'brand mark is not local SVG'
Assert-True ($chatText.TrimStart().StartsWith('<svg')) 'chat icon is not local SVG'

$forbiddenRuntime = 'cdn\.tailwindcss|cdn\.jsdelivr|cdnjs\.cloudflare|unpkg\.com|fonts\.googleapis'
Assert-True (-not [regex]::IsMatch($newText, $forbiddenRuntime, 'IgnoreCase')) 'public UI CDN dependency detected'
$forbiddenWarmPrimary = '#ff5321|#ff3632|#fe412b|#f96142|#ff5722'
Assert-True (-not [regex]::IsMatch($cssText, $forbiddenWarmPrimary, 'IgnoreCase')) 'legacy warm primary detected in R02 CSS'

Assert-True ((Get-Item -LiteralPath $css).Length -lt 32768) 'R02 CSS exceeds 32 KiB'
Assert-True ((Get-Item -LiteralPath $logo).Length -lt 8192) 'brand SVG exceeds 8 KiB'
Assert-True ((Get-Item -LiteralPath $chat).Length -lt 4096) 'chat SVG exceeds 4 KiB'

Assert-True (Test-Path -LiteralPath $screenshot -PathType Leaf) 'desktop screenshot is absent'
Add-Type -AssemblyName System.Drawing
$image = [System.Drawing.Image]::FromFile($screenshot)
try {
    $width = $image.Width
    $height = $image.Height
} finally {
    $image.Dispose()
}
Assert-True ($width -eq 1265 -and $height -eq 712) "unexpected screenshot dimensions ${width}x${height}"

$previewText = Get-Content -Raw -LiteralPath $responsivePreview -Encoding UTF8
$frameText = Get-Content -Raw -LiteralPath $responsiveFrame -Encoding UTF8
Assert-True ($previewText.Contains('preview-embedded')) 'responsive preview iframe mode is absent'
Assert-True ($frameText.Contains('"360x800":1,"390x844":1,"430x932":1')) 'responsive frame matrix changed'
Assert-True ($frameText.Contains('overflow=')) 'responsive overflow evidence marker is absent'

foreach ($responsiveScreenshot in $responsiveScreenshots) {
    Assert-True (Test-Path -LiteralPath $responsiveScreenshot.Path -PathType Leaf) "responsive screenshot is absent: $($responsiveScreenshot.Path)"
    $responsiveImage = [System.Drawing.Image]::FromFile($responsiveScreenshot.Path)
    try {
        $responsiveWidth = $responsiveImage.Width
        $responsiveHeight = $responsiveImage.Height
    } finally {
        $responsiveImage.Dispose()
    }
    Assert-True (
        $responsiveWidth -eq $responsiveScreenshot.Width -and
        $responsiveHeight -eq $responsiveScreenshot.Height
    ) "unexpected responsive screenshot dimensions ${responsiveWidth}x${responsiveHeight}"
    $responsiveHash = (Get-FileHash -Algorithm SHA256 -LiteralPath $responsiveScreenshot.Path).Hash.ToLowerInvariant()
    Assert-True ($responsiveHash -eq $responsiveScreenshot.Sha256) "responsive screenshot hash changed: $($responsiveScreenshot.Path)"
}

Write-Output '[R02-STATIC] PASS'
Write-Output "[R02-STATIC] overlay_files=$($actualOverlayFiles.Count)"
Write-Output "[R02-STATIC] css_bytes=$((Get-Item -LiteralPath $css).Length)"
Write-Output "[R02-STATIC] screenshot=${width}x${height}"
Write-Output '[R02-STATIC] responsive=360x800,390x844,430x932'
