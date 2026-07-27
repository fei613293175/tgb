param(
    [string]$RepoRoot = (Split-Path -Parent $PSScriptRoot)
)

$ErrorActionPreference = 'Stop'
$RepoRoot = [IO.Path]::GetFullPath($RepoRoot)
$repoPrefix = $RepoRoot.TrimEnd([IO.Path]::DirectorySeparatorChar) + [IO.Path]::DirectorySeparatorChar
$failures = [Collections.Generic.List[string]]::new()

function Add-Failure([string]$Message) {
    $failures.Add($Message)
}

function Get-YamlScalar {
    param(
        [Parameter(Mandatory = $true)][string]$Text,
        [Parameter(Mandatory = $true)][string]$Name
    )
    $match = [regex]::Match($Text, "(?m)^[ \t]*$([regex]::Escape($Name)):\s*(.+?)\s*$")
    if (-not $match.Success) {
        Add-Failure "YAML missing field: $Name"
        return $null
    }
    return $match.Groups[1].Value.Trim('"', "'")
}

$statusPath = Join-Path $RepoRoot 'CURRENT_STATUS.yaml'
$nextPath = Join-Path $RepoRoot 'NEXT_TASK.yaml'
$ledgerPath = Join-Path $RepoRoot '03_PAGE_LEDGER.csv'
$matrixPath = Join-Path $RepoRoot 'evidence/R09/final-readiness/FINAL_OWNER_VERIFICATION.csv'
$ownerSchemaPath = Join-Path $RepoRoot 'evidence/R09/final-readiness/OWNER_DEVICE_EVIDENCE_SCHEMA.json'

foreach ($path in @($statusPath, $nextPath, $ledgerPath, $matrixPath, $ownerSchemaPath)) {
    if (-not (Test-Path -LiteralPath $path -PathType Leaf)) {
        Add-Failure "required file missing: $path"
    }
}

