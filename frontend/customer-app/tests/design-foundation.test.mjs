import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
import test from 'node:test'

const config = await readFile(new URL('../nuxt.config.ts', import.meta.url), 'utf8')
const css = await readFile(new URL('../app/assets/main.css', import.meta.url), 'utf8')

test('customer frontend loads the shared design tokens', () => {
  assert.match(config, /shared-ui\/styles\/tokens\.css/)
})

test('customer foundation uses semantic shared tokens', () => {
  assert.match(css, /var\(--gp-bg\)/)
  assert.match(css, /var\(--gp-brand-100\)/)
  assert.match(css, /var\(--gp-touch-target\)/)
})

test('customer foundation preserves RTL at the Nuxt boundary', () => {
  assert.match(config, /dir:\s*'rtl'/)
  assert.match(config, /lang:\s*'fa'/)
})
