<script setup lang="ts">
import type { AdminAuditLog, AdminOutboxMessage } from '~/types/admin-contracts'

const { read } = useBackofficeApi()
const status = ref<'loading' | 'ready' | 'empty' | 'forbidden' | 'error'>('loading')
const auditLogs = ref<AdminAuditLog[]>([])
const outboxMessages = ref<AdminOutboxMessage[]>([])

const processedCount = computed(() => outboxMessages.value.filter((message) => message.processed_at).length)
const pendingCount = computed(() => outboxMessages.value.length - processedCount.value)

function text(value: string | number | null | undefined): string {
  return value === null || value === undefined || value === '' ? 'ثبت نشده' : String(value)
}

function date(value: string | null): string {
  if (!value) return 'ثبت نشده'
  const parsed = new Date(value)
  return Number.isNaN(parsed.getTime())
    ? value
    : new Intl.DateTimeFormat('fa-IR', { dateStyle: 'medium', timeStyle: 'short' }).format(parsed)
}

function outboxState(message: AdminOutboxMessage): { label: string; className: string } {
  if (message.processed_at) return { label: 'پردازش‌شده', className: 'gp-badge gp-badge--success' }
  if (message.attempts > 0) return { label: 'نیازمند بررسی', className: 'gp-badge gp-badge--warning' }
  return { label: 'در انتظار', className: 'gp-badge' }
}

async function load() {
  status.value = 'loading'
  try {
    const [auditResponse, outboxResponse] = await Promise.all([
      read<AdminAuditLog[]>('/admin/audit-logs'),
      read<AdminOutboxMessage[]>('/admin/outbox')
    ])
    auditLogs.value = auditResponse.data
    outboxMessages.value = outboxResponse.data
    status.value = auditLogs.value.length || outboxMessages.value.length ? 'ready' : 'empty'
  } catch (error: unknown) {
    const code = typeof error === 'object' && error !== null && 'statusCode' in error
      ? Number((error as { statusCode?: unknown }).statusCode)
      : null
    status.value = code === 403 ? 'forbidden' : 'error'
  }
}

onMounted(load)
</script>

<template>
  <section class="bo-workspace">
    <header class="bo-card gp-page-header">
      <div>
        <p class="gp-eyebrow">مرکز نظارت</p>
        <h2 class="gp-title">پنل مدیریت</h2>
        <p class="gp-description">نمای فقط‌خواندنی رویدادهای Audit و پیام‌های Outbox با کنترل دسترسی کامل در Backend.</p>
      </div>
      <button class="gp-button" type="button" :disabled="status === 'loading'" @click="load">به‌روزرسانی</button>
    </header>

    <div v-if="status === 'loading'" class="gp-state" aria-live="polite">در حال دریافت اطلاعات نظارتی…</div>
    <div v-else-if="status === 'forbidden'" class="gp-state gp-state--danger" role="alert">برای مشاهده اطلاعات مدیریتی مجوز کافی ندارید.</div>
    <div v-else-if="status === 'error'" class="gp-state gp-state--danger" role="alert">دریافت اطلاعات ناموفق بود. هیچ عملیات یا تغییری اجرا نشد.</div>
    <div v-else-if="status === 'empty'" class="gp-state" aria-live="polite">در حال حاضر رویداد Audit یا پیام Outbox برای نمایش وجود ندارد.</div>

    <template v-else>
      <section class="gp-card-grid" aria-label="خلاصه نظارت">
        <article class="gp-metric"><span class="gp-eyebrow">Audit</span><strong>{{ auditLogs.length }}</strong><span>رویداد ثبت‌شده</span></article>
        <article class="gp-metric"><span class="gp-eyebrow">Outbox</span><strong>{{ outboxMessages.length }}</strong><span>پیام عملیاتی</span></article>
        <article class="gp-metric"><span class="gp-eyebrow">در انتظار</span><strong>{{ pendingCount }}</strong><span>پیام پردازش‌نشده</span></article>
        <article class="gp-metric"><span class="gp-eyebrow">پردازش‌شده</span><strong>{{ processedCount }}</strong><span>پیام تکمیل‌شده</span></article>
      </section>

      <section class="bo-section" aria-labelledby="audit-heading">
        <div class="bo-section-heading"><div><p class="gp-eyebrow">ردیابی عملیات</p><h3 id="audit-heading">آخرین رویدادهای Audit</h3></div></div>
        <div class="gp-table-wrap">
          <table class="gp-table">
            <thead><tr><th>عملیات</th><th>عامل</th><th>موضوع</th><th>Request ID</th><th>زمان</th></tr></thead>
            <tbody>
              <tr v-for="log in auditLogs" :key="log.id">
                <td><strong>{{ text(log.action) }}</strong></td>
                <td>{{ text(log.actor_id) }}</td>
                <td>{{ text(log.subject_type) }} / {{ text(log.subject_id) }}</td>
                <td><code>{{ text(log.request_id) }}</code></td>
                <td>{{ date(log.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <section class="bo-section" aria-labelledby="outbox-heading">
        <div class="bo-section-heading"><div><p class="gp-eyebrow">پیام‌های داخلی</p><h3 id="outbox-heading">وضعیت Outbox</h3></div></div>
        <div class="gp-table-wrap">
          <table class="gp-table">
            <thead><tr><th>رویداد</th><th>Aggregate</th><th>تلاش</th><th>وضعیت</th><th>آخرین تغییر</th></tr></thead>
            <tbody>
              <tr v-for="message in outboxMessages" :key="message.uuid">
                <td><strong>{{ text(message.event_type) }}</strong></td>
                <td>{{ text(message.aggregate_type) }} / {{ text(message.aggregate_id) }}</td>
                <td>{{ message.attempts }}</td>
                <td><span :class="outboxState(message).className">{{ outboxState(message).label }}</span></td>
                <td>{{ date(message.updated_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </template>

    <aside class="bo-card bo-safety-note">این رابط فقط برای مشاهده و پایش است. تغییر مستقیم مانده، تولید کد Kimia، اجرای تسویه و تغییر Rule مالی در Frontend مجاز نیست.</aside>
  </section>
</template>
