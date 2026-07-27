param(
    [string]$OverlayRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) 'r09-brand-overlay')
)

$ErrorActionPreference = 'Stop'
$repo = [IO.Path]::GetFullPath((Split-Path -Parent $PSScriptRoot))
$OverlayRoot = [IO.Path]::GetFullPath($OverlayRoot)
$expected = @(
    'done/app.html',
    'm/gywm.html',
    'm/help.html',
    'source/plugin/view/module/site/sign.php',
    'source/plugin/xigua_hb/template/touch/pub.php',
    'source/plugin/xigua_hb/template/touch/vip.php',
    'source/plugin/xigua_hh/template/touch/myfans.php',
    'template/comiis_app/touch/common/showmessage.php'
)
$baseline = @{
    'done/app.html' = 'r08-site-overlay/done/app.html'
    'm/gywm.html' = 'r03-site-overlay/m/gywm.html'
    'm/help.html' = 'r03-site-overlay/m/help.html'
    'source/plugin/view/module/site/sign.php' = 'r08-site-overlay/source/plugin/view/module/site/sign.php'
    'source/plugin/xigua_hb/template/touch/pub.php' = 'r05-site-overlay-v5/source/plugin/xigua_hb/template/touch/pub.php'
    'source/plugin/xigua_hb/template/touch/vip.php' = 'r07-site-overlay/source/plugin/xigua_hb/template/touch/vip.php'
    'source/plugin/xigua_hh/template/touch/myfans.php' = 'r08-site-overlay/source/plugin/xigua_hh/template/touch/myfans.php'
    'template/comiis_app/touch/common/showmessage.php' = 'r06-site-overlay/template/comiis_app/touch/common/showmessage.php'
}

function Fail([string]$message) { throw "[R09-BRAND] FAIL: $message" }
function Read-Text([string]$path) { return ([IO.File]::ReadAllText($path) -replace "`r`n", "`n") }

$prefix = $OverlayRoot.TrimEnd([IO.Path]::DirectorySeparatorChar) + [IO.Path]::DirectorySeparatorChar
$actual = @(Get-ChildItem $OverlayRoot -Recurse -File | ForEach-Object { $_.FullName.Substring($prefix.Length).Replace('\','/') } | Sort-Object)
if (($actual -join "`n") -cne (($expected | Sort-Object) -join "`n")) { Fail 'exact eight-file allowlist changed' }

foreach ($relative in $expected) {
    $after = Read-Text (Join-Path $OverlayRoot $relative)
    $before = Read-Text (Join-Path $repo $baseline[$relative])
    switch ($relative) {
        'm/help.html' {
            $after = $after.Replace('推广宝', '创脉引擎').Replace('<title>创脉引擎 - 帮助中心</title>', '<title>推广宝 - 帮助中心</title>')
        }
        'm/gywm.html' { $after = $after.Replace('copyright 2024-2025 推广宝 版权所有', 'copyright 2024-2025 创脉引擎 版权所有') }
        'source/plugin/xigua_hb/template/touch/pub.php' {
            foreach ($required in @(
                '/* TGB-R09-PUBLISH-VISUAL-FIX:START */',
                '.tgb-light-grid header.x_header {',
                'height:60px!important;',
                'width:64px!important;',
                'height:44px!important;',
                '.tgb-publish-header-spacer, .tgb-publish-form-spacer, header.x_header .navtitle { display:none!important; }',
                '/* TGB-R09-PUBLISH-VISUAL-FIX:END */'
            )) {
                if (-not $after.Contains($required)) { Fail "publish visual contract missing: $required" }
            }
            $after = $after.Replace('推广宝会员，解锁', '签米会员，解锁')
            $after = $after.Replace(' class="tgb-publish-header-spacer"', '')
            $after = $after.Replace(' class="tgb-publish-header-title"', '')
            $after = $after.Replace(' class="tgb-publish-submit-label"', '')
            $after = $after.Replace(' class="tgb-publish-form-spacer"', '')
            $after = [regex]::Replace($after, '(?s)/\* TGB-R09-PUBLISH-VISUAL-FIX:START \*/.*?/\* TGB-R09-PUBLISH-VISUAL-FIX:END \*/\n', '')
        }
        'template/comiis_app/touch/common/showmessage.php' { $after = $after.Replace('<div class="nav-title">推广宝</div>', '<div class="nav-title">签米</div>') }
        'source/plugin/xigua_hb/template/touch/vip.php' { $after = $after.Replace('推广宝VIP会员中心已就绪', '签米VIP会员中心已就绪') }
        'done/app.html' { $after = $after.Replace('推广宝下载页已就绪', '签米下载页已就绪') }
        'source/plugin/xigua_hh/template/touch/myfans.php' { $after = $after.Replace('推广宝会员才能扶持', '签米会员才能扶持') }
        'source/plugin/view/module/site/sign.php' {
            $after = $after.Replace('推广宝刚刚上线', '签米刚刚上线')
            $after = $after.Replace('推广宝会员签到', '签米会员签到')
            $after = $after.Replace('推广宝会员特权：', '签米会员特权：')
            $after = $after.Replace('若好友是推广宝会员', '若好友是签米会员')
            $after = $after.Replace('若好友不是推广宝会员', '若好友不是签米会员')
            $after = $after.Replace('开通推广宝会员后', '开通签米会员后')
        }
    }
    if ($after -cne $before) { Fail "change exceeds approved brand substitutions: $relative" }
}

Write-Host '[R09-BRAND] PASS'
Write-Host '[R09-BRAND] files=8 changes=VISIBLE_BRAND_AND_APPROVED_PUBLISH_VISUAL_ONLY business_protocol=UNCHANGED'
