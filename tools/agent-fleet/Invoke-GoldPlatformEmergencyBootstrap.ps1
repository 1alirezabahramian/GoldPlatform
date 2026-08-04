[CmdletBinding(SupportsShouldProcess)]
param(
    [string]$ProjectRoot = 'C:\Users\USER\Desktop\p\GoldPlatform',
    [string]$Repository = '1alirezabahramian/GoldPlatform',
    [string]$ExpectedBranch = 'feature/goldplatform-developer-mcp',
    [string]$FleetBranch = 'work/agent-fleet-foundation',
    [string]$BackupRoot = "$env:USERPROFILE\Desktop\GoldPlatform-Agent-Backups"
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest
[Console]::OutputEncoding = [System.Text.UTF8Encoding]::new($false)
$OutputEncoding = [System.Text.UTF8Encoding]::new($false)

function Invoke-Git {
    param([Parameter(Mandatory)][string[]]$Arguments)
    $output = & git -C $ProjectRoot @Arguments 2>&1
    if ($LASTEXITCODE -ne 0) {
        throw "git $($Arguments -join ' ') failed: $($output | Out-String)"
    }
    return $output
}

function Backup-Path {
    param([Parameter(Mandatory)][string]$Path, [Parameter(Mandatory)][string]$DestinationRoot)
    if (-not (Test-Path -LiteralPath $Path)) { return }

    $relative = [IO.Path]::GetRelativePath($ProjectRoot, $Path)
    $target = Join-Path $DestinationRoot $relative
    $targetDir = Split-Path -Parent $target
    New-Item -ItemType Directory -Force -Path $targetDir | Out-Null
    Move-Item -LiteralPath $Path -Destination $target -Force
}

if (-not (Test-Path -LiteralPath $ProjectRoot)) {
    throw "Project root not found: $ProjectRoot"
}

$origin = ((Invoke-Git -Arguments @('remote','get-url','origin')) | Select-Object -First 1).Trim()
$normalizedOrigin = $origin.TrimEnd('/') -replace '\.git$', ''
$normalizedExpected = "https://github.com/$Repository"
if ($normalizedOrigin -ne $normalizedExpected) {
    throw "Origin mismatch. Expected '$normalizedExpected', found '$normalizedOrigin'."
}

$currentBranch = ((Invoke-Git -Arguments @('branch','--show-current')) | Select-Object -First 1).Trim()
if ($currentBranch -ne $ExpectedBranch) {
    throw "Branch mismatch. Expected '$ExpectedBranch', found '$currentBranch'."
}

$timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$backupDir = Join-Path $BackupRoot "fleet-bootstrap-$timestamp"
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

$before = ((Invoke-Git -Arguments @('rev-parse','HEAD')) | Select-Object -First 1).Trim()
$stashCreated = $false

try {
    $mcpPaths = @(
        (Join-Path $ProjectRoot 'tools\goldplatform-mcp\dist'),
        (Join-Path $ProjectRoot 'tools\goldplatform-mcp\node_modules'),
        (Join-Path $ProjectRoot 'tools\goldplatform-mcp\package-lock.json')
    )

    foreach ($path in $mcpPaths) {
        Backup-Path -Path $path -DestinationRoot $backupDir
    }

    $remainingDirty = (& git -C $ProjectRoot status --porcelain 2>&1 | Out-String).Trim()
    if ($LASTEXITCODE -ne 0) { throw 'Could not inspect working tree.' }
    if (-not [string]::IsNullOrWhiteSpace($remainingDirty)) {
        Invoke-Git -Arguments @('stash','push','-u','-m',"agent-fleet-bootstrap-$timestamp") | Out-Null
        $stashCreated = $true
    }

    Invoke-Git -Arguments @('fetch','--prune','origin',$ExpectedBranch,$FleetBranch) | Out-Null
    Invoke-Git -Arguments @('merge','--ff-only',"origin/$ExpectedBranch") | Out-Null

    $bootstrapTemp = Join-Path $env:TEMP "GoldPlatform-Fleet-$timestamp"
    if (Test-Path -LiteralPath $bootstrapTemp) {
        Remove-Item -LiteralPath $bootstrapTemp -Recurse -Force
    }
    New-Item -ItemType Directory -Force -Path $bootstrapTemp | Out-Null

    $archive = Join-Path $bootstrapTemp 'fleet.zip'
    $archiveUrl = "https://github.com/$Repository/archive/refs/heads/$FleetBranch.zip"
    Invoke-WebRequest -Uri $archiveUrl -OutFile $archive -UseBasicParsing
    Expand-Archive -LiteralPath $archive -DestinationPath $bootstrapTemp -Force

    $sourceRoot = Get-ChildItem -LiteralPath $bootstrapTemp -Directory | Select-Object -First 1
    if ($null -eq $sourceRoot) { throw 'Fleet archive extraction failed.' }

    $fleetSource = Join-Path $sourceRoot.FullName 'tools\agent-fleet'
    $localAgentSource = Join-Path $sourceRoot.FullName 'tools\local-agent'
    if (-not (Test-Path -LiteralPath $fleetSource)) { throw 'Fleet source not found in archive.' }
    if (-not (Test-Path -LiteralPath $localAgentSource)) { throw 'Local agent source not found in archive.' }

    Copy-Item -LiteralPath $fleetSource -Destination (Join-Path $ProjectRoot 'tools') -Recurse -Force
    Copy-Item -LiteralPath $localAgentSource -Destination (Join-Path $ProjectRoot 'tools') -Recurse -Force

    $installer = Join-Path $ProjectRoot 'tools\agent-fleet\Install-GoldPlatformAgentFleet.ps1'
    if (-not (Test-Path -LiteralPath $installer)) { throw 'Fleet installer not found after bootstrap copy.' }

    $tokens = $null
    $errors = $null
    [void][System.Management.Automation.Language.Parser]::ParseFile($installer, [ref]$tokens, [ref]$errors)
    if ($errors.Count -gt 0) {
        throw "Installer syntax validation failed: $($errors[0].Message)"
    }

    if ($PSCmdlet.ShouldProcess($env:COMPUTERNAME, 'Install GoldPlatform Agent Fleet')) {
        & pwsh -NoProfile -NonInteractive -ExecutionPolicy Bypass -File $installer -ProjectRoot $ProjectRoot
        if ($LASTEXITCODE -ne 0) { throw "Fleet installer failed with exit code $LASTEXITCODE" }
    }

    $after = ((Invoke-Git -Arguments @('rev-parse','HEAD')) | Select-Object -First 1).Trim()
    [pscustomobject]@{
        status = 'PASSED'
        computer = $env:COMPUTERNAME
        branch = $currentBranch
        from_commit = $before
        to_commit = $after
        backup = $backupDir
        stash_created = $stashCreated
        installed = $true
        completed_at = (Get-Date).ToString('o')
    } | ConvertTo-Json -Depth 4
}
catch {
    try { & git -C $ProjectRoot reset --hard $before 2>&1 | Out-Null } catch { }

    $rollback = Join-Path $ProjectRoot 'tools\agent-fleet\Uninstall-GoldPlatformAgentFleet.ps1'
    if (Test-Path -LiteralPath $rollback) {
        try { & pwsh -NoProfile -NonInteractive -ExecutionPolicy Bypass -File $rollback -ProjectRoot $ProjectRoot } catch { }
    }

    [pscustomobject]@{
        status = 'FAILED'
        computer = $env:COMPUTERNAME
        branch = $currentBranch
        original_commit = $before
        backup = $backupDir
        stash_created = $stashCreated
        error = $_.Exception.Message
        failed_at = (Get-Date).ToString('o')
    } | ConvertTo-Json -Depth 4

    throw
}
