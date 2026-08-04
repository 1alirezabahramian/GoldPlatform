<script setup lang="ts">
const { get } = useAdminApi()
const { data, error, pending, refresh } = await useAsyncData('operator-dashboard', () => get<Record<string, unknown>>('/operator/dashboard'))
</script>

<template>
  <section>
    <header class="page-header">
      <div><h1>داشبورد اپراتور</h1><p>صف کارهای روزانه و عملیات تحویل</p></div>
      <button type="button" @click="refresh()">به‌روزرسانی</button>
    </header>
    <div v-if="pending" class="state">در حال دریافت اطلاعات…</div>
    <div v-else-if="error" class="state error">دریافت داشبورد ناموفق بود.</div>
    <pre v-else class="data-card">{{ JSON.stringify(data?.data, null, 2) }}</pre>
  </section>
</template>
