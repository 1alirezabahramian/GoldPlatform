<script setup lang="ts">
const { read } = useBackofficeApi()
const status = ref<'loading'|'ready'|'empty'|'error'>('loading')

onMounted(async () => {
  try {
    const [audit, outbox] = await Promise.all([
      read<{ items?: unknown[] }>('/admin/audit-logs'),
      read<{ items?: unknown[] }>('/admin/outbox')
    ])
    const count = (audit.data.items?.length ?? 0) + (outbox.data.items?.length ?? 0)
    status.value = count > 0 ? 'ready' : 'empty'
  } catch {
    status.value = 'error'
  }
})
</script>

<template>
  <section>
    <div class="bo-card"><h2>پنل مدیریت</h2><p>نمای فقط‌خواندنی Audit و Outbox؛ هیچ تغییر مانده یا عملیات مالی از این رابط انجام نمی‌شود.</p></div>
    <div class="bo-grid">
      <article class="bo-card"><h3>کنترل دسترسی</h3><p class="bo-warning">نمایش منو جایگزین مجوز Backend نیست.</p></article>
      <article class="bo-card"><h3>وضعیت داده</h3><p class="bo-state">{{ status === 'loading' ? 'در حال دریافت…' : status === 'ready' ? 'اطلاعات عملیاتی دریافت شد.' : status === 'empty' ? 'موردی برای نمایش نیست.' : 'دریافت اطلاعات ناموفق بود.' }}</p></article>
    </div>
  </section>
</template>
