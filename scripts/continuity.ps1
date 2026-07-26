param(
    [ValidateSet('resume', 'verify', 'drift-audit')]
    [string]$Mode = 'resume'
)

$ErrorActionPreference = 'Stop'
$root = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
$manifest = Join-Path $root 'MANIFEST_SHA256.txt'
$excludedPrefixes = @(
    '.git/',
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
    if ($normalized -match '^deliverables/.+\.(apk|apks|aab)$') { return $true }
    foreach ($prefix in $excludedPrefixes) {
        if ($normalized.StartsWith($prefix, [System.StringComparison]::OrdinalIgnoreCase)) {
            return $true
        }
    }
    return $false
}

$required = @(
    'README_FIRST.md',
    '00_PROJECT_CHARTER.md',
    '01_BASELINE_FACTS.md',
    '02_PAGE_EXPERIENCE_LOG.md',
    '03_PAGE_LEDGER.csv',
    '04_VISUAL_SYSTEM.md',
    '05_VERSION_ROADMAP.md',
    '06_HARD_GATES.md',
    '07_TEST_MATRIX.md',
    '08_CONTINUITY_PROTOCOL.md',
    '09_REMOTE_ACCESS_AND_SAFETY.md',
    '10_ANDROID_APP_SPEC.md',
    '11_BRAND_GUIDE.md',
    '12_PAGE_SOURCE_MAP.csv',
    '13_SIDE_EFFECT_TEST_PLAN.md',
    '14_ANDROID_TOOLCHAIN.md',
    '15_GITHUB_ACTIONS.md',
    'STYLE_TOKENS.json',
    'CURRENT_STATUS.yaml',
    'NEXT_TASK.yaml',
    'DECISIONS.md',
    'LESSONS_LEARNED.md',
    'VERSION_CLOSEOUT_TEMPLATE.md',
    'EVIDENCE_INDEX.md',
    'MANIFEST_SHA256.txt'
)

function Assert-RequiredFiles {
    $missing = @()
    foreach ($relative in $required) {
        if (-not (Test-Path -LiteralPath (Join-Path $root $relative) -PathType Leaf)) {
            $missing += $relative
        }
    }
    if ($missing.Count -gt 0) {
        throw "交接包缺少必需文件：$($missing -join ', ')"
    }
}

