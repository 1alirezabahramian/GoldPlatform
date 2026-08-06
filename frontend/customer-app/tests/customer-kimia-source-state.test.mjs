import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
import test from 'node:test'

const dashboard = await readFile(new URL('../app/pages/dashboard.vue', import.meta.url), 'utf8')
const assets = await readFile(new URL('../app/pages/assets.vue', import.meta.url), 'utf8')

const source = `${dashboard}\n${assets}`

test('dashboard and assets identify Kimia as the financial balance source', () => {
  assert.match(source, /Kimia/)
  assert.match(source, /پول، طلا، سکه و ارز/)
})

test('financial source state does not invent balances or substitute zero', () => {
  assert.match(source, /هیچ مانده، ارزش یا عدد جایگزینی نمایش داده نمی‌شود/)
  assert.doesNotMatch(source, /\?\?\s*0/)
  assert.doesNotMatch(source, /balance\s*:\s*['\"]?0/)
})

test('dashboard and assets continue to call accepted read endpoints', () => {
  assert.match(dashboard, /endpoint="\/dashboard"/)
  assert.match(assets, /endpoint="\/assets"/)
})

test('frontend does not calculate financial values', () => {
  assert.doesNotMatch(source, /\*\s*750|\/\s*750/)
  assert.doesNotMatch(source, /rial|toman/i)
  assert.doesNotMatch(source, /wallet|ledger|voucher/i)
})
