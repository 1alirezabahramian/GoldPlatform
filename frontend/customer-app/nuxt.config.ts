export default defineNuxtConfig({
  compatibilityDate: '2026-08-01',
  devtools: { enabled: false },
  css: ['~/assets/main.css'],
  app: {
    head: {
      htmlAttrs: { lang: 'fa', dir: 'rtl' },
      titleTemplate: (titleChunk) => titleChunk ? `${titleChunk} | GoldPlatform` : 'GoldPlatform',
      meta: [
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'color-scheme', content: 'light' }
      ]
    }
  },
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE ?? '/api/v1/customer',
      brandName: process.env.NUXT_PUBLIC_BRAND_NAME ?? 'GoldPlatform',
      brandTagline: process.env.NUXT_PUBLIC_BRAND_TAGLINE ?? 'مدیریت ساده و امن دارایی'
    }
  },
  typescript: {
    strict: true,
    typeCheck: true
  },
  nitro: { preset: 'node-server' }
})
