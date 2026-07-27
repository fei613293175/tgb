param(
    [string]$OutputRoot = (Join-Path (Split-Path -Parent $PSScriptRoot) '.runtime/r09-publish-visual-fix'),
    [string]$ArchivePath = (Join-Path (Split-Path -Parent $PSScriptRoot) 'deliverables/r09-publish-visual-fix-v2.tar.gz')
)

$ErrorActionPreference = 'Stop'
$repoRoot = [IO.Path]::GetFullPath((Split-Path -Parent $PSScriptRoot))
$OutputRoot = [IO.Path]::GetFullPath($OutputRoot)
$ArchivePath = [IO.Path]::GetFullPath($ArchivePath)
$relative = 'source/plugin/xigua_hb/template/touch/pub.php'
$source = Join-Path $repoRoot "r09-brand-overlay/$relative"

if (-not (Test-Path -LiteralPath $source -PathType Leaf)) { throw '[R09-PUBLISH-BUILD] source template missing' }
if (Test-Path -LiteralPath $OutputRoot) { Remove-Item -LiteralPath $OutputRoot -Recurse -Force }
$target = Join-Path $OutputRoot $relative
New-Item -ItemType Directory -Path (Split-Path -Parent $target) -Force | Out-Null
Copy-Item -LiteralPath $source -Destination $target

$files = @(Get-ChildItem -LiteralPath $OutputRoot -Recurse -File)
if ($files.Count -ne 1) { throw "[R09-PUBLISH-BUILD] expected one file, got $($files.Count)" }
$templateSha = (Get-FileHash -LiteralPath $target -Algorithm SHA256).Hash.ToLowerInvariant()
if ($templateSha -ne 'd959be90891cda64663e0fd1e70d31ec305cabfab45ce6ceb34ea8d96878cec9') {
    throw "[R09-PUBLISH-BUILD] template hash drifted: $templateSha"
}

if (Test-Path -LiteralPath $ArchivePath) { Remove-Item -LiteralPath $ArchivePath -Force }
$tarPath = "$ArchivePath.tar"
if (Test-Path -LiteralPath $tarPath) { Remove-Item -LiteralPath $tarPath -Force }
try {
    tar -cf $tarPath --format ustar --mtime '2026-07-27 00:00:00' -C (Split-Path -Parent $OutputRoot) (Split-Path -Leaf $OutputRoot)
    if ($LASTEXITCODE -ne 0) { throw '[R09-PUBLISH-BUILD] tar creation failed' }
    $input = [IO.File]::OpenRead($tarPath)
    try {
        $output = [IO.File]::Create($ArchivePath)
        try {
            $gzip = [IO.Compression.GZipStream]::new($output, [IO.Compression.CompressionLevel]::Optimal, $true)
            try { $input.CopyTo($gzip) } finally { $gzip.Dispose() }
        }
        finally { $output.Dispose() }
    }
    finally { $input.Dispose() }
}
finally {
    if (Test-Path -LiteralPath $tarPath) { Remove-Item -LiteralPath $tarPath -Force }
}

$archiveSha = (Get-FileHash -LiteralPath $ArchivePath -Algorithm SHA256).Hash.ToLowerInvariant()
Write-Host "[R09-PUBLISH-BUILD] PASS files=1 template_sha256=$templateSha archive_sha256=$archiveSha"