if ($failures.Count -eq 0) {
    $statusText = Get-Content -LiteralPath $statusPath -Raw -Encoding UTF8
    $nextText = Get-Content -LiteralPath $nextPath -Raw -Encoding UTF8
    $ledger = @(Import-Csv -LiteralPath $ledgerPath)
    $matrix = @(Import-Csv -LiteralPath $matrixPath)
    try {
        $ownerSchema = Get-Content -LiteralPath $ownerSchemaPath -Raw -Encoding UTF8 | ConvertFrom-Json
    }
    catch {
        Add-Failure "owner evidence schema is invalid JSON: $($_.Exception.Message)"
        $ownerSchema = $null
    }

    $nonAndroid = @($ledger | Where-Object { $_.scope -eq 'IN_SCOPE' -and $_.family -ne 'android' })
    if ($nonAndroid.Count -ne 39 -or @($nonAndroid | Where-Object evidence_status -ne 'REDESIGNED_VERIFIED').Count -ne 0) {
        Add-Failure 'non-Android visual closeout is not 39/39 REDESIGNED_VERIFIED'
    }

    $android = @($ledger | Where-Object { $_.scope -eq 'IN_SCOPE' -and $_.family -eq 'android' })
    if ($android.Count -ne 6) {
        Add-Failure "Android IN_SCOPE count drifted: $($android.Count)"
    }
    $androidPending = @($android | Where-Object evidence_status -ne 'VERIFIED')
    if ($androidPending.Count -gt 0) {
        Add-Failure "Android ledger still pending: $($androidPending.page_id -join ', ')"
    }

    $requiredOwnerChecks = @(
        'APK-INSTALL-LAUNCH',
        'APP-SAFE-AREA',
        'GALLERY-UPLOAD',
        'ALIPAY-ROUNDTRIP',
        'TRUSTED-DOWNLOAD',
        'OFFLINE-RETRY'
    )
    $expectedMatrixColumns = @('check_id', 'requirement', 'status', 'evidence_path', 'evidence_sha256', 'notes')
    $actualMatrixColumns = @($matrix[0].PSObject.Properties.Name)
    if (($actualMatrixColumns -join "`n") -cne ($expectedMatrixColumns -join "`n")) {
        Add-Failure 'owner verification matrix columns drifted from contract'
    }
    $actualOwnerChecks = @($matrix.check_id | Sort-Object)
    if (($actualOwnerChecks -join "`n") -cne (($requiredOwnerChecks | Sort-Object) -join "`n")) {
        Add-Failure 'owner verification matrix IDs are incomplete, duplicated, or out of contract'
    }
    foreach ($row in $matrix) {
        if ($row.status -ne 'PASS') {
            Add-Failure "owner verification pending: $($row.check_id)"
            continue
        }
        if ([string]::IsNullOrWhiteSpace($row.evidence_path)) {
            Add-Failure "owner verification has no evidence path: $($row.check_id)"
            continue
        }
        if ($row.evidence_sha256 -notmatch '^[0-9a-f]{64}$') {
            Add-Failure "owner verification has invalid evidence SHA-256: $($row.check_id)"
            continue
        }
        $evidencePath = [IO.Path]::GetFullPath((Join-Path $RepoRoot $row.evidence_path))
        if (-not $evidencePath.StartsWith($repoPrefix, [StringComparison]::OrdinalIgnoreCase)) {
            Add-Failure "owner evidence path escapes repository: $($row.check_id)"
        }
        elseif (-not (Test-Path -LiteralPath $evidencePath -PathType Leaf)) {
            Add-Failure "owner evidence file missing: $($row.check_id)"
        }
        elseif ([IO.Path]::GetExtension($evidencePath) -ne '.json') {
            Add-Failure "owner evidence must be JSON: $($row.check_id)"
        }
        elseif ((Get-FileHash -LiteralPath $evidencePath -Algorithm SHA256).Hash.ToLowerInvariant() -ne $row.evidence_sha256) {
            Add-Failure "owner evidence SHA-256 mismatch: $($row.check_id)"
        }
        else {
            try {
                $evidence = Get-Content -LiteralPath $evidencePath -Raw -Encoding UTF8 | ConvertFrom-Json
            }
            catch {
                Add-Failure "owner evidence is invalid JSON: $($row.check_id)"
                continue
            }
            if ([int]$evidence.schema_version -ne 1 -or $evidence.check_id -ne $row.check_id -or
                $evidence.status -ne 'PASS' -or $evidence.tested_by -ne 'OWNER' -or
                $evidence.redaction_confirmed -ne $true) {
                Add-Failure "owner evidence identity or approval fields are invalid: $($row.check_id)"
            }
            $testedAt = [DateTimeOffset]::MinValue
            if (-not [DateTimeOffset]::TryParse([string]$evidence.tested_at, [ref]$testedAt)) {
                Add-Failure "owner evidence tested_at is invalid: $($row.check_id)"
            }
            if ([string]::IsNullOrWhiteSpace([string]$evidence.device.model) -or
                [string]::IsNullOrWhiteSpace([string]$evidence.device.android_version)) {
                Add-Failure "owner evidence device fields are incomplete: $($row.check_id)"
            }
            $expectedApkSha = (Get-YamlScalar -Text $statusText -Name 'server_release_apk_sha256').ToLowerInvariant()
            if ([string]::IsNullOrWhiteSpace([string]$evidence.apk.filename) -or
                ([string]$evidence.apk.sha256).ToLowerInvariant() -ne $expectedApkSha) {
                Add-Failure "owner evidence APK identity is invalid: $($row.check_id)"
            }
            $observations = @($evidence.observations)
            if ($observations.Count -eq 0 -or @($observations | Where-Object {
                        [string]::IsNullOrWhiteSpace([string]$_.step) -or $_.result -ne 'PASS'
                    }).Count -gt 0) {
                Add-Failure "owner evidence observations are incomplete: $($row.check_id)"
            }
            $attachments = @($evidence.attachments)
            if ($attachments.Count -eq 0) {
                Add-Failure "owner evidence has no attachment: $($row.check_id)"
            }
            foreach ($attachment in $attachments) {
                if ([string]::IsNullOrWhiteSpace([string]$attachment.path) -or $attachment.sha256 -notmatch '^[0-9a-f]{64}$') {
                    Add-Failure "owner attachment metadata is invalid: $($row.check_id)"
                    continue
                }
                $attachmentPath = [IO.Path]::GetFullPath((Join-Path $RepoRoot $attachment.path))
                if (-not $attachmentPath.StartsWith($repoPrefix, [StringComparison]::OrdinalIgnoreCase)) {
                    Add-Failure "owner attachment path escapes repository: $($row.check_id)"
                }
                elseif (-not (Test-Path -LiteralPath $attachmentPath -PathType Leaf)) {
                    Add-Failure "owner attachment file is missing: $($row.check_id)"
                }
                elseif ((Get-FileHash -LiteralPath $attachmentPath -Algorithm SHA256).Hash.ToLowerInvariant() -ne $attachment.sha256) {
                    Add-Failure "owner attachment SHA-256 mismatch: $($row.check_id)"
                }
            }
        }
    }

    if ($null -ne $ownerSchema) {
        $schemaChecks = @($ownerSchema.properties.check_id.enum | Sort-Object)
        if (($schemaChecks -join "`n") -cne (($requiredOwnerChecks | Sort-Object) -join "`n")) {
            Add-Failure 'owner evidence schema check IDs drifted from matrix contract'
        }
    }

    if ((Get-YamlScalar -Text $nextText -Name 'owner_verification_status') -ne 'PASS') {
        Add-Failure 'NEXT_TASK owner_verification_status is not PASS'
    }
    if ((Get-YamlScalar -Text $statusText -Name 'database_identifier_present_in_git_history') -ne 'false') {
        Add-Failure 'database identifier remains reachable in Git history'
    }
    if ((Get-YamlScalar -Text $statusText -Name 'database_identifier_stored_in_current_tree') -ne 'false' -or
        (Get-YamlScalar -Text $statusText -Name 'credentials_stored_in_current_tree') -ne 'false') {
        Add-Failure 'current-tree credential-free status is not verified'
    }
    if ((Get-YamlScalar -Text $statusText -Name 'database_credential_rotation_required') -ne 'false') {
        Add-Failure 'production database credential rotation is not verified'
    }

    $rotationEvidence = Get-YamlScalar -Text $statusText -Name 'database_credential_rotation_evidence'
    if ([string]::IsNullOrWhiteSpace($rotationEvidence) -or $rotationEvidence -eq 'PENDING_OWNER_ACTION') {
        Add-Failure 'database credential rotation evidence is pending'
    }
    else {
        $rotationEvidencePath = [IO.Path]::GetFullPath((Join-Path $RepoRoot $rotationEvidence))
        if (-not $rotationEvidencePath.StartsWith($repoPrefix, [StringComparison]::OrdinalIgnoreCase)) {
            Add-Failure 'database credential rotation evidence path escapes repository'
        }
        elseif (-not (Test-Path -LiteralPath $rotationEvidencePath -PathType Leaf)) {
            Add-Failure 'database credential rotation evidence file is missing'
        }
    }

    $exposedCommit = Get-YamlScalar -Text $statusText -Name 'database_history_exposed_commit'
    if (-not [string]::IsNullOrWhiteSpace($exposedCommit)) {
        $containingRefs = @(& git -C $RepoRoot for-each-ref "--contains=$exposedCommit" '--format=%(refname)' refs/heads refs/remotes 2>$null)
        if ($LASTEXITCODE -ne 0) {
            Add-Failure 'unable to verify exposed Git commit reachability'
        }
        elseif ($containingRefs.Count -gt 0) {
            Add-Failure "exposed Git commit remains reachable from refs: $($containingRefs -join ', ')"
        }
    }

    try {
        & (Join-Path $RepoRoot 'scripts/continuity.ps1') -Mode verify | Out-Null
    }
    catch {
        Add-Failure "continuity manifest verification failed: $($_.Exception.Message)"
    }

    $dirty = @(& git -C $RepoRoot status --porcelain=v1)
    if ($LASTEXITCODE -ne 0 -or $dirty.Count -gt 0) {
        Add-Failure 'Git worktree is not a clean checkpoint'
    }
    $upstream = (& git -C $RepoRoot rev-parse --abbrev-ref --symbolic-full-name '@{u}' 2>$null).Trim()
    if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($upstream)) {
        Add-Failure 'Git upstream is unavailable'
    }
    else {
        $head = (& git -C $RepoRoot rev-parse HEAD).Trim()
        $remoteHead = (& git -C $RepoRoot rev-parse $upstream).Trim()
        if ($head -ne $remoteHead) {
            Add-Failure 'local HEAD is not equal to the configured upstream'
        }
    }
}

if ($failures.Count -gt 0) {
    Write-Error ("[FINAL-PROJECT-READINESS] FAIL`n" + ($failures | ForEach-Object { "- $_" } | Out-String).TrimEnd())
    exit 1
}

Write-Host '[FINAL-PROJECT-READINESS] PASS'
Write-Host '[FINAL-PROJECT-READINESS] non_android=39 android=6 owner_checks=6 security=PASS checkpoint=PASS'
