import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
import test from 'node:test'

const config = await readFile(new URL('../nuxt.config.ts', import.meta.url), 'utf8')
const css = await readFile(new URL('../app/assets/main.css', import.meta.url), 'utf8')

test('admin operator frontend loads the shared design tokens', () => {
  assert.match(config, /shared-ui\/styles\/tokens\.css/)
})

test('admin operator foundation uses semantic shared tokens', () => {
  assert.match(css, /var\(--gp-bg\)/)
  assert.match(css, /var\(--gp-warning-bg\)/)
  assert.match(css, /var\(--gp-touch-target\)/)
})

test('admin operator foundation preserves RTL at the Nuxt boundary', () => {
  assert.match(config, /dir:\s*'rtl'/)
  assert.match(config, /lang:\s*'fa'/)
})
