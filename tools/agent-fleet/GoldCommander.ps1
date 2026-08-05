[CmdletBinding()]
param(
    [string]$ManifestPath = "$PSScriptRoot\agents.manifest.json",
    [string]$StateDirectory = "$env:ProgramData\GoldPlatform\AgentHost\commander"
)

$ErrorActionPreference = "Stop"
Set-StrictMode -Version Latest

New-Item -ItemType Directory -Force -Path $StateDirectory | Out-Null

if (-not (Test-Path -LiteralPath $ManifestPath)) {
    throw "Manifest not found: $ManifestPath"
}

$manifest = Get-Content -LiteralPath $ManifestPath -Raw | ConvertFrom-Json
$enabledAgents = @($manifest.agents | Where-Object { $_.enabled -eq $true })

$heartbeat = [ordered]@{
    timestamp = (Get-Date -Format o)
    computer = $env:COMPUTERNAME
    commander = "GoldCommander"
    status = "healthy"
    fleet_name = $manifest.fleet_name
    schema_version = $manifest.schema_version
    default_policy = $manifest.default_policy
    global_kill_switch = [bool]$manifest.global_kill_switch
    enabled_agent_count = $enabledAgents.Count
    enabled_agents = @($enabledAgents | ForEach-Object { $_.name })
}

$heartbeatPath = Join-Path $StateDirectory "heartbeat.json"
$historyPath = Join-Path $StateDirectory ("heartbeat-{0}.json" -f (Get-Date -Format "yyyyMMdd-HHmmss"))

$json = $heartbeat | ConvertTo-Json -Depth 8
$json | Set-Content -LiteralPath $heartbeatPath -Encoding utf8
$json | Set-Content -LiteralPath $historyPath -Encoding utf8

Write-Output "GOLD_COMMANDER=HEALTHY"
Write-Output "HEARTBEAT=$heartbeatPath"
Write-Output "ENABLED_AGENTS=$($enabledAgents.Count)"

# --- Queue Runtime ---
$queueRuntime = Join-Path $PSScriptRoot "GoldCommanderQueue.ps1"
if (Test-Path $queueRuntime) {
    try {
        & $queueRuntime
    }
    catch {
        Write-Warning $_.Exception.Message
    }
}

