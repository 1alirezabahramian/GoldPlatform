<script setup lang="ts">
const config = useRuntimeConfig()
</script>

<template>
  <div class="bo-shell">
    <header class="bo-header">
      <div><p>سامانه عملیاتی</p><h1>{{ config.public.brandName }}</h1></div>
      <span>دسترسی توسط Backend کنترل می‌شود</span>
    </header>
    <nav class="bo-nav" aria-label="ناوبری مدیریت و اپراتور">
      <NuxtLink to="/admin">مدیریت</NuxtLink>
      <NuxtLink to="/operator">اپراتور</NuxtLink>
    </nav>
    <main class="bo-main"><NuxtPage /></main>
  </div>
</template>
