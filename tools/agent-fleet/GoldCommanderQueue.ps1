[CmdletBinding()]
param(
    [string]$ManifestPath = "C:\GoldPlatform-AgentFleet\tools\agent-fleet\agents.manifest.json",
    [string]$QueueRoot = "C:\ProgramData\GoldPlatform\AgentHost\Queue"
)

$ErrorActionPreference = "Stop"
Set-StrictMode -Version Latest

$incoming   = Join-Path $QueueRoot "Incoming"
$processing = Join-Path $QueueRoot "Processing"
$completed  = Join-Path $QueueRoot "Completed"
$failed     = Join-Path $QueueRoot "Failed"

foreach ($path in @($incoming,$processing,$completed,$failed)) {
    New-Item -ItemType Directory -Force -Path $path | Out-Null
}

if (-not (Test-Path $ManifestPath)) {
    throw "Manifest not found: $ManifestPath"
}

$manifest = Get-Content $ManifestPath -Raw | ConvertFrom-Json

Get-ChildItem $incoming -Filter "*.json" -File | ForEach-Object {
    $source = $_.FullName
    $work   = Join-Path $processing $_.Name

    try {
        Move-Item $source $work -Force
        $mission = Get-Content $work -Raw | ConvertFrom-Json

        if ([string]::IsNullOrWhiteSpace([string]$mission.id)) {
            throw "Mission id is required."
        }

        if ([string]$mission.type -ne "fleet-status") {
            throw "Rejected mission type: $($mission.type)"
        }

        $enabledAgents = @($manifest.agents | Where-Object enabled)

        $observer = Join-Path $PSScriptRoot "GoldObserver.ps1"

        if (Test-Path $observer) {
            $observerResult = & $observer -MissionFile $work | ConvertFrom-Json
        }
        else {
            throw "GoldObserver runtime not found."
        }

        $result = [ordered]@{
            id = [string]$mission.id
            type = [string]$mission.type
            status = "completed"
            handled_by = $observerResult.agent
            finished_at = Get-Date -Format o
            computer = $env:COMPUTERNAME
            fleet_name = $manifest.fleet_name
            enabled_agent_count = $enabledAgents.Count
            enabled_agents = @($enabledAgents.name)
        }

        $destination = Join-Path $completed $_.Name
        $result | ConvertTo-Json -Depth 8 | Set-Content $destination -Encoding utf8
        Remove-Item $work -Force

        Write-Output "MISSION_COMPLETED=$($mission.id)"
    }
    catch {
        $errorResult = [ordered]@{
            file = $_.Name
            status = "failed"
            failed_at = Get-Date -Format o
            error = $_.Exception.Message
        }

        $destination = Join-Path $failed $_.Name
        $errorResult | ConvertTo-Json -Depth 6 | Set-Content $destination -Encoding utf8
        Remove-Item $work -Force -ErrorAction SilentlyContinue

        Write-Output "MISSION_FAILED=$($_.Name)"
    }
}

