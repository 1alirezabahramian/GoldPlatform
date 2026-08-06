<script setup lang="ts">
const config = useRuntimeConfig()
</script>

<template>
  <div class="gp-app-shell">
    <a class="gp-skip-link" href="#main-content">رفتن به محتوای اصلی</a>

    <header class="gp-header">
      <div class="gp-brand-lockup">
        <span class="gp-brand-mark" aria-hidden="true">G</span>
        <div>
          <p class="gp-eyebrow">{{ config.public.brandTagline }}</p>
          <h1>{{ config.public.brandName }}</h1>
        </div>
      </div>

      <div class="gp-header-actions">
        <span class="gp-secure-label">
          <span class="gp-status-dot" aria-hidden="true" />
          ارتباط امن
        </span>
      </div>
    </header>

    <main id="main-content" class="gp-main" aria-live="polite">
      <NuxtPage />
    </main>

    <nav class="gp-bottom-nav" aria-label="ناوبری اصلی">
      <NuxtLink to="/dashboard">
        <span class="gp-nav-icon" aria-hidden="true">⌂</span>
        <span>خانه</span>
      </NuxtLink>
      <NuxtLink to="/assets">
        <span class="gp-nav-icon" aria-hidden="true">◫</span>
        <span>دارایی‌ها</span>
      </NuxtLink>
      <NuxtLink to="/orders">
        <span class="gp-nav-icon" aria-hidden="true">≡</span>
        <span>سفارش‌ها</span>
      </NuxtLink>
      <NuxtLink to="/custody">
        <span class="gp-nav-icon" aria-hidden="true">◇</span>
        <span>امانات</span>
      </NuxtLink>
      <NuxtLink to="/profile">
        <span class="gp-nav-icon" aria-hidden="true">○</span>
        <span>حساب من</span>
      </NuxtLink>
    </nav>
  </div>
</template>
