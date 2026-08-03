import { execFile } from 'node:child_process';
import { promisify } from 'node:util';

const execFileAsync = promisify(execFile);

export interface ProcessResult {
  command: string;
  args: string[];
  exitCode: number;
  stdout: string;
  stderr: string;
  durationMs: number;
}

export async function runProcess(
  command: string,
  args: string[],
  cwd: string,
  timeoutMs = 120_000,
): Promise<ProcessResult> {
  const startedAt = Date.now();

  try {
    const { stdout, stderr } = await execFileAsync(command, args, {
      cwd,
      timeout: timeoutMs,
      windowsHide: true,
      maxBuffer: 2 * 1024 * 1024,
      shell: false,
      encoding: 'utf8',
    });

    return {
      command,
      args,
      exitCode: 0,
      stdout: stdout.trim(),
      stderr: stderr.trim(),
      durationMs: Date.now() - startedAt,
    };
  } catch (error) {
    const failure = error as NodeJS.ErrnoException & {
      code?: number | string;
      stdout?: string;
      stderr?: string;
    };

    return {
      command,
      args,
      exitCode: typeof failure.code === 'number' ? failure.code : 1,
      stdout: String(failure.stdout ?? '').trim(),
      stderr: String(failure.stderr ?? failure.message).trim(),
      durationMs: Date.now() - startedAt,
    };
  }
}
