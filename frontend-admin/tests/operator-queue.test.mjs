import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'

const orders = readFileSync(new URL('../pages/operator/orders/queue.vue', import.meta.url), 'utf8')
const deliveries = readFileSync(new URL('../pages/operator/deliveries/queue.vue', import.meta.url), 'utf8')
const workspace = readFileSync(new URL('../components/OperatorQueueWorkspace.vue', import.meta.url), 'utf8')

assert.match(orders, /useOperatorQueue\('orders'\)/)
assert.match(deliveries, /useOperatorQueue\('deliveries'\)/)
assert.match(workspace, /جزئیات امن/)
assert.doesNotMatch(workspace, /receiver_identifier|metadata|external_asset_id/)
