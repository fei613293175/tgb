param(
    [string]$PrivateRoot = ''
)

$ErrorActionPreference = 'Stop'
$handoffRoot = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..\..'))
$workspaceRoot = [System.IO.Path]::GetFullPath((Join-Path $handoffRoot '..'))
if ([string]::IsNullOrWhiteSpace($PrivateRoot)) {
    $PrivateRoot = Join-Path $workspaceRoot '.private\tg-signing'
}

$jdkHome = Get-ChildItem -LiteralPath (Join-Path $workspaceRoot '.toolchain\jdk21') -Directory -ErrorAction SilentlyContinue |
    Where-Object { Test-Path -LiteralPath (Join-Path $_.FullName 'bin\keytool.exe') } |
    Select-Object -First 1 -ExpandProperty FullName
if (-not $jdkHome) { throw 'JDK keytool not found. Run bootstrap-toolchain.ps1 first.' }

$keystore = Join-Path $PrivateRoot 'tuiguangbao-release.jks'
$properties = Join-Path $PrivateRoot 'keystore.properties'
if ((Test-Path -LiteralPath $keystore) -or (Test-Path -LiteralPath $properties)) {
    if ((Test-Path -LiteralPath $keystore) -and (Test-Path -LiteralPath $properties)) {
        Write-Host "Signing material already exists at $PrivateRoot; it was not replaced." -ForegroundColor Yellow
        exit 0
    }
    throw "Partial signing material exists at $PrivateRoot. Resolve it manually; no files were overwritten."
}

New-Item -ItemType Directory -Force -Path $PrivateRoot | Out-Null
$rng = [System.Security.Cryptography.RandomNumberGenerator]::Create()
function New-HexSecret {
    $bytes = New-Object byte[] 32
    $rng.GetBytes($bytes)
    return -join ($bytes | ForEach-Object { $_.ToString('x2') })
}
$storePassword = New-HexSecret
$keyPassword = New-HexSecret
$alias = 'tuiguangbao'
$keytool = Join-Path $jdkHome 'bin\keytool.exe'

& $keytool -genkeypair `
    -keystore $keystore `
    -storetype JKS `
    -storepass $storePassword `
    -keypass $keyPassword `
    -alias $alias `
    -keyalg RSA `
    -keysize 4096 `
    -validity 36500 `
    -dname 'CN=TuiGuangBao, OU=Mobile, O=Suewammes, L=Shanghai, ST=Shanghai, C=CN'
if ($LASTEXITCODE -ne 0) { throw 'keytool failed to create the release keystore.' }

$content = @(
    "keystorePath=$keystore"
    "storePassword=$storePassword"
    "keyAlias=$alias"
    "keyPassword=$keyPassword"
)
[System.IO.File]::WriteAllLines($properties, $content, [System.Text.UTF8Encoding]::new($false))

& icacls.exe $PrivateRoot /inheritance:r | Out-Null
& icacls.exe $PrivateRoot /grant:r "$($env:USERNAME):(OI)(CI)F" 'SYSTEM:(OI)(CI)F' | Out-Null
if ($LASTEXITCODE -ne 0) { throw 'Failed to restrict signing directory ACL.' }

$storePassword = $null
$keyPassword = $null
$content = $null
Write-Host "Release signing material created outside the handoff at $PrivateRoot." -ForegroundColor Green
Write-Host 'Passwords were not printed. Transfer both files to the owner through a separate secure channel.'
