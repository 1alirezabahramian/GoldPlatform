[CmdletBinding()]
param(
    [string]$ProjectRoot = (Resolve-Path "$PSScriptRoot\..\..").Path,
    [string]$TaskName = 'GoldPlatform Remote Command Queue',
    [int]$EveryMinutes = 1
)

$ErrorActionPreference = 'Stop'

if ($EveryMinutes -lt 1) {
    throw 'EveryMinutes must be at least 1.'
}

$pwsh = (Get-Command pwsh -ErrorAction Stop).Source
$scriptPath = Join-Path $ProjectRoot 'tools\local-agent\Invoke-GoldPlatformRemoteQueue.ps1'
if (-not (Test-Path $scriptPath)) {
    throw "Remote queue script not found: $scriptPath"
}

$action = New-ScheduledTaskAction `
    -Execute $pwsh `
    -Argument "-NoProfile -ExecutionPolicy Bypass -File `"$scriptPath`" -ProjectRoot `"$ProjectRoot`""

$startupTrigger = New-ScheduledTaskTrigger -AtStartup
$repeatTrigger = New-ScheduledTaskTrigger -Once -At (Get-Date).AddMinutes(1) `
    -RepetitionInterval (New-TimeSpan -Minutes $EveryMinutes) `
    -RepetitionDuration ([TimeSpan]::MaxValue)

$settings = New-ScheduledTaskSettingsSet `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -StartWhenAvailable `
    -MultipleInstances IgnoreNew

$principal = New-ScheduledTaskPrincipal `
    -UserId "$env:USERDOMAIN\$env:USERNAME" `
    -LogonType Interactive `
    -RunLevel Limited

Register-ScheduledTask `
    -TaskName $TaskName `
    -Action $action `
    -Trigger @($startupTrigger, $repeatTrigger) `
    -Settings $settings `
    -Principal $principal `
    -Force | Out-Null

Write-Host "Installed scheduled task: $TaskName"
Write-Host "Checks GitHub every $EveryMinutes minute(s)."
Write-Host "Test now with: Start-ScheduledTask -TaskName '$TaskName'"
