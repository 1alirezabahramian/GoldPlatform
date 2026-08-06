import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'
import test from 'node:test'

const app = await readFile(new URL('../app/app.vue', import.meta.url), 'utf8')
const readPage = await readFile(new URL('../app/components/CustomerReadPage.vue', import.meta.url), 'utf8')
const css = await readFile(new URL('../app/assets/main.css', import.meta.url), 'utf8')

test('customer shell exposes skip navigation and labelled primary navigation', () => {
  assert.match(app, /gp-skip-link/)
  assert.match(app, /href="#main-content"/)
  assert.match(app, /aria-label="ناوبری اصلی"/)
})

test('customer read states preserve unavailable data instead of showing zero', () => {
  assert.match(readPage, /به‌صورت صفر نمایش داده نمی‌شود/)
  assert.match(readPage, /state\.status === 'unavailable'/)
  assert.match(readPage, /تلاش دوباره/)
})

test('customer professional shell includes responsive and reduced-motion safeguards', () => {
  assert.match(css, /@media \(max-width: 40rem\)/)
  assert.match(css, /prefers-reduced-motion: reduce/)
  assert.match(css, /gp-state-icon-loading/)
})
