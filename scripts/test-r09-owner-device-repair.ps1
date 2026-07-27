$ErrorActionPreference = 'Stop'
$repoRoot = [IO.Path]::GetFullPath((Split-Path -Parent $PSScriptRoot))
$overlay = Join-Path $repoRoot 'r09-owner-fix-overlay'
$errors = [Collections.Generic.List[string]]::new()

function Read-RepoFile([string]$relative) {
    $path = Join-Path $repoRoot $relative
    if (-not (Test-Path -LiteralPath $path -PathType Leaf)) {
        $errors.Add("missing file: $relative")
        return ''
    }
    return [IO.File]::ReadAllText($path)
}

function Require-Text([string]$text, [string]$needle, [string]$message) {
    if (-not $text.Contains($needle)) { $errors.Add($message) }
}

function Forbid-Text([string]$text, [string]$needle, [string]$message) {
    if ($text.Contains($needle)) { $errors.Add($message) }
}

$build = Read-RepoFile 'scripts/build-r09-candidate.ps1'
Require-Text $build "'r09-owner-fix-overlay'" 'owner repair layer is missing from candidate build'
if ($build.IndexOf("'r09-owner-fix-overlay'") -lt $build.IndexOf("'r09-brand-overlay'")) {
    $errors.Add('owner repair layer must be the final candidate layer')
}

$homeTemplate = Read-RepoFile 'r09-owner-fix-overlay/source/plugin/xigua_hb/template/touch/index.php'
$homeCss = Read-RepoFile 'r09-owner-fix-overlay/source/plugin/xigua_hb/static/tgb-r04/discovery-light-grid-r04.css'
Require-Text $homeTemplate 'class="post-card tgb-headline-card"' 'home headline card class is missing'
Forbid-Text $homeTemplate 'class="post-card tgb-headline-card" style=' 'headline card still has legacy inline container styling'
Require-Text $homeTemplate '20260727-r09-owner-v5' 'home cache version was not advanced'
Require-Text $homeTemplate 'class="feed-list tgb-headline-feed"' 'headline list does not use the parity container'
$headlineStart = $homeTemplate.IndexOf('class="post-card tgb-headline-card"')
$headlineEnd = $homeTemplate.IndexOf('<div style="margin-bottom:15px;">', $headlineStart)
if ($headlineStart -lt 0 -or $headlineEnd -le $headlineStart) {
    $errors.Add('headline card block could not be isolated')
} else {
    $headlineBlock = $homeTemplate.Substring($headlineStart, $headlineEnd - $headlineStart)
    Forbid-Text $headlineBlock 'linear-gradient(135deg, #ffb47b, #ff8a5c)' 'headline card still contains the legacy orange avatar gradient'
    Forbid-Text $headlineBlock 'rgba(255, 220, 180, 0.7)' 'headline card still contains the legacy orange category background'
    Forbid-Text $headlineBlock 'rgba(255, 200, 120, 0.25)' 'headline card still contains the legacy orange divider'
}
Require-Text $homeCss '.feed-list .tgb-headline-card' 'headline parity CSS is missing'
Require-Text $homeCss '.feed-list.tgb-headline-feed' 'headline horizontal parity rule is missing'
Require-Text $homeCss 'border-radius: 8px !important' 'headline card does not use the standard 8px radius'
Require-Text $homeCss '.tgb-headline-badge' 'headline visual distinction label is missing'

$promotionCss = Read-RepoFile 'r09-owner-fix-overlay/source/plugin/tb_toutiao/static/tgb-r07/promotion-light-grid-r07.css'
Require-Text $promotionCss 'overflow-y: scroll !important' 'headline project list does not force vertical scrolling'
Require-Text $promotionCss 'touch-action: pan-y !important' 'headline project list touch scrolling is missing'
Require-Text $promotionCss 'R09 owner-device repair: shared bottom navigation parity' 'promotion bottom navigation parity is missing'

foreach ($relative in @(
    'r09-owner-fix-overlay/source/plugin/xigua_hb/static/tgb-r06/account-light-grid-r06.css',
    'r09-owner-fix-overlay/source/plugin/view/static/tgb-r08/sign-light-grid-r08.css',
    'r09-owner-fix-overlay/source/plugin/tb_cus_pipei/static/tgb-r08/dividend-light-grid-r08.css'
)) {
    $css = Read-RepoFile $relative
    Require-Text $css '.qmn-nav' "bottom navigation CSS is missing: $relative"
    Require-Text $css '-webkit-text-fill-color: currentColor !important' "active bottom navigation icon can retain old gradient: $relative"
}

