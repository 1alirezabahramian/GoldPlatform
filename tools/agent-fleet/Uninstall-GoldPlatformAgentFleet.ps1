[CmdletBinding(SupportsShouldProcess)]
param(
    [string]$TaskName = 'GoldPlatform Agent Fleet Watchdog',
    [string]$InstallRoot = "$env:ProgramData\GoldPlatform\AgentHost"
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

$task = Get-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue
if ($null -ne $task -and $PSCmdlet.ShouldProcess($TaskName, 'Unregister scheduled task')) {
    Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false
}

$backupRoot = Join-Path $InstallRoot 'backup'
$latestBackup = Get-ChildItem -LiteralPath $backupRoot -Directory -ErrorAction SilentlyContinue |
    Sort-Object Name -Descending |
    Select-Object -First 1

if ($null -ne $latestBackup) {
    $taskXml = Join-Path $latestBackup.FullName 'watchdog-task.xml'
    if (Test-Path -LiteralPath $taskXml) {
        $xml = Get-Content -LiteralPath $taskXml -Raw
        Register-ScheduledTask -TaskName $TaskName -Xml $xml -Force | Out-Null
        Write-Output "AGENT_FLEET_ROLLBACK=RESTORED_PREVIOUS_TASK"
        Write-Output "BACKUP=$($latestBackup.FullName)"
        exit 0
    }
}

Write-Output 'AGENT_FLEET_ROLLBACK=REMOVED'
Write-Output 'No previous scheduled-task backup was available.'
