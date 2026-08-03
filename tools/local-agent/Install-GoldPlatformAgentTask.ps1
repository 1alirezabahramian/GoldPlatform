[CmdletBinding()]
param(
    [string]$TaskName = 'GoldPlatform Local Health Check',
    [string]$ProjectRoot = (Resolve-Path "$PSScriptRoot\..\..").Path,
    [ValidateRange(1, 24)][int]$EveryHours = 2
)

$ErrorActionPreference = 'Stop'
$runner = Join-Path $ProjectRoot 'tools\local-agent\Invoke-GoldPlatformHealthCheck.ps1'
if (-not (Test-Path $runner)) { throw "Runner not found: $runner" }

$pwsh = (Get-Command pwsh.exe -ErrorAction Stop).Source
$arguments = "-NoProfile -ExecutionPolicy Bypass -File `"$runner`" -ProjectRoot `"$ProjectRoot`""

$action = New-ScheduledTaskAction -Execute $pwsh -Argument $arguments -WorkingDirectory $ProjectRoot
$startupTrigger = New-ScheduledTaskTrigger -AtStartup
$repeatTrigger = New-ScheduledTaskTrigger -Once -At (Get-Date).AddMinutes(2) `
    -RepetitionInterval (New-TimeSpan -Hours $EveryHours) `
    -RepetitionDuration (New-TimeSpan -Days 3650)
$settings = New-ScheduledTaskSettingsSet `
    -StartWhenAvailable `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -ExecutionTimeLimit (New-TimeSpan -Hours 1)

$principal = New-ScheduledTaskPrincipal -UserId "$env:USERDOMAIN\$env:USERNAME" -LogonType Interactive -RunLevel Highest

Register-ScheduledTask `
    -TaskName $TaskName `
    -Action $action `
    -Trigger @($startupTrigger, $repeatTrigger) `
    -Settings $settings `
    -Principal $principal `
    -Description 'Runs safe Docker, Laravel and read-only Kimia health checks for GoldPlatform.' `
    -Force | Out-Null

Write-Host "Installed scheduled task: $TaskName"
Write-Host "Runs at Windows startup and every $EveryHours hour(s)."
Write-Host "Test now with: Start-ScheduledTask -TaskName '$TaskName'"