$accountTemplate = Read-RepoFile 'r09-owner-fix-overlay/source/plugin/xigua_hb/template/touch/my_new.php'
$accountCss = Read-RepoFile 'r09-owner-fix-overlay/source/plugin/xigua_hb/static/tgb-r06/account-light-grid-r06.css'
Require-Text $accountTemplate 'account-light-grid-r06.css?20260727-r09-owner-v3' 'account cache version was not advanced'
Require-Text $accountCss 'width: auto !important' 'account header width reset is missing'
Require-Text $accountCss 'grid-column: 2' 'account title grid placement is missing'
Require-Text $accountCss 'grid-column: 3' 'account action grid placement is missing'
Require-Text $accountTemplate 'jquery-2.1.4.js?51{VERHASH}' 'account advertisement script is missing its local jQuery dependency'

$sharedShell = Read-RepoFile 'r02-site-overlay/source/plugin/xigua_hb/static/tgb-r02/light-grid-r02.css'
$sharedNav = Read-RepoFile 'r02-site-overlay/source/plugin/xigua_hb/template/touch/common_nav.php'
$ownerHeader = Read-RepoFile 'r09-owner-fix-overlay/source/plugin/xigua_hb/template/touch/common_header.php'
$myPublications = Read-RepoFile 'r09-owner-fix-overlay/source/plugin/xigua_hb/template/touch/mypub.php'
Require-Text $sharedShell '--tgb-header-height: calc(60px + env(safe-area-inset-top, 0px))' 'shared x_header height is not compact safe-area aware'
Forbid-Text $sharedShell '--tgb-header-height: 92px' 'legacy 92px shared header returned'
Forbid-Text $sharedNav '<div style="height:30px;"></div>' 'legacy 30px shared header spacer returned'
Require-Text $ownerHeader 'light-grid-r02.css?v=20260727-r09-owner-v1' 'shared shell cache version was not advanced'
Require-Text $myPublications '<div class="page__bd" style="margin-top:0;">' 'my publications retains legacy top offset'
Require-Text $myPublications 'padding:0 0 calc(76px + env(safe-area-inset-bottom,0px))' 'my publications retains legacy top padding'
Require-Text $myPublications '.page__bd > .x_header_fix { display:none!important; height:0!important; }' 'my publications retains the shared fixed-header spacer'
$reviewTemplate = Read-RepoFile 'r09-owner-fix-overlay/source/plugin/xigua_hb/template/touch/manage.php'
Require-Text $reviewTemplate 'padding:0 0 calc(76px + env(safe-area-inset-bottom,0px))' 'review page retains legacy top padding'
Require-Text $reviewTemplate '.page__bd > .x_header_fix { display:none!important; height:0!important; }' 'review page retains the shared fixed-header spacer'

foreach ($tab in @('tab1.php','tab2.php','tab3.php','tab4.php','tab5.php','tab6.php','tab9.php','tab10.php','tab11.php')) {
    $tabText = Read-RepoFile "r02-site-overlay/source/plugin/xigua_hb/template/touch/$tab"
    Forbid-Text $tabText 'const currentPath = window.location.pathname' "broken optional route matcher remains in $tab"
    Require-Text $tabText 'items.forEach(item =>' "bottom navigation click behavior is missing in $tab"
}

foreach ($relative in @(
    'r09-owner-fix-overlay/source/plugin/xigua_hb/static/tgb-r06/account-light-grid-r06.css',
    'r09-owner-fix-overlay/source/plugin/xigua_hb/static/tgb-r07/finance-light-grid-r07.css',
    'r09-owner-fix-overlay/source/plugin/xigua_hb/static/tgb-r05/detail-light-grid-r05.css',
    'r09-owner-fix-overlay/source/plugin/tb_cus_xiguahh/static/tgb-r07/sign-wallet-light-grid-r07.css',
    'r09-owner-fix-overlay/source/plugin/tb_toutiao/static/tgb-r07/promotion-light-grid-r07.css',
    'r09-owner-fix-overlay/source/plugin/view/static/tgb-r08/sign-light-grid-r08.css'
)) {
    $safeAreaCss = Read-RepoFile $relative
    Forbid-Text $safeAreaCss 'padding-top: 28px !important' "hard-coded duplicate top inset remains: $relative"
    Forbid-Text $safeAreaCss 'padding-top: 36px !important' "hard-coded duplicate top inset remains: $relative"
}

