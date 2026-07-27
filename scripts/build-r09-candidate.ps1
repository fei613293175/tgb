param(
    [string]$OutputRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) '.runtime/r09-production-candidate'),
    [string]$ArchivePath = (Join-Path (Split-Path -Parent $PSScriptRoot) 'deliverables/r09-production-candidate-v5.tar.gz')
)

$ErrorActionPreference = 'Stop'
$repoRoot = [IO.Path]::GetFullPath((Split-Path -Parent $PSScriptRoot))
$OutputRoot = [IO.Path]::GetFullPath($OutputRoot)
$ArchivePath = [IO.Path]::GetFullPath($ArchivePath)
$reportRoot = Join-Path $repoRoot 'evidence/R09/preflight'
$expectedPrefix = $repoRoot.TrimEnd([IO.Path]::DirectorySeparatorChar) + [IO.Path]::DirectorySeparatorChar
if (-not $OutputRoot.StartsWith($expectedPrefix, [StringComparison]::OrdinalIgnoreCase)) {
    throw '[R09-CANDIDATE] output root must stay inside the repository'
}

$layers = @(
    'r02-site-overlay',
    'r03-site-overlay',
    'r04-site-overlay',
    'r05-site-overlay-v5',
    'r06-site-overlay',
    'r07-site-overlay',
    'r08-site-overlay',
    'r09-brand-overlay'
)

$excludedOutOfScope = [Collections.Generic.HashSet[string]]::new([StringComparer]::OrdinalIgnoreCase)
foreach ($relative in @('m/hyxy.html', 'm/help.html', 'm/gywm.html')) {
    [void]$excludedOutOfScope.Add($relative)
}

if (Test-Path -LiteralPath $OutputRoot) {
    Remove-Item -LiteralPath $OutputRoot -Recurse -Force
}
New-Item -ItemType Directory -Path $OutputRoot | Out-Null

$owners = @{}
$overrides = [Collections.Generic.List[string]]::new()
foreach ($layer in $layers) {
    $sourceRoot = Join-Path $repoRoot $layer
    if (-not (Test-Path -LiteralPath $sourceRoot -PathType Container)) {
        throw "[R09-CANDIDATE] missing layer: $layer"
    }
    $sourcePrefix = [IO.Path]::GetFullPath($sourceRoot).TrimEnd([IO.Path]::DirectorySeparatorChar) + [IO.Path]::DirectorySeparatorChar
    foreach ($file in Get-ChildItem -LiteralPath $sourceRoot -Recurse -File) {
        $relative = $file.FullName.Substring($sourcePrefix.Length).Replace('\','/')
        if ($excludedOutOfScope.Contains($relative)) {
            continue
        }
        if ($owners.ContainsKey($relative)) {
            $overrides.Add("$relative`t$($owners[$relative])`t$layer")
        }
        $owners[$relative] = $layer
        $target = Join-Path $OutputRoot $relative
        New-Item -ItemType Directory -Path (Split-Path -Parent $target) -Force | Out-Null
        Copy-Item -LiteralPath $file.FullName -Destination $target -Force
    }
}

foreach ($relative in $excludedOutOfScope) {
    if (Test-Path -LiteralPath (Join-Path $OutputRoot $relative)) {
        throw "[R09-CANDIDATE] out-of-scope file leaked into candidate: $relative"
    }
}

$manifest = Get-ChildItem -LiteralPath $OutputRoot -Recurse -File | ForEach-Object {
    $relative = $_.FullName.Substring($OutputRoot.Length + 1).Replace('\','/')
    "$(Get-FileHash -LiteralPath $_.FullName -Algorithm SHA256 | Select-Object -ExpandProperty Hash)  $relative"
}
$manifest = $manifest | Sort-Object
New-Item -ItemType Directory -Path $reportRoot -Force | Out-Null
[IO.File]::WriteAllText((Join-Path $reportRoot 'R09_FILES_SHA256.txt'), (($manifest -join "`n") + "`n"), [Text.UTF8Encoding]::new($false))
$overrideLines = @("path`tprevious_layer`twinning_layer") + $overrides
[IO.File]::WriteAllText((Join-Path $reportRoot 'R09_LAYER_OVERRIDES.tsv'), (($overrideLines -join "`n") + "`n"), [Text.UTF8Encoding]::new($false))

if (Test-Path -LiteralPath $ArchivePath) { Remove-Item -LiteralPath $ArchivePath -Force }
$tarPath = "$ArchivePath.tar"
if (Test-Path -LiteralPath $tarPath) { Remove-Item -LiteralPath $tarPath -Force }
try {
    tar -cf $tarPath --format ustar --mtime '2026-07-27 00:00:00' -C (Split-Path -Parent $OutputRoot) (Split-Path -Leaf $OutputRoot)
    if ($LASTEXITCODE -ne 0) { throw '[R09-CANDIDATE] tar creation failed' }
    $inputStream = [IO.File]::OpenRead($tarPath)
    try {
        $outputStream = [IO.File]::Create($ArchivePath)
        try {
            $gzipStream = [IO.Compression.GZipStream]::new($outputStream, [IO.Compression.CompressionLevel]::Optimal, $true)
            try { $inputStream.CopyTo($gzipStream) } finally { $gzipStream.Dispose() }
        } finally { $outputStream.Dispose() }
    } finally { $inputStream.Dispose() }
} finally {
    Remove-Item -LiteralPath $tarPath -Force -ErrorAction SilentlyContinue
}

$fileCount = @(Get-ChildItem -LiteralPath $OutputRoot -Recurse -File).Count
$sha = (Get-FileHash -LiteralPath $ArchivePath -Algorithm SHA256).Hash.ToLowerInvariant()
Write-Host '[R09-CANDIDATE] PASS'
Write-Host "[R09-CANDIDATE] files=$fileCount overrides=$($overrides.Count) sha256=$sha"
