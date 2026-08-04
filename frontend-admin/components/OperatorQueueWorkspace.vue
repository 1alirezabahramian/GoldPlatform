<script setup lang="ts">
import type { QueueItem, QueuePage } from '~/types/operator-queue'

const props = defineProps<{
  title: string
  queue: QueuePage | null
  pending: boolean
  error: unknown
  status: string
  statuses: string[]
}>()
const emit = defineEmits<{ 'update:status': [value: string]; refresh: []; page: [value: number] }>()
const selected = ref<QueueItem | null>(null)
</script>

<template>
  <section class="page">
    <div class="page-heading">
      <div><p class="eyebrow">فضای کاری اپراتور</p><h1>{{ title }}</h1></div>
      <button class="button" type="button" @click="emit('refresh')">به‌روزرسانی</button>
    </div>

    <div class="card queue-toolbar">
      <label>وضعیت
        <select :value="status" @change="emit('update:status', ($event.target as HTMLSelectElement).value)">
          <option value="">همه وضعیت‌های فعال</option>
          <option v-for="item in statuses" :key="item" :value="item">{{ item }}</option>
        </select>
      </label>
    </div>

    <DashboardState v-if="pending" mode="loading" />
    <DashboardState v-else-if="error" mode="error" />
    <DashboardState v-else-if="!queue?.data.length" mode="empty" />

    <div v-else class="queue-layout">
      <div class="card queue-list">
        <button v-for="item in queue.data" :key="item.id" class="queue-row" type="button" @click="selected = item">
          <strong>#{{ item.id }}</strong>
          <span>{{ item.status }}</span>
          <small>{{ item.created_at ?? '—' }}</small>
        </button>
        <div class="pagination">
          <button :disabled="queue.current_page <= 1" @click="emit('page', queue.current_page - 1)">قبلی</button>
          <span>صفحه {{ queue.current_page }} از {{ queue.last_page }}</span>
          <button :disabled="queue.current_page >= queue.last_page" @click="emit('page', queue.current_page + 1)">بعدی</button>
        </div>
      </div>

      <aside class="card detail-panel">
        <template v-if="selected">
          <h2>جزئیات امن</h2>
          <dl>
            <dt>شناسه</dt><dd>{{ selected.id }}</dd>
            <dt>وضعیت</dt><dd>{{ selected.status }}</dd>
            <dt>نوع</dt><dd>{{ selected.type ?? selected.asset_type ?? '—' }}</dd>
            <dt>مقدار</dt><dd>{{ selected.quantity ?? '—' }} {{ selected.unit ?? '' }}</dd>
            <dt>شعبه</dt><dd>{{ selected.branch_code ?? '—' }}</dd>
            <dt>زمان درخواست</dt><dd>{{ selected.requested_for ?? selected.created_at ?? '—' }}</dd>
          </dl>
          <p class="muted">عملیات فقط زمانی نمایش داده می‌شود که Backend مجوز و Endpoint معتبر ارائه کند.</p>
        </template>
        <p v-else class="muted">برای مشاهده جزئیات، یک ردیف را انتخاب کنید.</p>
      </aside>
    </div>
  </section>
</template>