$cert = Read-RepoFile 'r09-owner-fix-overlay/source/plugin/xiaomy_certification/template/touch/rzres_1.htm'
$certCss = Read-RepoFile 'r09-owner-fix-overlay/source/plugin/xiaomy_certification/static/tgb-r06-certification-light-grid.css'
Require-Text $cert 'tgb-cert-result-card' 'certification result card was not redesigned'
Require-Text $cert 'tgb-r06-certification-light-grid.css?v=20260727-r09-owner-v2' 'certification result does not load versioned local CSS'
Forbid-Text $cert 'https://img.imehui.com/20251215/1765731723693eed8bcd901.png' 'certification result still uses the remote back icon'
Require-Text $certCss '.tgb-cert-result .tgb-cert-result-card' 'certification result CSS is missing'

$refresh = Read-RepoFile 'r09-owner-fix-overlay/source/plugin/xigua_hb/template/touch/sxtc.php'
Require-Text $refresh 'action="$SCRITPTNAME?id=xigua_hb&ac=refresh&do=sxtc" method="post"' 'refresh purchase action or method drifted'
Require-Text $refresh 'name="formhash" value="{FORMHASH}"' 'refresh formhash is missing'
Require-Text $refresh 'name="couponid"' 'refresh coupon field is missing'
Require-Text $refresh 'name="form[viptype]"' 'refresh package field is missing'
Require-Text $refresh 'name="dosubmit" value="1"' 'refresh submit protocol field is missing'
Require-Text $refresh 'HTMLFormElement.prototype.submit.call(purchaseForm)' 'refresh purchase does not bypass the global AJAX interceptor'

$router = Read-RepoFile 'android-app/app/src/main/java/com/suewammes/tuiguangbao/web/ExternalIntentRouter.kt'
$policy = Read-RepoFile 'android-app/app/src/main/java/com/suewammes/tuiguangbao/web/ExternalNavigationPolicy.kt'
$activity = Read-RepoFile 'android-app/app/src/main/java/com/suewammes/tuiguangbao/MainActivity.kt'
Forbid-Text $router 'AllowedHosts.isPaymentHttps' 'Android router still blocks third-party cashiers by payment host'
Require-Text $router 'component = null' 'intent component is not cleared'
Require-Text $router 'selector = null' 'intent selector is not cleared'
Require-Text $router 'Intent.CATEGORY_BROWSABLE' 'external intents are not constrained to browsable handlers'
foreach ($scheme in @('"about"','"content"','"data"','"file"','"http"','"javascript"')) {
    Require-Text $policy $scheme "dangerous scheme is not blocked: $scheme"
}
Require-Text $activity 'uri.scheme.equals("https", ignoreCase = true)' 'HTTPS new windows are not loaded into the main WebView'

$productionDeploy = Read-RepoFile 'scripts/remote/r09_deploy_production_candidate.sh'
Require-Text $productionDeploy 'EXPECTED_FILE_COUNT=79' 'production deploy gate is not locked to the 79-file owner repair candidate'
Require-Text $productionDeploy '128aaead7304ae1aa39df5ef99a2f69d4606246c63597d1eebf047065bd44939' 'production deploy gate is not locked to the verified pre-repair baseline'
Require-Text $productionDeploy '${DEPLOY_ID}-owner-repair' 'production rollback backup is not distinguished as the owner repair deployment'

$sign = Read-RepoFile 'r09-owner-fix-overlay/source/plugin/view/module/site/sign.php'
foreach ($visibleOldBrand in @('签米刚刚上线','签米会员签到','签米会员特权','开通签米会员后')) {
    Forbid-Text $sign $visibleOldBrand "visible old brand returned in sign page: $visibleOldBrand"
}

$status = Read-RepoFile 'CURRENT_STATUS.yaml'
Require-Text $status 'PASS_14_AFFECTED_PAGE_BROWSER_REVERIFY' 'CURRENT_STATUS does not record the completed owner-device repair re-verification'
Require-Text $status 'r09_owner_repair_production_deploy_id: 20260727T233853+0800' 'CURRENT_STATUS does not record the owner repair production deployment'
Require-Text $status 'server_release_apk_sha256: 5c69f3c4e64e214e901fae5574ec8b54c464e1cd19a907896742feb0327aa027' 'CURRENT_STATUS does not bind the latest server-signed APK'
Forbid-Text $status 'STATIC_PASS_HOME_HEADLINE_COMPONENT_PASS_OTHER_PAGES_BROWSER_BLOCKED' 'CURRENT_STATUS still records the resolved browser blocker'
Forbid-Text $status 'r09_global_h5_redesigned_verified: 39' 'invalid 39/39 verified claim remains active'

if ($errors.Count -gt 0) {
    $errors | ForEach-Object { Write-Error "[R09-OWNER-REPAIR] $_" }
    exit 1
}

Write-Host '[R09-OWNER-REPAIR] PASS'
