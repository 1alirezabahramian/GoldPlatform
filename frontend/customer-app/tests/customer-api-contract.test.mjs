import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
import test from 'node:test'

const apiClient = await readFile(new URL('../app/composables/useCustomerApi.ts', import.meta.url), 'utf8')
const foundationPage = await readFile(new URL('../app/pages/index.vue', import.meta.url), 'utf8')
const readPage = await readFile(new URL('../app/components/CustomerReadPage.vue', import.meta.url), 'utf8')
const appShell = await readFile(new URL('../app/app.vue', import.meta.url), 'utf8')

const corePages = await Promise.all([
  'dashboard.vue',
  'assets.vue',
  'orders.vue',
  'custody.vue',
  'profile.vue'
].map((name) => readFile(new URL(`../app/pages/${name}`, import.meta.url), 'utf8')))

test('customer API requests disable caching and automatic retries', () => {
  assert.match(apiClient, /cache:\s*'no-store'/)
  assert.match(apiClient, /retry:\s*0/)
})

test('frontend calls only configured customer API base', () => {
  assert.match(apiClient, /config\.public\.apiBase/)
  assert.doesNotMatch(apiClient, /94\.101\./)
})

test('foundation does not turn unavailable values into zero', () => {
  assert.match(foundationPage, /به‌عنوان صفر نمایش داده نمی‌شود/)
  assert.match(readPage, /به‌صورت صفر نمایش داده نمی‌شود/)
  assert.doesNotMatch(`${foundationPage}\n${readPage}`, /balance\s*:\s*0/i)
})

test('core customer pages use only accepted read endpoints', () => {
  const source = corePages.join('\n')
  for (const endpoint of ['/dashboard', '/assets', '/orders', '/custodies', '/profile']) {
    assert.match(source, new RegExp(`endpoint="${endpoint}"`))
  }
  assert.doesNotMatch(source, /POST|PUT|PATCH|DELETE/)
})

test('customer navigation exposes all core pages', () => {
  for (const route of ['/dashboard', '/assets', '/orders', '/custody', '/profile']) {
    assert.match(appShell, new RegExp(`to="${route}"`))
  }
})
