import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
import test from 'node:test'

const component = await readFile(new URL('../app/components/CustomerContractList.vue', import.meta.url), 'utf8')
const contracts = await readFile(new URL('../app/types/customer-contracts.ts', import.meta.url), 'utf8')
const orders = await readFile(new URL('../app/pages/orders.vue', import.meta.url), 'utf8')
const custody = await readFile(new URL('../app/pages/custody.vue', import.meta.url), 'utf8')
const deliveries = await readFile(new URL('../app/pages/deliveries.vue', import.meta.url), 'utf8')

test('customer list contracts preserve decimal values as strings', () => {
  assert.match(contracts, /quantity: string \| null/)
  assert.match(contracts, /weight: string \| null/)
  assert.match(contracts, /fineness: string \| null/)
})

test('customer lists use only accepted endpoints', () => {
  assert.match(orders, /endpoint="\/orders"/)
  assert.match(custody, /endpoint="\/custodies"/)
  assert.match(deliveries, /endpoint="\/deliveries"/)
})

test('unavailable data is not converted to zero', () => {
  assert.match(component, /به‌صورت صفر نمایش داده نمی‌شود/)
  assert.doesNotMatch(component, /\?\?\s*0/)
  assert.doesNotMatch(component, /Number\(/)
})

test('customer lists do not calculate financial values', () => {
  assert.doesNotMatch(component, /kimia/i)
  assert.doesNotMatch(component, /wallet|ledger|voucher/i)
  assert.doesNotMatch(component, /\*\s*750|\/\s*750/)
})
