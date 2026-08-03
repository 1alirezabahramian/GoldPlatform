[CmdletBinding()]
param(
    [string]$ProjectRoot = (Resolve-Path "$PSScriptRoot\..\..").Path,
    [string]$Repository = '1alirezabahramian/GoldPlatform',
    [string]$AllowedAuthor = '1alirezabahramian'
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest
[Console]::OutputEncoding = [System.Text.UTF8Encoding]::new()
$OutputEncoding = [System.Text.UTF8Encoding]::new()

$lockPath = Join-Path $ProjectRoot 'storage\agent-reports\remote-queue.lock'
New-Item -ItemType Directory -Force -Path (Split-Path $lockPath) | Out-Null

try {
    $lockStream = [System.IO.File]::Open($lockPath, 'OpenOrCreate', 'ReadWrite', 'None')
}
catch {
    exit 0
}

function Invoke-NativeCapture {
    param([Parameter(Mandatory)][scriptblock]$Command)

    $output = & $Command 2>&1 | Out-String
    $exitCode = $LASTEXITCODE
    return [pscustomobject]@{ Output = $output.TrimEnd(); ExitCode = $exitCode }
}

function Invoke-AllowedCommand {
    param([Parameter(Mandatory)][string]$CommandName)

    Push-Location $ProjectRoot
    try {
        switch ($CommandName) {
            'health-check' {
                return Invoke-NativeCapture { pwsh -NoProfile -ExecutionPolicy Bypass -File "$PSScriptRoot\Invoke-GoldPlatformHealthCheck.ps1" }
            }
            'tests' {
                return Invoke-NativeCapture { docker compose exec -T php php artisan test --no-ansi }
            }
            'docker-status' {
                return Invoke-NativeCapture { docker compose ps }
            }
            'git-status' {
                return Invoke-NativeCapture { git status --short --branch }
            }
            'kimia-readonly' {
                return Invoke-NativeCapture {
                    docker compose exec -T php php artisan kimia:test
                    if ($LASTEXITCODE -eq 0) {
                        docker compose exec -T php php artisan kimia:inspect-transactions 350 --page=0 --size=10
                    }
                }
            }
            'recent-logs' {
                return Invoke-NativeCapture { docker compose exec -T php sh -lc 'test -f storage/logs/laravel.log && tail -n 160 storage/logs/laravel.log || true' }
            }
            default {
                return [pscustomobject]@{ Output = "Rejected command: $CommandName"; ExitCode = 64 }
            }
        }
    }
    finally {
        Pop-Location
    }
}

try {
    if (-not (Get-Command gh -ErrorAction SilentlyContinue)) {
        throw 'GitHub CLI (gh) is not installed.'
    }

    & gh auth status --hostname github.com *> $null
    if ($LASTEXITCODE -ne 0) {
        throw 'GitHub CLI is not authenticated. Run: gh auth login'
    }

    $issuesJson = & gh api "repos/$Repository/issues?state=open&per_page=50"
    if ($LASTEXITCODE -ne 0) { throw 'Could not read GitHub issues.' }

    $issues = $issuesJson | ConvertFrom-Json | Where-Object {
        -not $_.pull_request -and
        $_.title -like '[AGENT]*' -and
        $_.user.login -eq $AllowedAuthor
    } | Sort-Object number

    foreach ($issue in $issues) {
        $body = [string]$issue.body
        $match = [regex]::Match($body, '(?im)^COMMAND\s*=\s*([a-z0-9-]+)\s*$')
        if (-not $match.Success) {
            & gh issue comment $issue.number --repo $Repository --body "Agent rejected this request: missing exact COMMAND=<allowed-command> line."
            & gh issue close $issue.number --repo $Repository --reason 'not planned'
            continue
        }

        $commandName = $match.Groups[1].Value.ToLowerInvariant()
        $allowed = @('health-check', 'tests', 'docker-status', 'git-status', 'kimia-readonly', 'recent-logs')
        if ($commandName -notin $allowed) {
            & gh issue comment $issue.number --repo $Repository --body "Agent rejected command '$commandName'. Allowed: $($allowed -join ', ')"
            & gh issue close $issue.number --repo $Repository --reason 'not planned'
            continue
        }

        & gh issue comment $issue.number --repo $Repository --body "Agent on $env:COMPUTERNAME accepted '$commandName' at $(Get-Date -Format o)."

        $result = Invoke-AllowedCommand -CommandName $commandName
        $status = if ($result.ExitCode -eq 0) { 'PASSED' } else { 'FAILED' }
        $text = [string]$result.Output
        if ($text.Length -gt 45000) {
            $text = "[Output truncated to last 45000 characters]`n" + $text.Substring($text.Length - 45000)
        }

        $comment = @"
## Agent result: $status

- Command: `$commandName`
- Computer: `$env:COMPUTERNAME`
- Finished: `$(Get-Date -Format o)`
- Exit code: `$($result.ExitCode)`

```text
$text
```
"@
        & gh issue comment $issue.number --repo $Repository --body $comment

        if ($result.ExitCode -eq 0) {
            & gh issue close $issue.number --repo $Repository --reason 'completed'
        }
        else {
            & gh issue close $issue.number --repo $Repository --reason 'not planned'
        }
    }
}
finally {
    if ($null -ne $lockStream) { $lockStream.Dispose() }
    Remove-Item $lockPath -Force -ErrorAction SilentlyContinue
}
