import test from 'node:test'
import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'

const admin = await readFile(new URL('../app/pages/admin.vue', import.meta.url), 'utf8')
const types = await readFile(new URL('../app/types/admin-contracts.ts', import.meta.url), 'utf8')
const config = await readFile(new URL('../nuxt.config.ts', import.meta.url), 'utf8')
const components = await readFile(new URL('../../shared-ui/styles/components.css', import.meta.url), 'utf8')

test('admin workspace uses only accepted read endpoints', () => {
  assert.match(admin, /\/admin\/audit-logs/)
  assert.match(admin, /\/admin\/outbox/)
  assert.doesNotMatch(admin, /\$fetch[\s\S]*method:\s*['"](?:POST|PUT|PATCH|DELETE)['"]/)
})

test('admin contracts match safe controller resources', () => {
  for (const field of ['actor_id', 'action', 'subject_type', 'subject_id', 'request_id', 'event_type', 'aggregate_type', 'attempts', 'processed_at']) {
    assert.match(types, new RegExp(`\\b${field}\\b`))
  }
})

test('shared component patterns are loaded and accessible', () => {
  assert.match(config, /shared-ui\/styles\/components\.css/)
  for (const pattern of ['gp-button', 'gp-badge', 'gp-state', 'gp-table', 'gp-data-list']) {
    assert.match(components, new RegExp(`\\.${pattern}`))
  }
  assert.match(components, /min-height:var\(--gp-touch-target\)/)
})

test('frontend contains no financial mutation or calculation', () => {
  assert.doesNotMatch(admin, /KimiaService|ActionMapper|Weight750|exchangegold|exchangecurrency/)
  assert.doesNotMatch(admin, /(?:money|gold|balance|amount|weight)\s*[*/+-]/i)
  assert.doesNotMatch(admin, /\?\?\s*0/)
})
