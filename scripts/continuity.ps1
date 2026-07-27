param(
    [ValidateSet('resume', 'verify', 'drift-audit')]
    [string]$Mode = 'resume'
)

$ErrorActionPreference = 'Stop'
$root = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
$manifest = Join-Path $root 'MANIFEST_SHA256.txt'
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
    '16_RUNTIME_CLICK_AUDIT.md',
    '17_RUNTIME_CLICK_GRAPH.csv',
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
        $actual = (Get-CanonicalManifestHash -Path $path -RelativePath $relative).ToUpperInvariant()
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

function Get-YamlScalar {
    param(
        [Parameter(Mandatory = $true)][string]$Text,
        [Parameter(Mandatory = $true)][string]$Name
    )
    $match = [regex]::Match($Text, "(?m)^[ \t]*$([regex]::Escape($Name)):\s*(.+?)\s*$")
    if (-not $match.Success) {
        throw "YAML 缺少字段：$Name"
    }
    return $match.Groups[1].Value.Trim('"', "'")
}

function Get-YamlList {
    param(
        [Parameter(Mandatory = $true)][string]$Text,
        [Parameter(Mandatory = $true)][string]$Name
    )
    $match = [regex]::Match(
        $Text,
        "(?ms)^$([regex]::Escape($Name)):\s*\r?\n(?<body>(?:^[ \t]+- [^\r\n]*(?:\r?\n|$))+?)^(?=\S|$)"
    )
    if (-not $match.Success) {
        throw "YAML 缺少列表：$Name"
    }
    return @(
        [regex]::Matches($match.Groups['body'].Value, '(?m)^\s+-\s+(.+?)\s*$') |
            ForEach-Object { $_.Groups[1].Value.Trim('"', "'") }
    )
}

function Assert-StateConsistency {
    $statusText = Get-Content -LiteralPath (Join-Path $root 'CURRENT_STATUS.yaml') -Raw -Encoding UTF8
    $nextText = Get-Content -LiteralPath (Join-Path $root 'NEXT_TASK.yaml') -Raw -Encoding UTF8
    $pairs = @(
        @('project_id', 'project_id'),
        @('current_release', 'release'),
        @('active_task_id', 'task_id'),
        @('release_status', 'status')
    )
    foreach ($pair in $pairs) {
        $left = Get-YamlScalar -Text $statusText -Name $pair[0]
        $right = Get-YamlScalar -Text $nextText -Name $pair[1]
        if ($left -ne $right) {
            throw "状态不一致：CURRENT_STATUS.$($pair[0])=$left，NEXT_TASK.$($pair[1])=$right"
        }
    }
    if ((Get-YamlScalar -Text $statusText -Name 'release_status') -ne 'IN_PROGRESS') {
        throw '当前接续包必须明确保持唯一版本 IN_PROGRESS。'
    }
}

