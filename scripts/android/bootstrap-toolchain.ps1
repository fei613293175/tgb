param(
    [string]$ToolchainRoot = '',
    [string]$JdkMajor = '21'
)

$ErrorActionPreference = 'Stop'
$handoffRoot = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..\..'))
if ([string]::IsNullOrWhiteSpace($ToolchainRoot)) {
    $ToolchainRoot = [System.IO.Path]::GetFullPath((Join-Path $handoffRoot '..\.toolchain'))
}

$downloadRoot = Join-Path $ToolchainRoot 'downloads'
$jdkRoot = Join-Path $ToolchainRoot "jdk$JdkMajor"
$zip = Join-Path $downloadRoot "microsoft-jdk-$JdkMajor-windows-x64.zip"
$checksumFile = "$zip.sha256sum.txt"
$zipUrl = "https://aka.ms/download-jdk/microsoft-jdk-$JdkMajor-windows-x64.zip"
$checksumUrl = "$zipUrl.sha256sum.txt"

New-Item -ItemType Directory -Force -Path $downloadRoot | Out-Null
if (-not (Test-Path -LiteralPath $checksumFile -PathType Leaf)) {
    & curl.exe -L --fail --retry 3 --output $checksumFile $checksumUrl
    if ($LASTEXITCODE -ne 0) { throw 'JDK checksum download failed.' }
}
if (-not (Test-Path -LiteralPath $zip -PathType Leaf)) {
    & curl.exe -L --fail --retry 3 --output $zip $zipUrl
    if ($LASTEXITCODE -ne 0) { throw 'JDK archive download failed.' }
}

$expected = ((Get-Content -Raw -LiteralPath $checksumFile).Trim() -split '\s+')[0].ToUpperInvariant()
$actual = (Get-FileHash -Algorithm SHA256 -LiteralPath $zip).Hash
if ($actual -ne $expected) {
    throw "JDK checksum mismatch. expected=$expected actual=$actual"
}

$existing = Get-ChildItem -LiteralPath $jdkRoot -Directory -ErrorAction SilentlyContinue |
    Where-Object { Test-Path -LiteralPath (Join-Path $_.FullName 'bin\java.exe') } |
    Select-Object -First 1
if (-not $existing) {
    if (Test-Path -LiteralPath $jdkRoot) {
        throw "JDK target exists but is incomplete: $jdkRoot"
    }
    New-Item -ItemType Directory -Path $jdkRoot | Out-Null
    Expand-Archive -LiteralPath $zip -DestinationPath $jdkRoot
    $existing = Get-ChildItem -LiteralPath $jdkRoot -Directory |
        Where-Object { Test-Path -LiteralPath (Join-Path $_.FullName 'bin\java.exe') } |
        Select-Object -First 1
}
if (-not $existing) { throw 'Extracted JDK not found.' }

$java = Join-Path $existing.FullName 'bin\java.exe'
$javac = Join-Path $existing.FullName 'bin\javac.exe'
$keytool = Join-Path $existing.FullName 'bin\keytool.exe'
foreach ($required in @($java, $javac, $keytool)) {
    if (-not (Test-Path -LiteralPath $required -PathType Leaf)) {
        throw "Missing JDK tool: $required"
    }
}

Write-Host "JDK_HOME=$($existing.FullName)"
Write-Host "JDK_ARCHIVE_SHA256=$actual"
& $java -version
& $javac -version
Write-Host 'Android JDK bootstrap PASS.' -ForegroundColor Green
