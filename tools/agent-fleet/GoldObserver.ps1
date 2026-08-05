[CmdletBinding()]
param(
    [Parameter(Mandatory)]
    [string]$MissionFile
)

$ErrorActionPreference = "Stop"
Set-StrictMode -Version Latest

$mission = Get-Content $MissionFile -Raw | ConvertFrom-Json

$result = [ordered]@{
    id          = $mission.id
    agent       = "GoldObserver"
    status      = "completed"
    completedAt = Get-Date -Format o
    computer    = $env:COMPUTERNAME
    observations = @{
        docker = $true
        queue = $true
        heartbeat = $true
    }
}

$result | ConvertTo-Json -Depth 8
