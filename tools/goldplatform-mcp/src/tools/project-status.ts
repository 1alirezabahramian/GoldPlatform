import type { AppConfig } from '../config.js';
import { runProcess } from '../services/process-runner.js';

export interface ToolResult {
  [key: string]: unknown;
  ok: boolean;
  operation: string;
  summary: string;
  exitCode: number | null;
  durationMs: number;
  changedFiles: string[];
  warnings: string[];
  evidence: Record<string, unknown>;
  reportPath: string | null;
}

export async function getProjectStatus(config: AppConfig): Promise<ToolResult> {
  const startedAt = Date.now();

  const [branch, commit, worktree, docker, laravel] = await Promise.all([
    runProcess('git', ['branch', '--show-current'], config.projectRoot),
    runProcess('git', ['rev-parse', 'HEAD'], config.projectRoot),
    runProcess('git', ['status', '--short'], config.projectRoot),
    runProcess('docker', ['compose', 'ps', '--format', 'json'], config.projectRoot),
    runProcess('docker', ['compose', 'exec', '-T', 'php', 'php', 'artisan', 'about', '--only=environment'], config.projectRoot),
  ]);

  const checks = { branch, commit, worktree, docker, laravel };
  const failedChecks = Object.entries(checks)
    .filter(([, result]) => result.exitCode !== 0)
    .map(([name]) => name);

  return {
    ok: failedChecks.length === 0,
    operation: 'project_status',
    summary: failedChecks.length === 0
      ? 'وضعیت Git، Docker و Laravel با موفقیت خوانده شد.'
      : `برخی بررسی‌ها ناموفق بودند: ${failedChecks.join(', ')}`,
    exitCode: failedChecks.length === 0 ? 0 : 1,
    durationMs: Date.now() - startedAt,
    changedFiles: [],
    warnings: failedChecks,
    evidence: {
      branch: branch.stdout,
      commit: commit.stdout,
      worktree: worktree.stdout === '' ? 'clean' : worktree.stdout,
      docker: docker.stdout,
      laravel: laravel.stdout,
      errors: Object.fromEntries(
        Object.entries(checks)
          .filter(([, result]) => result.exitCode !== 0)
          .map(([name, result]) => [name, result.stderr]),
      ),
    },
    reportPath: null,
  };
}
