export default defineNuxtConfig({
  compatibilityDate: '2026-08-01',
  devtools: { enabled: false },
  css: ['../../shared-ui/styles/tokens.css', '../../shared-ui/styles/components.css', '~/assets/main.css', '~/assets/contract-lists.css'],
  app: {
    head: {
      htmlAttrs: { lang: 'fa', dir: 'rtl' },
      titleTemplate: '%s | GoldPlatform',
      link: [
        { rel: 'manifest', href: '/manifest.webmanifest' },
        { rel: 'icon', href: '/icons/goldplatform.svg', type: 'image/svg+xml' },
        { rel: 'apple-touch-icon', href: '/icons/goldplatform.svg' }
      ],
      meta: [
        { name: 'viewport', content: 'width=device-width, initial-scale=1, viewport-fit=cover' },
        { name: 'color-scheme', content: 'light' },
        { name: 'theme-color', content: '#5f4a14' },
        { name: 'mobile-web-app-capable', content: 'yes' },
        { name: 'apple-mobile-web-app-capable', content: 'yes' },
        { name: 'apple-mobile-web-app-status-bar-style', content: 'default' },
        { name: 'apple-mobile-web-app-title', content: 'GoldPlatform' }
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
