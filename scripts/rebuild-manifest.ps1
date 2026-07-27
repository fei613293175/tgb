param()

$ErrorActionPreference = 'Stop'
$root = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
$manifest = Join-Path $root 'MANIFEST_SHA256.txt'
$rootPrefix = $root.TrimEnd([System.IO.Path]::DirectorySeparatorChar) + [System.IO.Path]::DirectorySeparatorChar
. (Join-Path $PSScriptRoot 'manifest-canonical.ps1')

$excludedPrefixes = @(
    '.git/',
    '.runtime/',
    'ci-artifacts/',
    'android-app/.gradle/',
    'android-app/.idea/',
    'android-app/.kotlin/',
    'android-app/build/',
    'android-app/app/build/'
)
function Test-IsExcluded([string]$relative) {
    $normalized = $relative.Replace('\', '/')
    if ($normalized -eq 'android-app/local.properties') { return $true }
    foreach ($prefix in $excludedPrefixes) {
        if ($normalized.StartsWith($prefix, [System.StringComparison]::OrdinalIgnoreCase)) {
            return $true
        }
    }
    return $false
}

$lines = Get-ChildItem -LiteralPath $root -Recurse -File |
    Where-Object {
        if ($_.FullName -eq $manifest) { return $false }
        $relative = $_.FullName.Substring($rootPrefix.Length)
        return -not (Test-IsExcluded $relative)
    } |
    Sort-Object FullName |
    ForEach-Object {
        $relative = $_.FullName.Substring($rootPrefix.Length).Replace('\', '/')
        $hash = Get-CanonicalManifestHash -Path $_.FullName -RelativePath $relative
        "$hash  $relative"
    }

[System.IO.File]::WriteAllLines($manifest, $lines, [System.Text.UTF8Encoding]::new($false))
Write-Host "已重建 MANIFEST_SHA256.txt，共 $($lines.Count) 个文件。" -ForegroundColor Green
