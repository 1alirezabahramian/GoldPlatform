import assert from 'node:assert/strict';
import test from 'node:test';
import path from 'node:path';
import { resolveProjectPath } from '../src/security/project-path.js';

const root = path.resolve('C:\\Users\\USER\\Desktop\\p\\GoldPlatform');

test('allows a repository-relative path inside the project root', () => {
  const resolved = resolveProjectPath(root, 'docs/00_PROJECT_MEMORY.md');
  assert.equal(resolved, path.resolve(root, 'docs/00_PROJECT_MEMORY.md'));
});

test('rejects path traversal', () => {
  assert.throws(
    () => resolveProjectPath(root, '../secret.txt'),
    /out-of-project access/i,
  );
});

test('rejects absolute paths', () => {
  assert.throws(
    () => resolveProjectPath(root, 'C:\\Windows\\System32\\drivers\\etc\\hosts'),
    /absolute paths/i,
  );
});

test('rejects environment secret files', () => {
  assert.throws(
    () => resolveProjectPath(root, '.env'),
    /secret environment files/i,
  );
});
