import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
import test from 'node:test'

const api = await readFile(new URL('../app/composables/useBackofficeApi.ts', import.meta.url), 'utf8')
const admin = await readFile(new URL('../app/pages/admin.vue', import.meta.url), 'utf8')
const operator = await readFile(new URL('../app/pages/operator.vue', import.meta.url), 'utf8')

test('backoffice reads disable cache and retry', () => {
  assert.match(api, /cache:\s*'no-store'/)
  assert.match(api, /retry:\s*0/)
})

test('foundation is read only', () => {
  assert.doesNotMatch(`${api}\n${admin}\n${operator}`, /method:\s*'(POST|PUT|PATCH|DELETE)'/)
})

test('accepted canonical endpoints are used', () => {
  for (const endpoint of ['/admin/audit-logs','/admin/outbox','/operator/orders/queue','/operator/deliveries/queue']) {
    assert.match(`${admin}\n${operator}`, new RegExp(endpoint.replaceAll('/', '\\/')))
  }
})

test('no direct Kimia or balance mutation is exposed', () => {
  assert.doesNotMatch(`${api}\n${admin}\n${operator}`, /94\.101\.|balance\s*[+\-*/=]|kimia\/|voucher/i)
})
