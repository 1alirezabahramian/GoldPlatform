[CmdletBinding()]
param(
    [string]$ProjectRoot = (Resolve-Path "$PSScriptRoot\..\..").Path,
    [string]$ExpectedRepository = 'https://github.com/1alirezabahramian/GoldPlatform.git',
    [string]$ExpectedBranch = 'feature/goldplatform-developer-mcp',
    [string]$TaskName = 'GoldPlatform Remote Command Queue'
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest
[Console]::OutputEncoding = [System.Text.UTF8Encoding]::new($false)
$OutputEncoding = [System.Text.UTF8Encoding]::new($false)

function Invoke-Git {
    param([Parameter(Mandatory)][string[]]$Arguments)
    & git -C $ProjectRoot @Arguments 2>&1
    if ($LASTEXITCODE -ne 0) {
        throw "git $($Arguments -join ' ') failed with exit code $LASTEXITCODE"
    }
}

$before = (& git -C $ProjectRoot rev-parse HEAD).Trim()
if ($LASTEXITCODE -ne 0) { throw 'Could not read current commit.' }

$origin = (& git -C $ProjectRoot remote get-url origin).Trim()
if ($LASTEXITCODE -ne 0) { throw 'Could not read origin URL.' }
$normalizedOrigin = $origin.TrimEnd('/') -replace '\.git$', ''
$normalizedExpected = $ExpectedRepository.TrimEnd('/') -replace '\.git$', ''
if ($normalizedOrigin -ne $normalizedExpected) {
    throw "Origin mismatch. Expected '$normalizedExpected', found '$normalizedOrigin'."
}

$branch = (& git -C $ProjectRoot branch --show-current).Trim()
if ($LASTEXITCODE -ne 0) { throw 'Could not read current branch.' }
if ($branch -ne $ExpectedBranch) {
    throw "Branch mismatch. Expected '$ExpectedBranch', found '$branch'."
}

$dirty = & git -C $ProjectRoot status --porcelain
if ($LASTEXITCODE -ne 0) { throw 'Could not inspect working tree.' }
if (-not [string]::IsNullOrWhiteSpace(($dirty | Out-String))) {
    throw 'Self-update refused: working tree has local changes.'
}

Invoke-Git -Arguments @('fetch', '--prune', 'origin', $ExpectedBranch)
$remoteHead = (& git -C $ProjectRoot rev-parse "origin/$ExpectedBranch").Trim()
if ($LASTEXITCODE -ne 0) { throw 'Could not resolve remote branch.' }

if ($before -eq $remoteHead) {
    Write-Output "SELF_UPDATE=NO_CHANGES"
    Write-Output "COMMIT=$before"
    exit 0
}

$mergeBase = (& git -C $ProjectRoot merge-base $before $remoteHead).Trim()
if ($LASTEXITCODE -ne 0) { throw 'Could not verify update ancestry.' }
if ($mergeBase -ne $before) {
    throw 'Self-update refused: remote update is not a fast-forward.'
}

try {
    Invoke-Git -Arguments @('merge', '--ff-only', "origin/$ExpectedBranch")

    $queueScript = Join-Path $ProjectRoot 'tools\local-agent\Invoke-GoldPlatformRemoteQueue.ps1'
    $healthScript = Join-Path $ProjectRoot 'tools\local-agent\Invoke-GoldPlatformHealthCheck.ps1'
    foreach ($script in @($queueScript, $healthScript)) {
        $tokens = $null
        $errors = $null
        [void][System.Management.Automation.Language.Parser]::ParseFile($script, [ref]$tokens, [ref]$errors)
        if ($errors.Count -gt 0) {
            throw "PowerShell syntax validation failed for $script :: $($errors[0].Message)"
        }
    }

    $after = (& git -C $ProjectRoot rev-parse HEAD).Trim()
    Write-Output "SELF_UPDATE=UPDATED"
    Write-Output "FROM=$before"
    Write-Output "TO=$after"
    Write-Output "BRANCH=$ExpectedBranch"

    if (Get-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue) {
        Write-Output "TASK=$TaskName"
        Write-Output 'TASK_STATUS=INSTALLED'
    }
}
catch {
    & git -C $ProjectRoot reset --hard $before 2>&1 | Out-Null
    throw "Self-update failed and was rolled back to $before. $($_.Exception.Message)"
}
