<script setup lang="ts">
const { read } = useBackofficeApi()
const status = ref<'loading'|'ready'|'empty'|'error'>('loading')

onMounted(async () => {
  try {
    const [orders, deliveries] = await Promise.all([
      read<{ items?: unknown[] }>('/operator/orders/queue'),
      read<{ items?: unknown[] }>('/operator/deliveries/queue')
    ])
    const count = (orders.data.items?.length ?? 0) + (deliveries.data.items?.length ?? 0)
    status.value = count > 0 ? 'ready' : 'empty'
  } catch {
    status.value = 'error'
  }
})
</script>

<template>
  <section>
    <div class="bo-card"><h2>پنل اپراتور</h2><p>نمای فقط‌خواندنی صف سفارش و تحویل؛ اجرای عملیات حساس در این Foundation فعال نیست.</p></div>
    <div class="bo-grid">
      <article class="bo-card"><h3>مرز ایمنی</h3><p class="bo-warning">هیچ Kimia Write، تسویه یا تغییر مانده از Frontend انجام نمی‌شود.</p></article>
      <article class="bo-card"><h3>وضعیت صف‌ها</h3><p class="bo-state">{{ status === 'loading' ? 'در حال دریافت…' : status === 'ready' ? 'صف‌های عملیاتی دریافت شدند.' : status === 'empty' ? 'صفی برای نمایش نیست.' : 'دریافت صف‌ها ناموفق بود.' }}</p></article>
    </div>
  </section>
</template>
