export default defineNuxtConfig({
  compatibilityDate: '2026-08-01',
  devtools: { enabled: false },
  css: ['../../shared-ui/styles/tokens.css', '~/assets/main.css', '~/assets/contract-lists.css'],
  app: {
    head: {
      htmlAttrs: { lang: 'fa', dir: 'rtl' },
      titleTemplate: '%s | GoldPlatform',
      meta: [
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'color-scheme', content: 'light' }
      ]
    }
  },
  runtimeConfig: {
    public: {
      apiBase: '/api/v1/customer',
      brandName: 'GoldPlatform',
      brandTagline: 'مدیریت ساده و امن دارایی'
    }
  },
  typescript: {
    strict: true,
    typeCheck: true
  },
  nitro: {
    preset: 'node-server',
    externals: {
      inline: ['vue', '@vue/server-renderer']
    }
  }
})
