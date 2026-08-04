[CmdletBinding()]
param(
    [string]$ProjectRoot = (Resolve-Path "$PSScriptRoot\..\..").Path,
    [string]$ReportDirectory = "$env:ProgramData\GoldPlatform\AgentHost\logs",
    [switch]$AllowRestart
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

New-Item -ItemType Directory -Force -Path $ReportDirectory | Out-Null
$timestamp = Get-Date -Format o
$report = [ordered]@{
    timestamp = $timestamp
    computer = $env:COMPUTERNAME
    project_root = $ProjectRoot
    allow_restart = [bool]$AllowRestart
    checks = @()
}

function Add-Check {
    param([string]$Name, [string]$Status, [string]$Detail)
    $report.checks += [ordered]@{ name = $Name; status = $Status; detail = $Detail }
}

function Test-CommandAvailable {
    param([string]$Name)
    return $null -ne (Get-Command $Name -ErrorAction SilentlyContinue)
}

# Agent queue scheduled task
$taskName = 'GoldPlatform Remote Command Queue'
$task = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
if ($null -eq $task) {
    Add-Check -Name 'agent_task' -Status 'missing' -Detail $taskName
}
else {
    $info = Get-ScheduledTaskInfo -TaskName $taskName
    Add-Check -Name 'agent_task' -Status ([string]$info.State) -Detail "LastRun=$($info.LastRunTime); LastResult=$($info.LastTaskResult)"
    if ($AllowRestart -and $info.State -eq 'Disabled') {
        Enable-ScheduledTask -TaskName $taskName | Out-Null
        Add-Check -Name 'agent_task_recovery' -Status 'enabled' -Detail $taskName
    }
}

# Docker and project containers
if (Test-CommandAvailable 'docker') {
    try {
        $dockerVersion = (& docker version --format '{{.Server.Version}}' 2>&1 | Out-String).Trim()
        if ($LASTEXITCODE -eq 0) {
            Add-Check -Name 'docker' -Status 'ok' -Detail $dockerVersion
            $compose = (& docker compose -f (Join-Path $ProjectRoot 'docker-compose.yml') ps --format json 2>&1 | Out-String).Trim()
            Add-Check -Name 'docker_compose' -Status ($(if ($LASTEXITCODE -eq 0) { 'ok' } else { 'failed' })) -Detail $compose
        }
        else {
            Add-Check -Name 'docker' -Status 'failed' -Detail $dockerVersion
        }
    }
    catch {
        Add-Check -Name 'docker' -Status 'failed' -Detail $_.Exception.Message
    }
}
else {
    Add-Check -Name 'docker' -Status 'missing' -Detail 'docker command not found'
}

# DorsanDesk: observe only by default. Restart is limited to an existing Windows service.
$dorsanServices = Get-Service -ErrorAction SilentlyContinue | Where-Object {
    $_.Name -match 'dorsan' -or $_.DisplayName -match 'dorsan'
}
if (@($dorsanServices).Count -eq 0) {
    $processes = Get-Process -ErrorAction SilentlyContinue | Where-Object { $_.ProcessName -match 'dorsan' }
    Add-Check -Name 'dorsandesk' -Status ($(if (@($processes).Count -gt 0) { 'process-running' } else { 'not-detected' })) -Detail ((@($processes | Select-Object -ExpandProperty ProcessName) -join ','))
}
else {
    foreach ($service in $dorsanServices) {
        Add-Check -Name 'dorsandesk_service' -Status ([string]$service.Status) -Detail "$($service.Name) / $($service.DisplayName)"
        if ($AllowRestart -and $service.Status -ne 'Running') {
            Start-Service -Name $service.Name
            $service.WaitForStatus('Running', [TimeSpan]::FromSeconds(30))
            Add-Check -Name 'dorsandesk_recovery' -Status 'started' -Detail $service.Name
        }
    }
}

$reportPath = Join-Path $ReportDirectory ("watchdog-{0}.json" -f (Get-Date -Format 'yyyyMMdd-HHmmss'))
$report | ConvertTo-Json -Depth 6 | Set-Content -Path $reportPath -Encoding utf8
Write-Output "WATCHDOG_REPORT=$reportPath"
Write-Output ($report | ConvertTo-Json -Depth 6)
