[CmdletBinding(SupportsShouldProcess)]
param(
    [string]$ProjectRoot = (Resolve-Path "$PSScriptRoot\..\..").Path,
    [string]$TaskName = 'GoldPlatform Agent Fleet Watchdog',
    [string]$InstallRoot = "$env:ProgramData\GoldPlatform\AgentHost",
    [switch]$EnableRecovery
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

$manifestPath = Join-Path $ProjectRoot 'tools\agent-fleet\agents.manifest.json'
$watchdogPath = Join-Path $ProjectRoot 'tools\agent-fleet\Invoke-GoldPlatformWatchdog.ps1'
$queuePath = Join-Path $ProjectRoot 'tools\local-agent\Invoke-GoldPlatformRemoteQueue.ps1'

foreach ($required in @($manifestPath, $watchdogPath, $queuePath)) {
    if (-not (Test-Path -LiteralPath $required)) {
        throw "Required file is missing: $required"
    }
}

$manifest = Get-Content -LiteralPath $manifestPath -Raw | ConvertFrom-Json
if ($null -eq $manifest.agents -or @($manifest.agents).Count -lt 1) {
    throw 'Agent Fleet manifest has no agents.'
}

$timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$backupRoot = Join-Path $InstallRoot "backup\$timestamp"
New-Item -ItemType Directory -Force -Path $backupRoot | Out-Null
New-Item -ItemType Directory -Force -Path (Join-Path $InstallRoot 'logs') | Out-Null

$existing = Get-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue
if ($null -ne $existing) {
    Export-ScheduledTask -TaskName $TaskName | Set-Content -Path (Join-Path $backupRoot 'watchdog-task.xml') -Encoding utf8
}

$pwsh = (Get-Command pwsh -ErrorAction Stop).Source
$arguments = @(
    '-NoProfile',
    '-NonInteractive',
    '-ExecutionPolicy', 'Bypass',
    '-File', ('"' + $watchdogPath + '"'),
    '-ProjectRoot', ('"' + $ProjectRoot + '"')
)
if ($EnableRecovery) {
    $arguments += '-AllowRestart'
}

$action = New-ScheduledTaskAction -Execute $pwsh -Argument ($arguments -join ' ')
$triggerStartup = New-ScheduledTaskTrigger -AtStartup
$triggerPeriodic = New-ScheduledTaskTrigger -Once -At (Get-Date).AddMinutes(1) -RepetitionInterval (New-TimeSpan -Minutes 5)
$settings = New-ScheduledTaskSettingsSet -StartWhenAvailable -MultipleInstances IgnoreNew -ExecutionTimeLimit (New-TimeSpan -Minutes 3)
$principal = New-ScheduledTaskPrincipal -UserId $env:USERNAME -LogonType Interactive -RunLevel Limited

if ($PSCmdlet.ShouldProcess($TaskName, 'Install or update scheduled watchdog task')) {
    Register-ScheduledTask -TaskName $TaskName -Action $action -Trigger @($triggerStartup, $triggerPeriodic) -Settings $settings -Principal $principal -Force | Out-Null
}

$installRecord = [ordered]@{
    installed_at = Get-Date -Format o
    computer = $env:COMPUTERNAME
    user = $env:USERNAME
    project_root = $ProjectRoot
    task_name = $TaskName
    recovery_enabled = [bool]$EnableRecovery
    manifest_agents = @($manifest.agents | ForEach-Object { $_.name })
    backup_root = $backupRoot
}
$recordPath = Join-Path $InstallRoot 'installation.json'
$installRecord | ConvertTo-Json -Depth 5 | Set-Content -Path $recordPath -Encoding utf8

Write-Output 'AGENT_FLEET_INSTALL=SUCCESS'
Write-Output "TASK=$TaskName"
Write-Output "RECORD=$recordPath"
Write-Output "BACKUP=$backupRoot"
