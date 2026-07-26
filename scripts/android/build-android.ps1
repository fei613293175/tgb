param(
    [ValidateSet('debug', 'release')]
    [string]$Variant = 'debug',
    [string]$PrivateSigningRoot = ''
)

$ErrorActionPreference = 'Stop'
$handoffRoot = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..\..'))
$workspaceRoot = [System.IO.Path]::GetFullPath((Join-Path $handoffRoot '..'))
$projectRoot = Join-Path $handoffRoot 'android-app'
$toolchainRoot = Join-Path $workspaceRoot '.toolchain'
$jdkHome = Get-ChildItem -LiteralPath (Join-Path $toolchainRoot 'jdk21') -Directory -ErrorAction SilentlyContinue |
    Where-Object { Test-Path -LiteralPath (Join-Path $_.FullName 'bin\java.exe') } |
    Select-Object -First 1 -ExpandProperty FullName
if (-not $jdkHome) { throw 'JDK not found. Run scripts/android/bootstrap-toolchain.ps1.' }

$androidSdk = if (-not [string]::IsNullOrWhiteSpace($env:ANDROID_SDK_ROOT)) {
    $env:ANDROID_SDK_ROOT
} elseif (-not [string]::IsNullOrWhiteSpace($env:ANDROID_HOME)) {
    $env:ANDROID_HOME
} else {
    Join-Path $env:LOCALAPPDATA 'Android\Sdk'
}
if (-not (Test-Path -LiteralPath $androidSdk -PathType Container)) {
    throw "Android SDK not found: $androidSdk"
}

$env:JAVA_HOME = $jdkHome
$env:ANDROID_SDK_ROOT = $androidSdk
$env:Path = (Join-Path $jdkHome 'bin') + ';' + $env:Path

if ($Variant -eq 'release') {
    if ([string]::IsNullOrWhiteSpace($PrivateSigningRoot)) {
        $PrivateSigningRoot = Join-Path $workspaceRoot '.private\tg-signing'
    }
    $propertiesPath = Join-Path $PrivateSigningRoot 'keystore.properties'
    if (-not (Test-Path -LiteralPath $propertiesPath -PathType Leaf)) {
        throw 'Release signing properties are missing. Run create-signing-material.ps1.'
    }
    $signing = @{}
    foreach ($line in Get-Content -LiteralPath $propertiesPath -Encoding UTF8) {
        if ($line -match '^([^=]+)=(.*)$') {
            $signing[$Matches[1]] = $Matches[2]
        }
    }
    foreach ($key in @('keystorePath', 'storePassword', 'keyAlias', 'keyPassword')) {
        if (-not $signing.ContainsKey($key) -or [string]::IsNullOrWhiteSpace($signing[$key])) {
            throw "Signing property is missing: $key"
        }
    }
    if (-not (Test-Path -LiteralPath $signing.keystorePath -PathType Leaf)) {
        throw 'The configured release keystore does not exist.'
    }
    $env:TGB_KEYSTORE_PATH = $signing.keystorePath
    $env:TGB_KEYSTORE_PASSWORD = $signing.storePassword
    $env:TGB_KEY_ALIAS = $signing.keyAlias
    $env:TGB_KEY_PASSWORD = $signing.keyPassword
}

$tasks = if ($Variant -eq 'release') {
    @('testDebugUnitTest', 'lintRelease', 'assembleRelease')
} else {
    @('testDebugUnitTest', 'lintDebug', 'assembleDebug')
}
Push-Location $projectRoot
try {
    & .\gradlew.bat @tasks --no-daemon
    if ($LASTEXITCODE -ne 0) { throw "Android $Variant build failed." }
} finally {
    Pop-Location
}

$apk = Join-Path $projectRoot "app\build\outputs\apk\$Variant\app-$Variant.apk"
if (-not (Test-Path -LiteralPath $apk -PathType Leaf)) { throw "APK missing: $apk" }
$apkItem = Get-Item -LiteralPath $apk
if ($Variant -eq 'release' -and $apkItem.Length -lt 10485760) {
    throw "Signed release APK is smaller than 10 MiB: $($apkItem.Length) bytes"
}

$buildTools = Get-ChildItem -LiteralPath (Join-Path $androidSdk 'build-tools') -Directory |
    Sort-Object { [version]$_.Name } -Descending |
    Select-Object -First 1 -ExpandProperty FullName
$apksigner = Join-Path $buildTools 'apksigner.bat'
$aapt = Join-Path $buildTools 'aapt.exe'
& $apksigner verify --verbose --print-certs $apk
if ($LASTEXITCODE -ne 0) { throw 'APK signature verification failed.' }
& $aapt dump badging $apk | Select-Object -First 8
if ($LASTEXITCODE -ne 0) { throw 'APK metadata inspection failed.' }

Write-Host "APK_PATH=$apk"
Write-Host "APK_BYTES=$($apkItem.Length)"
Write-Host "APK_SHA256=$((Get-FileHash -LiteralPath $apk -Algorithm SHA256).Hash)"
Write-Host "Android $Variant build PASS." -ForegroundColor Green
