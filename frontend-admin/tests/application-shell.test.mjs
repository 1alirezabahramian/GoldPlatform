import test from 'node:test'
import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'

const read = (path) => readFileSync(new URL(`../${path}`, import.meta.url), 'utf8')

test('route guard loads backend bootstrap and handles 401/403', () => {
  const middleware = read('middleware/backoffice.global.ts')
  assert.match(middleware, /load\(panel/)
  assert.match(middleware, /session-expired/)
  assert.match(middleware, /unauthorized/)
})

test('sidebar is generated from backend navigation', () => {
  const layout = read('layouts/default.vue')
  assert.match(layout, /session\.navigation/)
  assert.doesNotMatch(layout, /AccountId|VoucherId|TransactionCode|Kimia/)
})
