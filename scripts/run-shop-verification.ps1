[CmdletBinding()]
param(
    [ValidateRange(1, 2147483647)]
    [int]$AccountId = 350,

    [switch]$IncludeLiveKimia
)

Set-StrictMode -Version 2.0
$ErrorActionPreference = 'Stop'
$env:NO_COLOR = '1'

$ProjectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$ReportDirectory = Join-Path $ProjectRoot 'test-reports'
$Timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$ReportFile = Join-Path $ReportDirectory "shop-verification-$Timestamp.txt"
$ExpectedBranch = 'work/product-kimia-next'
$script:Failures = 0

New-Item -ItemType Directory -Path $ReportDirectory -Force | Out-Null
Set-Location $ProjectRoot

function Write-Report {
    param([string]$Message = '')

    Write-Host $Message
    Add-Content -LiteralPath $ReportFile -Value $Message -Encoding UTF8
}

function Invoke-ExternalStep {
    param(
        [string]$Title,
        [string]$Executable,
        [string[]]$Arguments
    )

    Write-Report ''
    Write-Report ("===== {0} =====" -f $Title)
    Write-Report ("Command: {0} {1}" -f $Executable, ($Arguments -join ' '))

    try {
        & $Executable @Arguments 2>&1 | ForEach-Object {
            Write-Report ([string]$_)
        }

        $ExitCode = $LASTEXITCODE
    }
    catch {
        Write-Report ("ERROR: {0}" -f $_.Exception.Message)
        $ExitCode = 1
    }

    Write-Report ("Exit code: {0}" -f $ExitCode)

    if ($ExitCode -ne 0) {
        $script:Failures++
        return $false
    }

    return $true
}

function Invoke-DockerArtisan {
    param(
        [string]$Title,
        [string[]]$ArtisanArguments
    )

    $Arguments = @(
        'compose',
        'exec',
        '-T',
        'php',
        'php',
        'artisan'
    ) + $ArtisanArguments + @('--no-ansi')

    return (Invoke-ExternalStep -Title $Title -Executable 'docker' -Arguments $Arguments)
}

Write-Report 'GoldPlatform shop verification report'
Write-Report ("Started: {0}" -f (Get-Date -Format 'yyyy-MM-dd HH:mm:ss zzz'))
Write-Report ("PowerShell: {0}" -f $PSVersionTable.PSVersion)
Write-Report ("Project: {0}" -f $ProjectRoot)
Write-Report ("Live Kimia reads requested: {0}" -f $IncludeLiveKimia.IsPresent)
Write-Report 'This script never runs Kimia POST, PUT, or DELETE requests.'

$CurrentBranch = (& git branch --show-current 2>&1 | Out-String).Trim()
$BranchExitCode = $LASTEXITCODE
Write-Report ("Current branch: {0}" -f $CurrentBranch)

if ($BranchExitCode -ne 0 -or $CurrentBranch -ne $ExpectedBranch) {
    Write-Report ("STOP: Expected branch {0}. No tests were run." -f $ExpectedBranch)
    Write-Report ("Report file: {0}" -f $ReportFile)
    exit 1
}

$TrackedChanges = (& git status --porcelain --untracked-files=no 2>&1 | Out-String).Trim()
$StatusExitCode = $LASTEXITCODE

if ($StatusExitCode -ne 0 -or $TrackedChanges -ne '') {
    Write-Report 'STOP: Tracked local changes exist. No tests were run.'
    Write-Report $TrackedChanges
    Write-Report ("Report file: {0}" -f $ReportFile)
    exit 1
}

