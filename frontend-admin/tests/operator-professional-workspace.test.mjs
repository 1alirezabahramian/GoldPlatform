import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
import test from 'node:test'

const page = await readFile(new URL('../app/pages/operator.vue', import.meta.url), 'utf8')
const contracts = await readFile(new URL('../app/types/operator-contracts.ts', import.meta.url), 'utf8')
const styles = await readFile(new URL('../app/assets/main.css', import.meta.url), 'utf8')

test('operator workspace uses accepted queue endpoints', () => {
  assert.match(page, /\/operator\/orders\/queue/)
  assert.match(page, /\/operator\/deliveries\/queue/)
})

test('operator financial decimals remain strings', () => {
  assert.match(contracts, /asset_quantity: string \| null/)
  assert.match(contracts, /gold_price: string \| null/)
  assert.match(contracts, /commission: string \| null/)
  assert.match(contracts, /total_price: string \| null/)
})

test('operator workspace preserves backend permission enforcement', () => {
  assert.match(page, /statusCode/)
  assert.match(page, /code === 403/)
  assert.match(page, /مجوز کافی ندارید/)
})

test('operator workspace does not implement financial or Kimia writes', () => {
  assert.doesNotMatch(page, /mutate\(/)
  assert.doesNotMatch(page, /\/kimia/i)
  assert.doesNotMatch(page, /wallet|ledger|voucher/i)
  assert.doesNotMatch(page, /\*\s*750|\/\s*750/)
})

test('operator workspace is responsive and reduced-motion aware', () => {
  assert.match(styles, /bo-contract-grid/)
  assert.match(styles, /@media \(max-width: 40rem\)/)
  assert.match(styles, /prefers-reduced-motion/)
})
