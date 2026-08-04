<script setup lang="ts">
const { session } = useBackofficeSession()
</script>

<template>
  <section class="page">
    <div class="page-heading">
      <div><p class="eyebrow">مدیریت</p><h1>داشبورد مدیر</h1></div>
      <span class="status">نشست فعال</span>
    </div>
    <div class="card-grid">
      <article class="card"><h2>دسترسی‌های فعال</h2><strong>{{ session?.permissions.length ?? 0 }}</strong></article>
      <article class="card"><h2>بخش‌های قابل مشاهده</h2><strong>{{ session?.navigation.length ?? 0 }}</strong></article>
    </div>
    <article class="card"><h2>شروع کار</h2><p>بخش‌های مجاز از منوی کنار صفحه در دسترس هستند. تمام مجوزها از Backend دریافت می‌شوند.</p></article>
  </section>
</template>
