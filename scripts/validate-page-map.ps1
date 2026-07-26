param()

$ErrorActionPreference = 'Stop'
$root = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
$ledger = Import-Csv -LiteralPath (Join-Path $root '03_PAGE_LEDGER.csv')
$sourceMap = Import-Csv -LiteralPath (Join-Path $root '12_PAGE_SOURCE_MAP.csv')
$ledgerIds = @($ledger.page_id | Sort-Object -Unique)
$sourceIds = @($sourceMap.page_id | Sort-Object -Unique)
$duplicates = @($sourceMap | Group-Object page_id | Where-Object Count -ne 1)
$missing = @(Compare-Object -ReferenceObject $sourceIds -DifferenceObject $ledgerIds -PassThru |
    Where-Object { $_ -in $ledgerIds })
$extra = @(Compare-Object -ReferenceObject $ledgerIds -DifferenceObject $sourceIds -PassThru |
    Where-Object { $_ -in $sourceIds })
$blank = @($sourceMap | Where-Object {
    -not $_.page_id -or -not $_.route_entry -or -not $_.controller -or
    -not $_.template_or_inline -or -not $_.mutation_guard -or -not $_.mapping_status
})

if ($ledger.Count -ne $sourceMap.Count -or $duplicates.Count -gt 0 -or
    $missing.Count -gt 0 -or $extra.Count -gt 0 -or $blank.Count -gt 0) {
    throw @"
页面源码映射失败：
ledger=$($ledger.Count) map=$($sourceMap.Count)
duplicates=$($duplicates.Name -join ',')
missing=$($missing -join ',')
extra=$($extra -join ',')
blank=$($blank.page_id -join ',')
"@
}

Write-Host "页面源码映射通过：$($ledger.Count)/$($ledger.Count)，无缺失、无重复、无必填空值。" -ForegroundColor Green
