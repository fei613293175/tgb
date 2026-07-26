$script:ManifestBinaryExtensions = [System.Collections.Generic.HashSet[string]]::new(
    [System.StringComparer]::OrdinalIgnoreCase
)
foreach ($extension in @(
    '.aab', '.aar', '.apk', '.apks', '.bytes', '.gif', '.gz', '.jar', '.jpeg', '.jpg',
    '.otf', '.pdf', '.png', '.so', '.webp', '.woff', '.woff2', '.zip'
)) {
    [void]$script:ManifestBinaryExtensions.Add($extension)
}

function Get-CanonicalManifestHash {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Path,
        [Parameter(Mandatory = $true)]
        [string]$RelativePath
    )

    $bytes = [System.IO.File]::ReadAllBytes($Path)
    $extension = [System.IO.Path]::GetExtension($RelativePath)
    if (-not $script:ManifestBinaryExtensions.Contains($extension)) {
        $normalized = [System.Collections.Generic.List[byte]]::new($bytes.Length)
        for ($index = 0; $index -lt $bytes.Length; $index++) {
            if (
                $bytes[$index] -eq 13 -and
                $index + 1 -lt $bytes.Length -and
                $bytes[$index + 1] -eq 10
            ) {
                $normalized.Add(10)
                $index++
            } else {
                $normalized.Add($bytes[$index])
            }
        }
        $bytes = $normalized.ToArray()
    }

    $sha256 = [System.Security.Cryptography.SHA256]::Create()
    try {
        return -join ($sha256.ComputeHash($bytes) | ForEach-Object { $_.ToString('x2') })
    } finally {
        $sha256.Dispose()
    }
}
