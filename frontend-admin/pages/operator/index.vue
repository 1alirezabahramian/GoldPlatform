<script setup lang="ts">
import type { OperatorOperationalDashboard } from '~/types/dashboard'

const dashboard = useOperationalDashboard()
const data = ref<OperatorOperationalDashboard | null>(null)
const loading = ref(true)
const error = ref<string | null>(null)

const load = async () => {
  loading.value = true
  error.value = null
  try {
    data.value = await dashboard.operator()
  } catch {
    error.value = 'دریافت داشبورد اپراتور ممکن نشد.'
  } finally {
    loading.value = false
  }
}

await load()

const summary = computed(() => data.value ? [
  { label: 'سفارش‌های جدید', value: data.value.summary.pending_orders },
  { label: 'سفارش‌های تأییدشده', value: data.value.summary.approved_orders },
  { label: 'درخواست‌های تحویل', value: data.value.summary.requested_deliveries },
  { label: 'آماده تحویل', value: data.value.summary.ready_deliveries },
] : [])
</script>

<template>
  <main class="dashboard-page">
    <header class="page-heading">
      <div><p class="eyebrow">عملیات</p><h1>صف کارهای امروز</h1></div>
      <button type="button" @click="load">به‌روزرسانی</button>
    </header>
    <DashboardState :loading="loading" :error="error" @refresh="load">
      <template v-if="data">
        <OperationalSummaryGrid :items="summary" />
        <div class="queue-grid">
          <OperationalQueueList title="صف سفارش‌ها" :items="data.queues.orders" />
          <OperationalQueueList title="صف تحویل‌ها" :items="data.queues.deliveries" />
        </div>
      </template>
    </DashboardState>
  </main>
</template>
