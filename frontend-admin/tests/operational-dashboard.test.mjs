import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'

const admin = await readFile(new URL('../pages/admin/index.vue', import.meta.url), 'utf8')
const operator = await readFile(new URL('../pages/operator/index.vue', import.meta.url), 'utf8')
const client = await readFile(new URL('../composables/useOperationalDashboard.ts', import.meta.url), 'utf8')

assert.match(client, /\/api\/v1\/admin\/dashboard/)
assert.match(client, /\/api\/v1\/operator\/dashboard/)
assert.doesNotMatch(admin + operator, /revenue|profit|gold_value|kimia_reference/i)