function Assert-ReadFirstFiles {
    $nextText = Get-Content -LiteralPath (Join-Path $root 'NEXT_TASK.yaml') -Raw -Encoding UTF8
    $manifestPaths = [System.Collections.Generic.HashSet[string]]::new([System.StringComparer]::OrdinalIgnoreCase)
    foreach ($line in (Get-Content -LiteralPath $manifest -Encoding UTF8)) {
        if ($line -match '^[0-9a-fA-F]{64}  (.+)$') {
            [void]$manifestPaths.Add($Matches[1].Replace('\', '/'))
        }
    }
    foreach ($relative in (Get-YamlList -Text $nextText -Name 'read_first')) {
        $normalized = $relative.Replace('\', '/')
        if (-not (Test-Path -LiteralPath (Join-Path $root $normalized) -PathType Leaf)) {
            throw "NEXT_TASK.read_first 文件缺失：$normalized"
        }
        if (-not $manifestPaths.Contains($normalized)) {
            throw "NEXT_TASK.read_first 未纳入 MANIFEST：$normalized"
        }
    }
}

function Assert-ClickGraphConsistency {
    $statusText = Get-Content -LiteralPath (Join-Path $root 'CURRENT_STATUS.yaml') -Raw -Encoding UTF8
    $ledger = @(Import-Csv -LiteralPath (Join-Path $root '03_PAGE_LEDGER.csv'))
    $graph = @(Import-Csv -LiteralPath (Join-Path $root '17_RUNTIME_CLICK_GRAPH.csv'))
    $clicked = @($graph | Where-Object clicked -eq 'true')
    $pending = @($graph | Where-Object scope_effect -eq 'VISIBLE_ENTRY_PENDING_ISOLATED_CLICK')
    $hidden = @($graph | Where-Object scope_effect -in @('HIDDEN_OUT_OF_VISUAL_SCOPE', 'HIDDEN_PROTOCOL_ONLY'))
    $clickedChildren = @($clicked.child_page_id | Sort-Object -Unique)
    $expected = [ordered]@{
        click_graph_edges = $graph.Count
        click_graph_clicked_edges = $clicked.Count
        click_graph_pending_isolated_edges = $pending.Count
        click_graph_hidden_or_absent_edges = $hidden.Count
        click_proven_page_count = $clickedChildren.Count
    }
    foreach ($item in $expected.GetEnumerator()) {
        $recorded = [int](Get-YamlScalar -Text $statusText -Name $item.Key)
        if ($recorded -ne $item.Value) {
            throw "点击图统计漂移：$($item.Key) 记录=$recorded 实际=$($item.Value)"
        }
    }

    $unproven = @(
        $ledger |
            Where-Object {
                $_.scope -eq 'IN_SCOPE' -and
                $_.page_id -ne 'DESKTOP-SPLASH' -and
                -not $_.page_id.StartsWith('NATIVE-') -and
                $_.page_id -notin $clickedChildren
            } |
            Select-Object -ExpandProperty page_id
    )
    if ($unproven.Count -gt 0) {
        throw "视觉 IN_SCOPE 页面缺少已点击父边：$($unproven -join ', ')"
    }
    $invalidScope = @(
        $ledger |
            Where-Object {
                $_.scope -eq 'IN_SCOPE' -and
                $_.reachability -in @('DIRECT_URL_ONLY', 'SOURCE_ROUTE_FOUND', 'SOURCE_DISCOVERED', 'DORMANT_ENABLED', 'HIDDEN_ZERO_EXPOSURE')
            } |
            Select-Object -ExpandProperty page_id
    )
    if ($invalidScope.Count -gt 0) {
        throw "禁止直接 URL、源码、休眠或隐藏页面进入视觉范围：$($invalidScope -join ', ')"
    }
}

function Assert-ArtifactHashes {
    $statusText = Get-Content -LiteralPath (Join-Path $root 'CURRENT_STATUS.yaml') -Raw -Encoding UTF8
    $artifacts = @(
        @('handoff_apk', 'server_release_apk_sha256', 'server_release_apk_bytes'),
        @('r05_v5_overlay_archive', 'r05_v5_overlay_archive_sha256', $null)
    )
    foreach ($artifact in $artifacts) {
        $relative = Get-YamlScalar -Text $statusText -Name $artifact[0]
        $path = Join-Path $root $relative
        if (-not (Test-Path -LiteralPath $path -PathType Leaf)) {
            throw "正式制品缺失：$relative"
        }
        $actualHash = (Get-FileHash -LiteralPath $path -Algorithm SHA256).Hash.ToLowerInvariant()
        $expectedHash = (Get-YamlScalar -Text $statusText -Name $artifact[1]).ToLowerInvariant()
        if ($actualHash -ne $expectedHash) {
            throw "正式制品原始 SHA-256 不一致：$relative"
        }
        if ($null -ne $artifact[2]) {
            $actualBytes = (Get-Item -LiteralPath $path).Length
            $expectedBytes = [long](Get-YamlScalar -Text $statusText -Name $artifact[2])
            if ($actualBytes -ne $expectedBytes) {
                throw "正式制品字节数不一致：$relative"
            }
        }
    }
}

function Assert-GitCheckpoint {
    if (-not (Test-Path -LiteralPath (Join-Path $root '.git'))) {
        throw '交接目录不是 Git 工作树，无法证明跨电脑恢复。'
    }
    $dirty = @(& git -C $root status --porcelain=v1)
    if ($LASTEXITCODE -ne 0 -or $dirty.Count -gt 0) {
        throw 'Git 工作树不是干净的已冻结检查点；必须提交全部交接文件后再运行漂移审计。'
    }
    $upstream = (& git -C $root rev-parse --abbrev-ref --symbolic-full-name '@{u}' 2>$null).Trim()
    if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($upstream)) {
        throw '当前分支没有上游分支，无法证明新电脑可恢复。'
    }
    $head = (& git -C $root rev-parse HEAD).Trim()
    $remoteHead = (& git -C $root rev-parse $upstream).Trim()
    if ($LASTEXITCODE -ne 0 -or $head -ne $remoteHead) {
        throw "当前提交尚未推送到上游：HEAD=$head upstream=$remoteHead"
    }
}

function Show-Resume {
    Test-Manifest
    Assert-StateConsistency
    Assert-ReadFirstFiles
    Assert-ClickGraphConsistency
    Assert-ArtifactHashes
    Write-Host ''
    Write-Host '当前状态' -ForegroundColor Cyan
    Get-Content -LiteralPath (Join-Path $root 'CURRENT_STATUS.yaml') -Encoding UTF8
    Write-Host ''
    Write-Host '唯一下一任务' -ForegroundColor Cyan
    Get-Content -LiteralPath (Join-Path $root 'NEXT_TASK.yaml') -Encoding UTF8
    Write-Host ''
    Write-Host '接管提醒：收到“继续开发 / 立即开发 / 立即开始”任一触发词后，先完整阅读 read_first、硬门禁、决定和踩坑，再从唯一 IN_PROGRESS 任务继续；不得直接改生产。' -ForegroundColor Yellow
}

function Test-Drift {
    Test-Manifest
    Assert-StateConsistency
    Assert-ReadFirstFiles
    Assert-ClickGraphConsistency
    Assert-ArtifactHashes
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
    $graph = @(Import-Csv -LiteralPath (Join-Path $root '17_RUNTIME_CLICK_GRAPH.csv'))
    Write-Host "已点击边：$(@($graph | Where-Object clicked -eq 'true').Count)"
    Write-Host "待隔离点击边：$(@($graph | Where-Object scope_effect -eq 'VISIBLE_ENTRY_PENDING_ISOLATED_CLICK').Count)"
    Write-Host "隐藏或不存在边：$(@($graph | Where-Object scope_effect -in @('HIDDEN_OUT_OF_VISUAL_SCOPE','HIDDEN_PROTOCOL_ONLY')).Count)"

    if ($failed.Count -gt 0) {
        throw "漂移检查失败。先加固文档/状态，再关版。"
    }
    Assert-GitCheckpoint
    Write-Host '结构化漂移检查通过；仍必须人工完成 VERSION_CLOSEOUT_TEMPLATE.md 的全局审计。' -ForegroundColor Green
}

switch ($Mode) {
    'verify' { Test-Manifest }
    'resume' { Show-Resume }
    'drift-audit' { Test-Drift }
}
