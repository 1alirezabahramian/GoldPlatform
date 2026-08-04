<script setup lang="ts">
const { get } = useAdminApi()
const { data, error, pending, refresh } = await useAsyncData('admin-dashboard', () => get<Record<string, unknown>>('/admin/dashboard'))
</script>

<template>
  <section>
    <header class="page-header">
      <div><h1>داشبورد مدیریت</h1><p>نمای عملیاتی بر پایه داده واقعی Backend</p></div>
      <button type="button" @click="refresh()">به‌روزرسانی</button>
    </header>
    <div v-if="pending" class="state">در حال دریافت اطلاعات…</div>
    <div v-else-if="error" class="state error">دریافت داشبورد ناموفق بود.</div>
    <pre v-else class="data-card">{{ JSON.stringify(data?.data, null, 2) }}</pre>
  </section>
</template>
