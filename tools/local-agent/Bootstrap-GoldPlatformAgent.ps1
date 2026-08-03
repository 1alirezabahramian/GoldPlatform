[CmdletBinding()]
param(
    [string]$ProjectRoot = 'C:\Users\USER\Desktop\p\GoldPlatform',
    [string]$PinnedCommit = '2d1d4150cc6e55b789d7df583293a62a21fca194'
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

if (-not (Test-Path -LiteralPath $ProjectRoot)) {
    throw "GoldPlatform project was not found at: $ProjectRoot"
}

$remoteUrl = (& git -C $ProjectRoot remote get-url origin).Trim()
if ($LASTEXITCODE -ne 0) { throw 'Could not read Git origin.' }
$normalized = $remoteUrl.TrimEnd('/') -replace '\.git$', ''
if ($normalized -ne 'https://github.com/1alirezabahramian/GoldPlatform') {
    throw "Repository verification failed: $normalized"
}

$branch = (& git -C $ProjectRoot branch --show-current).Trim()
if ($LASTEXITCODE -ne 0) { throw 'Could not read current branch.' }
if ($branch -ne 'feature/local-agent-runner') {
    throw "Branch verification failed. Current branch: $branch"
}

$downloadUrl = "https://raw.githubusercontent.com/1alirezabahramian/GoldPlatform/$PinnedCommit/tools/local-agent/Invoke-GoldPlatformSelfUpdate.ps1"
$tempScript = Join-Path $env:TEMP "GoldPlatform-SelfUpdate-$PinnedCommit.ps1"

try {
    Invoke-WebRequest -UseBasicParsing -Uri $downloadUrl -OutFile $tempScript
    if (-not (Test-Path -LiteralPath $tempScript)) { throw 'Bootstrap download failed.' }

    $tokens = $null
    $errors = $null
    [void][System.Management.Automation.Language.Parser]::ParseFile($tempScript, [ref]$tokens, [ref]$errors)
    if ($errors.Count -gt 0) { throw "Downloaded script syntax error: $($errors[0].Message)" }

    $pwsh = (Get-Command pwsh -ErrorAction Stop).Source
    & $pwsh -NoProfile -NonInteractive -ExecutionPolicy Bypass -File $tempScript -ProjectRoot $ProjectRoot
    if ($LASTEXITCODE -ne 0) { throw "Self-update exited with code $LASTEXITCODE" }

    Write-Host 'BOOTSTRAP=PASSED'
    Write-Host 'The agent now supports safe remote self-update.'
}
finally {
    Remove-Item -LiteralPath $tempScript -Force -ErrorAction SilentlyContinue
}