function Test-Manifest {
    Assert-RequiredFiles
    $failures = @()
    $seen = [System.Collections.Generic.HashSet[string]]::new([System.StringComparer]::OrdinalIgnoreCase)
    $rootPrefix = $root.TrimEnd([System.IO.Path]::DirectorySeparatorChar) + [System.IO.Path]::DirectorySeparatorChar
    $lines = Get-Content -LiteralPath $manifest -Encoding UTF8
    foreach ($line in $lines) {
        if ([string]::IsNullOrWhiteSpace($line) -or $line.StartsWith('#')) {
            continue
        }
        if ($line -notmatch '^([0-9a-fA-F]{64})  (.+)$') {
            $failures += "格式错误：$line"
            continue
        }
        $expected = $Matches[1].ToUpperInvariant()
        $relative = $Matches[2].Replace('/', [System.IO.Path]::DirectorySeparatorChar)
        $path = [System.IO.Path]::GetFullPath((Join-Path $root $relative))
        if (-not $path.StartsWith($rootPrefix, [System.StringComparison]::OrdinalIgnoreCase)) {
            $failures += "越界路径：$relative"
            continue
        }
        if (-not (Test-Path -LiteralPath $path -PathType Leaf)) {
            $failures += "文件缺失：$relative"
            continue
        }
        $actual = (Get-FileHash -LiteralPath $path -Algorithm SHA256).Hash
        if ($actual -ne $expected) {
            $failures += "哈希不匹配：$relative"
        }
        [void]$seen.Add($relative)
    }
    $allFiles = Get-ChildItem -LiteralPath $root -Recurse -File |
        Where-Object {
            if ($_.FullName -eq $manifest) { return $false }
            $relative = $_.FullName.Substring($rootPrefix.Length)
            return -not (Test-IsExcluded $relative)
        }
    foreach ($file in $allFiles) {
        $relative = $file.FullName.Substring($rootPrefix.Length)
        if (-not $seen.Contains($relative)) {
            $failures += "MANIFEST 未登记：$relative"
        }
    }
    foreach ($relative in $seen) {
        if (-not (Test-Path -LiteralPath (Join-Path $root $relative) -PathType Leaf)) {
            $failures += "MANIFEST 多余条目：$relative"
        }
    }
    if ($failures.Count -gt 0) {
        throw "MANIFEST 校验失败：`n$($failures -join "`n")"
    }
    Write-Host 'MANIFEST_SHA256.txt 校验通过。' -ForegroundColor Green
}

function Show-Resume {
    Test-Manifest
    Write-Host ''
    Write-Host '当前状态' -ForegroundColor Cyan
    Get-Content -LiteralPath (Join-Path $root 'CURRENT_STATUS.yaml') -Encoding UTF8
    Write-Host ''
    Write-Host '唯一下一任务' -ForegroundColor Cyan
    Get-Content -LiteralPath (Join-Path $root 'NEXT_TASK.yaml') -Encoding UTF8
    Write-Host ''
    Write-Host '接管提醒：先完整阅读硬门禁、项目章程、页面台账、视觉规范、Android 规范、决定和踩坑；不得直接改生产。' -ForegroundColor Yellow
}

function Test-Drift {
    Test-Manifest
    $statusText = Get-Content -LiteralPath (Join-Path $root 'CURRENT_STATUS.yaml') -Raw -Encoding UTF8
    $gateText = Get-Content -LiteralPath (Join-Path $root '06_HARD_GATES.md') -Raw -Encoding UTF8
    $tokens = Get-Content -LiteralPath (Join-Path $root 'STYLE_TOKENS.json') -Raw -Encoding UTF8 | ConvertFrom-Json
    $ledger = Import-Csv -LiteralPath (Join-Path $root '03_PAGE_LEDGER.csv')
    $sourceMap = Import-Csv -LiteralPath (Join-Path $root '12_PAGE_SOURCE_MAP.csv')
    $ledgerIds = @($ledger.page_id | Sort-Object -Unique)
    $sourceIds = @($sourceMap.page_id | Sort-Object -Unique)
    $mappingEqual = ($ledgerIds.Count -eq $sourceIds.Count) -and
        (-not (Compare-Object -ReferenceObject $ledgerIds -DifferenceObject $sourceIds))
    $mapDuplicates = @($sourceMap | Group-Object page_id | Where-Object Count -ne 1)

    $checks = [ordered]@{
        'project_id' = $statusText.Contains('project_id: TG-H5-UI-REDESIGN')
        'business_frozen' = $statusText.Contains('business_behavior_change_allowed: false')
        'light_only' = $statusText.Contains('dark_page_theme_allowed: false') -and ($tokens.meta.darkPageBackgroundAllowed -eq $false)
        'brand_name' = $statusText.Contains('product_name: 推广宝')
        'android_package' = $statusText.Contains('com.suewammes.tuiguangbao')
        'apk_floor' = $statusText.Contains('10485760')
        'hard_gate_brand' = $gateText.Contains('推广宝')
        'hard_gate_drift' = $gateText.Contains('关版漂移审计')
        'ledger_present' = $ledger.Count -gt 0
        'page_map_coverage' = $mappingEqual -and ($mapDuplicates.Count -eq 0)
        'side_effect_plan_present' = Test-Path -LiteralPath (Join-Path $root '13_SIDE_EFFECT_TEST_PLAN.md') -PathType Leaf
        'single_in_progress_release' = ([regex]::Matches($statusText, 'release_status: IN_PROGRESS')).Count -eq 1
    }

    $failed = @($checks.GetEnumerator() | Where-Object { -not $_.Value })
    foreach ($item in $checks.GetEnumerator()) {
        $label = if ($item.Value) { 'PASS' } else { 'FAIL' }
        Write-Host "$label $($item.Key)"
    }
    Write-Host "页面台账总数：$($ledger.Count)"
    Write-Host "源码映射总数：$($sourceMap.Count)"
    Write-Host "真实观察：$(@($ledger | Where-Object evidence_status -eq 'OBSERVED').Count)"
    Write-Host "待运行时验证：$(@($ledger | Where-Object evidence_status -in @('SOURCE_DISCOVERED','DORMANT_ENABLED','INTERACTIVE_ONLY','PLANNED')).Count)"

    if ($failed.Count -gt 0) {
        throw "漂移检查失败。先加固文档/状态，再关版。"
    }
    Write-Host '结构化漂移检查通过；仍必须人工完成 VERSION_CLOSEOUT_TEMPLATE.md 的全局审计。' -ForegroundColor Green
}

switch ($Mode) {
    'verify' { Test-Manifest }
    'resume' { Show-Resume }
    'drift-audit' { Test-Drift }
}
