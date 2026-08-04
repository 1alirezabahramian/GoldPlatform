[CmdletBinding(SupportsShouldProcess)]
param(
    [string]$ProjectRoot = 'C:\Users\USER\Desktop\p\GoldPlatform',
    [string]$TaskName = 'GoldPlatform GoldRescue Heartbeat',
    [string]$InstallRoot = "$env:ProgramData\GoldPlatform\GoldRescue",
    [switch]$EnableRemoteRecovery
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

$source = Join-Path $ProjectRoot 'tools\agent-fleet\GoldRescueAgent.ps1'
if (-not (Test-Path -LiteralPath $source)) { throw "GoldRescue source missing: $source" }
$pwsh = (Get-Command pwsh -ErrorAction Stop).Source

New-Item -ItemType Directory -Force -Path $InstallRoot | Out-Null
New-Item -ItemType Directory -Force -Path (Join-Path $InstallRoot 'logs') | Out-Null
New-Item -ItemType Directory -Force -Path (Join-Path $InstallRoot 'backup') | Out-Null

$destination = Join-Path $InstallRoot 'GoldRescueAgent.ps1'
Copy-Item -LiteralPath $source -Destination $destination -Force

$tokens = $null; $errors = $null
[void][System.Management.Automation.Language.Parser]::ParseFile($destination,[ref]$tokens,[ref]$errors)
if ($errors.Count -gt 0) { throw "GoldRescue syntax validation failed: $($errors[0].Message)" }

$arguments = @(
    '-NoProfile','-NonInteractive','-ExecutionPolicy','Bypass',
    '-File',('"' + $destination + '"'),
    '-ProjectRoot',('"' + $ProjectRoot + '"'),
    '-Command','heartbeat'
)
if ($EnableRemoteRecovery) { $arguments += '-EnableRemoteRecovery' }

$action = New-ScheduledTaskAction -Execute $pwsh -Argument ($arguments -join ' ')
$triggerStartup = New-ScheduledTaskTrigger -AtStartup
$triggerRepeat = New-ScheduledTaskTrigger -Once -At (Get-Date).AddMinutes(1)
$triggerRepeat.Repetition.Interval = 'PT5M'
$triggerRepeat.Repetition.Duration = 'P1D'
$settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -MultipleInstances IgnoreNew

if ($PSCmdlet.ShouldProcess($TaskName,'Register GoldRescue heartbeat task')) {
    Register-ScheduledTask -TaskName $TaskName -Action $action -Trigger @($triggerStartup,$triggerRepeat) -Settings $settings -User $env:USERNAME -Force | Out-Null
}

& $pwsh -NoProfile -NonInteractive -ExecutionPolicy Bypass -File $destination -ProjectRoot $ProjectRoot -Command heartbeat
if ($LASTEXITCODE -ne 0) { throw 'GoldRescue heartbeat validation failed.' }

[ordered]@{
    installed = $true
    task = $TaskName
    install_root = $InstallRoot
    recovery_enabled = [bool]$EnableRemoteRecovery
} | ConvertTo-Json -Depth 4
