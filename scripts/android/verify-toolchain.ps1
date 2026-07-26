param(
    [string]$ToolchainRoot = '',
    [string]$AndroidSdkRoot = ''
)

$ErrorActionPreference = 'Stop'
$handoffRoot = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..\..'))
if ([string]::IsNullOrWhiteSpace($ToolchainRoot)) {
    $ToolchainRoot = [System.IO.Path]::GetFullPath((Join-Path $handoffRoot '..\.toolchain'))
}
if ([string]::IsNullOrWhiteSpace($AndroidSdkRoot)) {
    if (-not [string]::IsNullOrWhiteSpace($env:ANDROID_SDK_ROOT)) {
        $AndroidSdkRoot = $env:ANDROID_SDK_ROOT
    } elseif (-not [string]::IsNullOrWhiteSpace($env:ANDROID_HOME)) {
        $AndroidSdkRoot = $env:ANDROID_HOME
    } else {
        $AndroidSdkRoot = Join-Path $env:LOCALAPPDATA 'Android\Sdk'
    }
}

$jdkHome = Get-ChildItem -LiteralPath (Join-Path $ToolchainRoot 'jdk21') -Directory -ErrorAction SilentlyContinue |
    Where-Object { Test-Path -LiteralPath (Join-Path $_.FullName 'bin\java.exe') } |
    Select-Object -First 1 -ExpandProperty FullName
if (-not $jdkHome) { throw 'JDK 21 not found. Run bootstrap-toolchain.ps1 first.' }

$required = @(
    (Join-Path $jdkHome 'bin\java.exe'),
    (Join-Path $jdkHome 'bin\javac.exe'),
    (Join-Path $jdkHome 'bin\keytool.exe'),
    (Join-Path $AndroidSdkRoot 'platform-tools\adb.exe'),
    (Join-Path $AndroidSdkRoot 'cmdline-tools\latest\bin\sdkmanager.bat')
)
foreach ($path in $required) {
    if (-not (Test-Path -LiteralPath $path -PathType Leaf)) {
        throw "Android toolchain missing: $path"
    }
}

$platforms = @(Get-ChildItem -LiteralPath (Join-Path $AndroidSdkRoot 'platforms') -Directory -ErrorAction SilentlyContinue)
$buildTools = @(Get-ChildItem -LiteralPath (Join-Path $AndroidSdkRoot 'build-tools') -Directory -ErrorAction SilentlyContinue)
if ($platforms.Count -eq 0 -or $buildTools.Count -eq 0) {
    throw 'Android platform or build-tools is missing.'
}

$env:JAVA_HOME = $jdkHome
$env:Path = (Join-Path $jdkHome 'bin') + ';' + $env:Path
& (Join-Path $jdkHome 'bin\java.exe') -version
& (Join-Path $AndroidSdkRoot 'platform-tools\adb.exe') version
& (Join-Path $AndroidSdkRoot 'cmdline-tools\latest\bin\sdkmanager.bat') --version
Write-Host "ANDROID_SDK_ROOT=$AndroidSdkRoot"
Write-Host "PLATFORMS=$($platforms.Name -join ',')"
Write-Host "BUILD_TOOLS=$($buildTools.Name -join ',')"
Write-Host 'Android toolchain verification PASS.' -ForegroundColor Green
