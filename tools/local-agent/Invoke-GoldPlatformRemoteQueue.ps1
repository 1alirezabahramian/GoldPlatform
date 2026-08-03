[CmdletBinding()]
param(
    [string]$ProjectRoot = (Resolve-Path "$PSScriptRoot\..\..").Path,
    [string]$Repository = '1alirezabahramian/GoldPlatform',
    [string]$AllowedAuthor = '1alirezabahramian'
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest
[Console]::OutputEncoding = [System.Text.UTF8Encoding]::new($false)
$OutputEncoding = [System.Text.UTF8Encoding]::new($false)
$env:NO_COLOR = '1'

$reportDir = Join-Path $ProjectRoot 'storage\agent-reports'
$lockPath = Join-Path $reportDir 'remote-queue.lock'
New-Item -ItemType Directory -Force -Path $reportDir | Out-Null
$lockStream = $null

try {
    $lockStream = [System.IO.File]::Open($lockPath, 'OpenOrCreate', 'ReadWrite', 'None')
}
catch {
    exit 0
}

function Invoke-CapturedCommand {
    param(
        [Parameter(Mandatory)][string]$FilePath,
        [Parameter(Mandatory)][string[]]$Arguments,
        [Parameter(Mandatory)][int]$TimeoutSeconds
    )

    $psi = [System.Diagnostics.ProcessStartInfo]::new()
    $psi.FileName = $FilePath
    $psi.WorkingDirectory = $ProjectRoot
    $psi.UseShellExecute = $false
    $psi.CreateNoWindow = $true
    $psi.RedirectStandardOutput = $true
    $psi.RedirectStandardError = $true
    $psi.StandardOutputEncoding = [System.Text.UTF8Encoding]::new($false)
    $psi.StandardErrorEncoding = [System.Text.UTF8Encoding]::new($false)

    foreach ($argument in $Arguments) {
        [void]$psi.ArgumentList.Add($argument)
    }

    $process = [System.Diagnostics.Process]::new()
    $process.StartInfo = $psi
    $started = Get-Date

    try {
        if (-not $process.Start()) {
            throw "Could not start process: $FilePath"
        }

        $stdoutTask = $process.StandardOutput.ReadToEndAsync()
        $stderrTask = $process.StandardError.ReadToEndAsync()
        $finished = $process.WaitForExit($TimeoutSeconds * 1000)

        if (-not $finished) {
            try { $process.Kill($true) } catch { }
            try { $process.WaitForExit(5000) | Out-Null } catch { }

            $stdout = $stdoutTask.GetAwaiter().GetResult()
            $stderr = $stderrTask.GetAwaiter().GetResult()

            return [pscustomobject]@{
                Output = (($stdout + "`n" + $stderr).Trim() + "`n[TIMEOUT] Process exceeded $TimeoutSeconds seconds.")
                ExitCode = 124
                TimedOut = $true
                DurationSeconds = [math]::Round(((Get-Date) - $started).TotalSeconds, 1)
            }
        }

        $stdout = $stdoutTask.GetAwaiter().GetResult()
        $stderr = $stderrTask.GetAwaiter().GetResult()

        return [pscustomobject]@{
            Output = ($stdout + "`n" + $stderr).Trim()
            ExitCode = $process.ExitCode
            TimedOut = $false
            DurationSeconds = [math]::Round(((Get-Date) - $started).TotalSeconds, 1)
        }
    }
    catch {
        return [pscustomobject]@{
            Output = ($_ | Out-String).Trim()
            ExitCode = 1
            TimedOut = $false
            DurationSeconds = [math]::Round(((Get-Date) - $started).TotalSeconds, 1)
        }
    }
    finally {
        $process.Dispose()
    }
}

function Invoke-AllowedCommand {
    param([Parameter(Mandatory)][string]$CommandName)

    $pwsh = (Get-Command pwsh -ErrorAction Stop).Source
    $healthScript = Join-Path $PSScriptRoot 'Invoke-GoldPlatformHealthCheck.ps1'
    $selfUpdateScript = Join-Path $PSScriptRoot 'Invoke-GoldPlatformSelfUpdate.ps1'

    switch ($CommandName) {
        'self-update' {
            return Invoke-CapturedCommand -FilePath $pwsh -Arguments @('-NoProfile','-NonInteractive','-ExecutionPolicy','Bypass','-File',$selfUpdateScript,'-ProjectRoot',$ProjectRoot) -TimeoutSeconds 300
        }
        'health-check' {
            return Invoke-CapturedCommand -FilePath $pwsh -Arguments @('-NoProfile','-NonInteractive','-ExecutionPolicy','Bypass','-File',$healthScript,'-ProjectRoot',$ProjectRoot) -TimeoutSeconds 1200
        }
        'tests' {
            $docker = (Get-Command docker -ErrorAction Stop).Source
            return Invoke-CapturedCommand -FilePath $docker -Arguments @('compose','exec','-T','php','php','artisan','test','--no-ansi') -TimeoutSeconds 900
        }
        'docker-status' {
            $docker = (Get-Command docker -ErrorAction Stop).Source
            return Invoke-CapturedCommand -FilePath $docker -Arguments @('compose','ps') -TimeoutSeconds 120
        }
        'git-status' {
            $git = (Get-Command git -ErrorAction Stop).Source
            return Invoke-CapturedCommand -FilePath $git -Arguments @('status','--short','--branch') -TimeoutSeconds 120
        }
        'kimia-readonly' {
            $escapedRoot = $ProjectRoot.Replace("'", "''")
            $command = "Set-Location -LiteralPath '$escapedRoot'; docker compose exec -T php php artisan kimia:test --no-ansi; if (`$LASTEXITCODE -eq 0) { docker compose exec -T php php artisan kimia:inspect-transactions 350 --page=0 --size=10 --no-ansi; exit `$LASTEXITCODE }; exit `$LASTEXITCODE"
            return Invoke-CapturedCommand -FilePath $pwsh -Arguments @('-NoProfile','-NonInteractive','-Command',$command) -TimeoutSeconds 300
        }
        'recent-logs' {
            $docker = (Get-Command docker -ErrorAction Stop).Source
            return Invoke-CapturedCommand -FilePath $docker -Arguments @('compose','exec','-T','php','sh','-lc','test -f storage/logs/laravel.log && tail -n 160 storage/logs/laravel.log || true') -TimeoutSeconds 120
        }
        default {
            return [pscustomobject]@{ Output = "Rejected command: $CommandName"; ExitCode = 64; TimedOut = $false; DurationSeconds = 0 }
        }
    }
}

function Add-IssueComment {
    param([int]$IssueNumber, [string]$Body)

    $tempFile = Join-Path $env:TEMP ("goldplatform-agent-comment-{0}-{1}.md" -f $IssueNumber, [guid]::NewGuid())

    try {
        [System.IO.File]::WriteAllText($tempFile, $Body, [System.Text.UTF8Encoding]::new($false))
        & gh issue comment $IssueNumber --repo $Repository --body-file $tempFile | Out-Null
        if ($LASTEXITCODE -ne 0) {
            throw "Could not comment on issue #$IssueNumber."
        }
    }
    finally {
        Remove-Item $tempFile -Force -ErrorAction SilentlyContinue
    }
}

function Close-AgentIssue {
    param([int]$IssueNumber, [bool]$Succeeded)

    $reason = if ($Succeeded) { 'completed' } else { 'not planned' }
    & gh issue close $IssueNumber --repo $Repository --reason $reason | Out-Null
    if ($LASTEXITCODE -ne 0) {
        throw "Could not close issue #$IssueNumber."
    }
}

try {
    if (-not (Get-Command gh -ErrorAction SilentlyContinue)) {
        throw 'GitHub CLI (gh) is not installed.'
    }

    & gh auth status --hostname github.com *> $null
    if ($LASTEXITCODE -ne 0) {
        throw 'GitHub CLI is not authenticated.'
    }

    $issuesJson = & gh api "repos/$Repository/issues?state=open&per_page=50"
    if ($LASTEXITCODE -ne 0) {
        throw 'Could not read GitHub issues.'
    }

    $issues = @($issuesJson | ConvertFrom-Json) | Where-Object {
        $isPullRequest = $_.PSObject.Properties.Name -contains 'pull_request'
        (-not $isPullRequest) -and
        ([string]$_.title).StartsWith('[AGENT]', [System.StringComparison]::OrdinalIgnoreCase) -and
        ([string]$_.user.login -eq $AllowedAuthor)
    } | Sort-Object number

    foreach ($issue in $issues) {
        $issueNumber = [int]$issue.number
        $commentsJson = & gh api "repos/$Repository/issues/$issueNumber/comments?per_page=100"
        if ($LASTEXITCODE -ne 0) { continue }

        $comments = @($commentsJson | ConvertFrom-Json)
        $finalComment = $comments | Where-Object {
            ([string]$_.body).StartsWith('## Agent result:', [System.StringComparison]::OrdinalIgnoreCase)
        } | Select-Object -Last 1

        if ($null -ne $finalComment) {
            Close-AgentIssue -IssueNumber $issueNumber -Succeeded (([string]$finalComment.body) -match '^## Agent result: PASSED')
            continue
        }

        $body = [string]$issue.body
        $match = [regex]::Match($body, '(?im)^COMMAND\s*=\s*([a-z0-9-]+)\s*$')

        if (-not $match.Success) {
            Add-IssueComment -IssueNumber $issueNumber -Body "## Agent result: FAILED`n`nMissing exact COMMAND=<allowed-command> line."
            Close-AgentIssue -IssueNumber $issueNumber -Succeeded $false
            continue
        }

        $commandName = $match.Groups[1].Value.ToLowerInvariant()
        $allowed = @('self-update','health-check','tests','docker-status','git-status','kimia-readonly','recent-logs')

        if ($commandName -notin $allowed) {
            Add-IssueComment -IssueNumber $issueNumber -Body "## Agent result: FAILED`n`nRejected command '$commandName'. Allowed: $($allowed -join ', ')"
            Close-AgentIssue -IssueNumber $issueNumber -Succeeded $false
            continue
        }

        $runId = [guid]::NewGuid().ToString('N')
        Add-IssueComment -IssueNumber $issueNumber -Body "[AGENT-CLAIM] run=$runId computer=$env:COMPUTERNAME command=$commandName started=$(Get-Date -Format o) timeout-protected=true"

        $result = Invoke-AllowedCommand -CommandName $commandName
        $status = if ($result.ExitCode -eq 0) { 'PASSED' } elseif ($result.TimedOut) { 'TIMED_OUT' } else { 'FAILED' }
        $text = [string]$result.Output

        if ([string]::IsNullOrWhiteSpace($text)) {
            $text = '[No process output]'
        }

        if ($text.Length -gt 45000) {
            $text = "[Output truncated to last 45000 characters]`n" + $text.Substring($text.Length - 45000)
        }

        $fence = [string]([char]96) * 3
        $comment = @(
            "## Agent result: $status",
            '',
            "- Run ID: $runId",
            "- Command: $commandName",
            "- Computer: $env:COMPUTERNAME",
            "- Finished: $(Get-Date -Format o)",
            "- Duration: $($result.DurationSeconds) seconds",
            "- Exit code: $($result.ExitCode)",
            "- Timed out: $($result.TimedOut)",
            '',
            ($fence + 'text'),
            $text,
            $fence
        ) -join "`n"

        Add-IssueComment -IssueNumber $issueNumber -Body $comment
        Close-AgentIssue -IssueNumber $issueNumber -Succeeded ($result.ExitCode -eq 0)
    }
}
catch {
    $fatalPath = Join-Path $reportDir ("remote-queue-fatal-{0}.log" -f (Get-Date -Format 'yyyyMMdd-HHmmss'))
    ($_ | Out-String) | Set-Content -Path $fatalPath -Encoding utf8
    throw
}
finally {
    if ($null -ne $lockStream) {
        $lockStream.Dispose()
    }

    Remove-Item $lockPath -Force -ErrorAction SilentlyContinue
}
