import path from 'node:path';

const DEFAULT_PROJECT_ROOT = 'C:\\Users\\USER\\Desktop\\p\\GoldPlatform';

export interface AppConfig {
  projectRoot: string;
  port: number;
  host: string;
  auditLogRelativePath: string;
}

export function loadConfig(env: NodeJS.ProcessEnv = process.env): AppConfig {
  const projectRoot = path.resolve(env.GOLDPLATFORM_PROJECT_ROOT ?? DEFAULT_PROJECT_ROOT);
  const port = Number.parseInt(env.GOLDPLATFORM_MCP_PORT ?? '8787', 10);

  if (!Number.isInteger(port) || port < 1 || port > 65535) {
    throw new Error('GOLDPLATFORM_MCP_PORT must be an integer between 1 and 65535.');
  }

  return {
    projectRoot,
    port,
    host: env.GOLDPLATFORM_MCP_HOST ?? '127.0.0.1',
    auditLogRelativePath: 'storage/agent-reports/mcp-audit.jsonl',
  };
}
