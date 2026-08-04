[CmdletBinding()]
param(
    [string]$ProjectRoot = 'C:\Users\USER\Desktop\p\GoldPlatform',
    [string]$InstallRoot = "$env:ProgramData\GoldPlatform\GoldRescue",
    [string]$ExpectedRepository = 'https://github.com/1alirezabahramian/GoldPlatform.git',
    [string]$ApprovedBranch = 'feature/goldplatform-developer-mcp',
    [ValidateSet('heartbeat','inspect','repair-agent','install-fleet','restart-dorsandesk')]
    [string]$Command = 'heartbeat',
    [switch]$EnableRemoteRecovery
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest
[Console]::OutputEncoding = [System.Text.UTF8Encoding]::new($false)
$OutputEncoding = [System.Text.UTF8Encoding]::new($false)

function Write-Audit {
    param([string]$Event,[hashtable]$Data)
    $logDir = Join-Path $InstallRoot 'logs'
    New-Item -ItemType Directory -Force -Path $logDir | Out-Null
    $record = [ordered]@{
        timestamp = (Get-Date).ToString('o')
        computer = $env:COMPUTERNAME
        user = $env:USERNAME
        agent = 'GoldRescue'
        event = $Event
        data = $Data
    }
    ($record | ConvertTo-Json -Depth 8 -Compress) | Add-Content -Path (Join-Path $logDir 'audit.jsonl') -Encoding utf8
}

function Assert-Repository {
    if (-not (Test-Path -LiteralPath $ProjectRoot)) { throw "Project root not found: $ProjectRoot" }
    $origin = (& git -C $ProjectRoot remote get-url origin).Trim()
    if ($LASTEXITCODE -ne 0) { throw 'Could not read git origin.' }
    $normalize = { param($v) (($v.TrimEnd('/')) -replace '\.git$','') }
    if ((& $normalize $origin) -ne (& $normalize $ExpectedRepository)) { throw "Origin mismatch: $origin" }
}

function Get-State {
    Assert-Repository
    $branch = (& git -C $ProjectRoot branch --show-current).Trim()
    $head = (& git -C $ProjectRoot rev-parse HEAD).Trim()
    $dirty = @(& git -C $ProjectRoot status --porcelain)
    [ordered]@{
        computer = $env:COMPUTERNAME
        branch = $branch
        head = $head
        dirty_count = $dirty.Count
        docker = [bool](Get-Command docker -ErrorAction SilentlyContinue)
        pwsh = [bool](Get-Command pwsh -ErrorAction SilentlyContinue)
        queue_task = [bool](Get-ScheduledTask -TaskName 'GoldPlatform Remote Command Queue' -ErrorAction SilentlyContinue)
        rescue_enabled = [bool]$EnableRemoteRecovery
    }
}

function Backup-AgentFiles {
    $stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
    $backup = Join-Path $InstallRoot "backup\$stamp"
    New-Item -ItemType Directory -Force -Path $backup | Out-Null
    foreach ($relative in @(
        'tools\local-agent\Invoke-GoldPlatformRemoteQueue.ps1',
        'tools\local-agent\Invoke-GoldPlatformSelfUpdate.ps1',
        'tools\local-agent\Invoke-GoldPlatformHealthCheck.ps1'
    )) {
        $source = Join-Path $ProjectRoot $relative
        if (Test-Path -LiteralPath $source) {
            $dest = Join-Path $backup $relative
            New-Item -ItemType Directory -Force -Path (Split-Path $dest -Parent) | Out-Null
            Copy-Item -LiteralPath $source -Destination $dest -Force
        }
    }
    return $backup
}

function Repair-Agent {
    if (-not $EnableRemoteRecovery) { throw 'Remote recovery is disabled by policy.' }
    Assert-Repository
    $branch = (& git -C $ProjectRoot branch --show-current).Trim()
    if ($branch -ne $ApprovedBranch) { throw "Branch mismatch. Expected '$ApprovedBranch', found '$branch'." }

    $backup = Backup-AgentFiles
    Write-Audit 'backup-created' @{ path = $backup }

    & git -C $ProjectRoot fetch --prune origin $ApprovedBranch
    if ($LASTEXITCODE -ne 0) { throw 'git fetch failed.' }

    foreach ($path in @(
        'tools/local-agent/Invoke-GoldPlatformRemoteQueue.ps1',
        'tools/local-agent/Invoke-GoldPlatformSelfUpdate.ps1',
        'tools/local-agent/Invoke-GoldPlatformHealthCheck.ps1'
    )) {
        & git -C $ProjectRoot checkout "origin/$ApprovedBranch" -- $path
        if ($LASTEXITCODE -ne 0) { throw "Could not restore $path from approved branch." }
    }

    foreach ($script in @(
        (Join-Path $ProjectRoot 'tools\local-agent\Invoke-GoldPlatformRemoteQueue.ps1'),
        (Join-Path $ProjectRoot 'tools\local-agent\Invoke-GoldPlatformSelfUpdate.ps1'),
        (Join-Path $ProjectRoot 'tools\local-agent\Invoke-GoldPlatformHealthCheck.ps1')
    )) {
        $tokens = $null; $errors = $null
        [void][System.Management.Automation.Language.Parser]::ParseFile($script,[ref]$tokens,[ref]$errors)
        if ($errors.Count -gt 0) { throw "Syntax validation failed: $script :: $($errors[0].Message)" }
    }

    Write-Audit 'agent-repaired' @{ branch = $branch; backup = $backup }
    return @{ repaired = $true; backup = $backup }
}

function Install-Fleet {
    if (-not $EnableRemoteRecovery) { throw 'Remote recovery is disabled by policy.' }
    $installer = Join-Path $ProjectRoot 'tools\agent-fleet\Install-GoldPlatformAgentFleet.ps1'
    if (-not (Test-Path -LiteralPath $installer)) { throw "Fleet installer missing: $installer" }
    & pwsh -NoProfile -NonInteractive -ExecutionPolicy Bypass -File $installer -ProjectRoot $ProjectRoot
    if ($LASTEXITCODE -ne 0) { throw "Fleet installer failed with exit code $LASTEXITCODE" }
    Write-Audit 'fleet-installed' @{ project = $ProjectRoot }
    return @{ installed = $true }
}

function Restart-DorsanDesk {
    if (-not $EnableRemoteRecovery) { throw 'Remote recovery is disabled by policy.' }
    $service = Get-Service | Where-Object { $_.Name -match 'dorsan|dorsandesk' -or $_.DisplayName -match 'Dorsan' } | Select-Object -First 1
    if ($service) {
        Restart-Service -Name $service.Name -Force
        Write-Audit 'dorsandesk-service-restarted' @{ service = $service.Name }
        return @{ restarted = 'service'; name = $service.Name }
    }
    $process = Get-Process | Where-Object { $_.ProcessName -match 'dorsan|dorsandesk' } | Select-Object -First 1
    if ($process) {
        Stop-Process -Id $process.Id -Force
        Write-Audit 'dorsandesk-process-stopped' @{ process = $process.ProcessName }
        return @{ restarted = 'process-stop-only'; name = $process.ProcessName }
    }
    throw 'DorsanDesk service/process was not found.'
}

try {
    $result = switch ($Command) {
        'heartbeat' { Get-State }
        'inspect' { Get-State }
        'repair-agent' { Repair-Agent }
        'install-fleet' { Install-Fleet }
        'restart-dorsandesk' { Restart-DorsanDesk }
    }
    Write-Audit 'command-success' @{ command = $Command }
    [ordered]@{ status='success'; command=$Command; result=$result } | ConvertTo-Json -Depth 8
    exit 0
}
catch {
    Write-Audit 'command-failed' @{ command=$Command; error=$_.Exception.Message }
    [ordered]@{ status='failed'; command=$Command; error=$_.Exception.Message } | ConvertTo-Json -Depth 8
    exit 1
}
