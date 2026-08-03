[CmdletBinding()]
param(
    [string]$ProjectRoot = (Resolve-Path "$PSScriptRoot\..\..").Path,
    [switch]$IncludeKimiaSync,
    [switch]$OpenReport
)

$ErrorActionPreference = 'Continue'
Set-StrictMode -Version Latest

[Console]::InputEncoding = [System.Text.UTF8Encoding]::new($false)
[Console]::OutputEncoding = [System.Text.UTF8Encoding]::new($false)
$OutputEncoding = [System.Text.UTF8Encoding]::new($false)
$env:NO_COLOR = '1'
if ($PSStyle) { $PSStyle.OutputRendering = 'PlainText' }

$timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$reportDir = Join-Path $ProjectRoot 'storage\agent-reports'
$reportPath = Join-Path $reportDir "health-$timestamp.log"
New-Item -ItemType Directory -Force -Path $reportDir | Out-Null

$script:Failed = $false

function Write-ReportLine {
    param([string]$Text = '')
    $Text | Tee-Object -FilePath $reportPath -Append
}

function Invoke-Check {
    param(
        [Parameter(Mandatory)][string]$Name,
        [Parameter(Mandatory)][scriptblock]$Command,
        [switch]$AllowFailure,
        [switch]$Informational
    )

    Write-ReportLine ""
    Write-ReportLine "===== $Name ====="
    try {
        & $Command 2>&1 | ForEach-Object { Write-ReportLine ($_ | Out-String).TrimEnd() }
        if ($LASTEXITCODE -ne 0) {
            throw "Exit code: $LASTEXITCODE"
        }
        $status = if ($Informational) { 'INFO' } else { 'PASS' }
        Write-ReportLine "[$status] $Name"
    }
    catch {
        Write-ReportLine "[FAIL] $Name :: $($_.Exception.Message)"
        if (-not $AllowFailure) { $script:Failed = $true }
    }
}

Push-Location $ProjectRoot
try {
    Write-ReportLine "GoldPlatform Local Agent Health Check"
    Write-ReportLine "Started: $(Get-Date -Format o)"
    Write-ReportLine "Project: $ProjectRoot"
    Write-ReportLine "Computer: $env:COMPUTERNAME"
    Write-ReportLine "User: $env:USERNAME"

    Invoke-Check -Name 'Git status' -Command { git status --short --branch }
    Invoke-Check -Name 'Docker availability' -Command { docker info --format '{{.ServerVersion}}' }
    Invoke-Check -Name 'Start containers' -Command { docker compose up -d }
    Invoke-Check -Name 'Compose status' -Command { docker compose ps }
    Invoke-Check -Name 'Laravel about' -Command { docker compose exec -T php php artisan about --no-ansi }
    Invoke-Check -Name 'Migration status' -Command { docker compose exec -T php php artisan migrate:status --no-ansi }
    Invoke-Check -Name 'Laravel test suite' -Command { docker compose exec -T php php artisan test --no-ansi }
    Invoke-Check -Name 'Kimia connection (read-only)' -Command { docker compose exec -T php php artisan kimia:test --no-ansi }
    Invoke-Check -Name 'Kimia transaction inspection for account 350 (read-only)' -Command {
        docker compose exec -T php php artisan kimia:inspect-transactions 350 --page=0 --size=10 --no-ansi
    }

    if ($IncludeKimiaSync) {
        Invoke-Check -Name 'Kimia sync groups' -Command { docker compose exec -T php php artisan kimia:sync-groups --no-ansi }
        Invoke-Check -Name 'Kimia sync coins' -Command { docker compose exec -T php php artisan kimia:sync-coins --no-ansi }
        Invoke-Check -Name 'Kimia sync currencies' -Command { docker compose exec -T php php artisan kimia:sync-currencies --no-ansi }
        Invoke-Check -Name 'Kimia sync accounts type 3' -Command { docker compose exec -T php php artisan kimia:sync-accounts --type=3 --no-ansi }
    }

    Invoke-Check -Name 'Recent Laravel log snapshot (historical entries may be included)' -AllowFailure -Informational -Command {
        docker compose exec -T php sh -lc 'test -f storage/logs/laravel.log && tail -n 120 storage/logs/laravel.log || true'
    }

    Write-ReportLine ""
    Write-ReportLine "Finished: $(Get-Date -Format o)"
    $finalResult = if ($script:Failed) { 'RESULT=FAILED' } else { 'RESULT=PASSED' }
    Write-ReportLine $finalResult
}
finally {
    Pop-Location
}

Write-Host "Report: $reportPath"
if ($OpenReport) { Invoke-Item $reportPath }
if ($script:Failed) { exit 1 }
exit 0
