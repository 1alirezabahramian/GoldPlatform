import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
import test from 'node:test'

const apiClient = await readFile(new URL('../app/composables/useCustomerApi.ts', import.meta.url), 'utf8')
const page = await readFile(new URL('../app/pages/index.vue', import.meta.url), 'utf8')

test('customer API requests disable caching and automatic retries', () => {
  assert.match(apiClient, /cache:\s*'no-store'/)
  assert.match(apiClient, /retry:\s*0/)
})

test('frontend calls only configured customer API base', () => {
  assert.match(apiClient, /config\.public\.apiBase/)
  assert.doesNotMatch(apiClient, /kimia/i)
  assert.doesNotMatch(apiClient, /94\.101\./)
})

test('foundation does not turn unavailable values into zero', () => {
  assert.match(page, /به‌عنوان صفر نمایش داده نمی‌شود/)
  assert.doesNotMatch(page, /balance\s*:\s*0/i)
})
