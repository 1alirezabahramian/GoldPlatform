<script setup lang="ts">
const config = useRuntimeConfig()
</script>

<template>
  <div class="gp-app-shell">
    <header class="gp-header">
      <div>
        <p class="gp-eyebrow">سامانه مدیریت دارایی مشتری</p>
        <h1>{{ config.public.brandName }}</h1>
      </div>
      <span class="gp-secure-label">ارتباط امن</span>
    </header>

    <main class="gp-main" aria-live="polite">
      <NuxtPage />
    </main>

    <nav class="gp-bottom-nav" aria-label="ناوبری اصلی">
      <NuxtLink to="/">خانه</NuxtLink>
      <span aria-disabled="true">دارایی‌های من</span>
      <span aria-disabled="true">سفارش‌های من</span>
      <span aria-disabled="true">حساب کاربری</span>
    </nav>
  </div>
</template>
