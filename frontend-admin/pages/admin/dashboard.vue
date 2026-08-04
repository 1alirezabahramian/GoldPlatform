<script setup lang="ts">
import type { AdminOperationalDashboard } from '~/types/dashboard'
const dashboard = useOperationalDashboard()
const data = ref<AdminOperationalDashboard | null>(null)
const loading = ref(true)
const error = ref<string | null>(null)
const load = async () => { loading.value = true; error.value = null; try { data.value = await dashboard.admin() } catch { error.value = 'دریافت داشبورد مدیریتی ممکن نشد.' } finally { loading.value = false } }
await load()
const summary = computed(() => data.value ? [
  { label: 'سفارش‌های باز', value: data.value.summary.open_orders },
  { label: 'تحویل‌های فعال', value: data.value.summary.active_deliveries },
  { label: 'تسویه‌های ناموفق', value: data.value.summary.failed_settlements },
  { label: 'امانات فعال', value: data.value.summary.custody_items },
  { label: 'پیام‌های معطل', value: data.value.summary.pending_outbox },
] : [])
</script>
<template><main class="dashboard-page"><header class="page-heading"><div><p>مدیریت</p><h1>داشبورد عملیاتی</h1></div><button type="button" @click="load">به‌روزرسانی</button></header><DashboardState :loading="loading" :error="error" @refresh="load"><template v-if="data"><OperationalSummaryGrid :items="summary"/><p v-if="!data.financial_metrics_supported" class="notice">شاخص‌های مالی تا زمان تأیید منبع حقیقت نمایش داده نمی‌شوند.</p><div class="queue-grid"><OperationalQueueList title="صف سفارش‌ها" :items="data.queues.orders"/><OperationalQueueList title="صف تحویل‌ها" :items="data.queues.deliveries"/></div></template></DashboardState></main></template>
