import path from 'node:path';

const BLOCKED_BASENAMES = new Set(['.env', '.env.local', '.env.production', '.env.testing']);

export function resolveProjectPath(projectRoot: string, relativePath: string): string {
  if (relativePath.trim() === '') {
    throw new Error('A repository-relative path is required.');
  }

  if (path.isAbsolute(relativePath)) {
    throw new Error('Absolute paths are not allowed.');
  }

  const normalizedRoot = path.resolve(projectRoot);
  const resolved = path.resolve(normalizedRoot, relativePath);
  const rootWithSeparator = normalizedRoot.endsWith(path.sep)
    ? normalizedRoot
    : `${normalizedRoot}${path.sep}`;

  if (resolved !== normalizedRoot && !resolved.startsWith(rootWithSeparator)) {
    throw new Error('Path traversal or out-of-project access is not allowed.');
  }

  const basename = path.basename(resolved).toLowerCase();
  if (BLOCKED_BASENAMES.has(basename) || basename.startsWith('.env.')) {
    throw new Error('Secret environment files are not accessible.');
  }

  return resolved;
}
