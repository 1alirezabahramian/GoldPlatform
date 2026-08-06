import test from 'node:test'
import assert from 'node:assert/strict'
import { readFile } from 'node:fs/promises'

const root = new URL('../', import.meta.url)
const read = (path) => readFile(new URL(path, root), 'utf8')

test('PWA manifest and safe-area metadata are wired', async () => {
  const [config, manifest] = await Promise.all([
    read('nuxt.config.ts'),
    read('public/manifest.webmanifest')
  ])
  assert.match(config, /viewport-fit=cover/)
  assert.match(config, /manifest\.webmanifest/)
  assert.match(config, /apple-mobile-web-app-capable/)
  assert.equal(JSON.parse(manifest).display, 'standalone')
})

test('service worker never caches API or financial responses', async () => {
  const worker = await read('public/sw.js')
  assert.match(worker, /url\.pathname\.startsWith\('\/api\/'\)/)
  assert.match(worker, /request\.method !== 'GET'/)
  assert.doesNotMatch(worker, /cache\.put\([^)]*api/i)
})

test('offline page states that balances and sensitive operations are unavailable', async () => {
  const offline = await read('public/offline.html')
  assert.match(offline, /اطلاعات مالی/)
  assert.match(offline, /عملیات حساس/)
  assert.match(offline, /Kimia/)
})