$LocalGateOk = $true
$LocalGateOk = (Invoke-ExternalStep 'Git status' 'git' @('status', '--short', '--branch')) -and $LocalGateOk
$LocalGateOk = (Invoke-ExternalStep 'Git commit' 'git' @('rev-parse', 'HEAD')) -and $LocalGateOk
$LocalGateOk = (Invoke-ExternalStep 'Docker services' 'docker' @('compose', 'ps')) -and $LocalGateOk
$LocalGateOk = (Invoke-ExternalStep 'PHP version' 'docker' @('compose', 'exec', '-T', 'php', 'php', '-v')) -and $LocalGateOk
$LocalGateOk = (Invoke-DockerArtisan 'Laravel environment' @('about')) -and $LocalGateOk
$LocalGateOk = (Invoke-DockerArtisan 'Kimia write safety status' @('kimia:safety-status')) -and $LocalGateOk
$LocalGateOk = (Invoke-DockerArtisan 'Write safety tests' @('test', 'tests/Unit/Kimia/KimiaWriteSafetyGateTest.php')) -and $LocalGateOk
$LocalGateOk = (Invoke-DockerArtisan 'Safety command tests' @('test', 'tests/Feature/KimiaSafetyStatusCommandTest.php')) -and $LocalGateOk
$LocalGateOk = (Invoke-DockerArtisan 'Identity and binding tests' @('test', 'tests/Feature/UserIdentityConstraintsTest.php')) -and $LocalGateOk
$LocalGateOk = (Invoke-DockerArtisan 'Tenant foundation isolation tests' @('test', 'tests/Feature/TenantDomainResolutionTest.php')) -and $LocalGateOk
$LocalGateOk = (Invoke-DockerArtisan 'Balance repository tests' @('test', 'tests/Unit/Kimia/VoucherRepositoryTest.php')) -and $LocalGateOk
$LocalGateOk = (Invoke-DockerArtisan 'Balance command tests' @('test', 'tests/Feature/KimiaInspectBalanceCommandTest.php')) -and $LocalGateOk
$LocalGateOk = (Invoke-DockerArtisan 'Sync-state command tests' @('test', 'tests/Feature/KimiaInspectSyncStateCommandTest.php')) -and $LocalGateOk
$LocalGateOk = (Invoke-DockerArtisan 'Full automated suite' @('test')) -and $LocalGateOk
$LocalGateOk = (Invoke-DockerArtisan 'Migration status' @('migrate:status')) -and $LocalGateOk
$LocalGateOk = (Invoke-DockerArtisan 'Pending migration SQL preview only' @('migrate', '--pretend', '--force')) -and $LocalGateOk

if ($IncludeLiveKimia.IsPresent -and $LocalGateOk) {
    Write-Report ''
    Write-Report '===== LIVE KIMIA READ-ONLY PHASE ====='
    Write-Report 'Only GET requests and local projection updates are allowed in this phase.'

    Invoke-DockerArtisan 'Kimia connection' @('kimia:test') | Out-Null
    Invoke-DockerArtisan 'Kimia account groups sync (local projection)' @('kimia:sync-groups') | Out-Null
    Invoke-DockerArtisan 'Kimia retail accounts sync (local projection)' @('kimia:sync-accounts', '--type=3') | Out-Null
    Invoke-DockerArtisan 'Kimia coins sync (local projection)' @('kimia:sync-coins') | Out-Null
    Invoke-DockerArtisan 'Kimia currencies sync (local projection)' @('kimia:sync-currencies') | Out-Null
    Invoke-DockerArtisan 'Local projection verification' @('kimia:inspect-sync-state', ("--account={0}" -f $AccountId)) | Out-Null
    Invoke-DockerArtisan 'Kimia balance read' @('kimia:inspect-balance', [string]$AccountId) | Out-Null
}
elseif ($IncludeLiveKimia.IsPresent) {
    Write-Report ''
    Write-Report 'LIVE KIMIA PHASE SKIPPED: a local verification step failed.'
}
else {
    Write-Report ''
    Write-Report 'LIVE KIMIA PHASE SKIPPED: -IncludeLiveKimia was not supplied.'
}

Write-Report ''
Write-Report '===== FINAL RESULT ====='
Write-Report ("Failures: {0}" -f $script:Failures)
Write-Report ("Finished: {0}" -f (Get-Date -Format 'yyyy-MM-dd HH:mm:ss zzz'))
Write-Report ("Report file: {0}" -f $ReportFile)

if ($script:Failures -gt 0) {
    Write-Report 'RESULT: FAIL'
    exit 1
}

Write-Report 'RESULT: PASS'
exit 0
