export default defineNuxtConfig({
  compatibilityDate: '2026-08-01',
  devtools: { enabled: false },
  css: ['~/assets/main.css'],
  app: {
    head: {
      htmlAttrs: { lang: 'fa', dir: 'rtl' },
      title: 'GoldPlatform | مدیریت و اپراتور',
      meta: [
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'color-scheme', content: 'light' }
      ]
    }
  },
  runtimeConfig: {
    public: {
      apiBase: '/api',
      brandName: 'GoldPlatform'
    }
  },
  typescript: { strict: true, typeCheck: true },
  nitro: {
    preset: 'node-server',
    externals: {
      inline: ['vue', '@vue/server-renderer']
    }
  }
})
