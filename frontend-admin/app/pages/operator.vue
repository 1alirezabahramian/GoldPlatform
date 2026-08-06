<script setup lang="ts">
import type { OperatorDelivery, OperatorOrder } from '~/types/operator-contracts'

const { read } = useBackofficeApi()
const status = ref<'loading' | 'ready' | 'empty' | 'forbidden' | 'error'>('loading')
const orders = ref<OperatorOrder[]>([])
const deliveries = ref<OperatorDelivery[]>([])

const statusLabels: Record<string, string> = {
  pending: 'در انتظار بررسی', approved: 'تأییدشده', executing: 'در حال اجرا', settling: 'در حال تسویه',
  requested: 'درخواست‌شده', ready: 'آماده تحویل'
}

function label(value: string): string { return statusLabels[value] ?? value }
function text(value: string | number | null | undefined): string {
  return value === null || value === undefined || value === '' ? 'ثبت نشده' : String(value)
}
function date(value: string | null): string {
  if (!value) return 'ثبت نشده'
  const parsed = new Date(value)
  return Number.isNaN(parsed.getTime()) ? value : new Intl.DateTimeFormat('fa-IR', { dateStyle: 'medium', timeStyle: 'short' }).format(parsed)
}

async function load() {
  status.value = 'loading'
  try {
    const [orderResponse, deliveryResponse] = await Promise.all([
      read<OperatorOrder[]>('/operator/orders/queue'),
      read<OperatorDelivery[]>('/operator/deliveries/queue')
    ])
    orders.value = orderResponse.data
    deliveries.value = deliveryResponse.data
    status.value = orders.value.length || deliveries.value.length ? 'ready' : 'empty'
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
    <header class="bo-card bo-page-header">
      <div><p class="bo-eyebrow">مرکز عملیات</p><h2>پنل اپراتور</h2><p>صف سفارش‌ها و تحویل‌ها با داده واقعی Backend و کنترل مجوز سمت سرور.</p></div>
      <button class="bo-button" type="button" :disabled="status === 'loading'" @click="load">به‌روزرسانی</button>
    </header>

    <div v-if="status === 'loading'" class="bo-card bo-state-card" aria-live="polite">در حال دریافت صف‌های عملیاتی…</div>
    <div v-else-if="status === 'forbidden'" class="bo-card bo-state-card bo-danger" role="alert">برای مشاهده این بخش مجوز کافی ندارید.</div>
    <div v-else-if="status === 'error'" class="bo-card bo-state-card bo-danger" role="alert">دریافت صف‌ها ناموفق بود. عملیات حساسی اجرا نشد.</div>
    <div v-else-if="status === 'empty'" class="bo-card bo-state-card" aria-live="polite">در حال حاضر سفارش یا تحویل فعالی در صف نیست.</div>

    <template v-else>
      <section class="bo-section" aria-labelledby="orders-heading">
        <div class="bo-section-heading"><div><p class="bo-eyebrow">{{ orders.length }} مورد</p><h3 id="orders-heading">صف سفارش‌ها</h3></div></div>
        <div class="bo-contract-grid">
          <article v-for="order in orders" :key="order.id" class="bo-card bo-contract-card">
            <div class="bo-contract-head"><div><p class="bo-eyebrow">کاربر {{ order.user_id }}</p><h4>{{ text(order.type) }}</h4></div><span class="bo-badge">{{ label(order.status) }}</span></div>
            <dl class="bo-data-list">
              <div><dt>دارایی</dt><dd>{{ text(order.asset_type) }}</dd></div>
              <div><dt>مقدار</dt><dd>{{ text(order.asset_quantity) }} {{ text(order.asset_unit) }}</dd></div>
              <div><dt>ثبت</dt><dd>{{ date(order.created_at) }}</dd></div>
              <div><dt>انقضا</dt><dd>{{ date(order.expires_at) }}</dd></div>
            </dl>
          </article>
        </div>
      </section>

      <section class="bo-section" aria-labelledby="deliveries-heading">
        <div class="bo-section-heading"><div><p class="bo-eyebrow">{{ deliveries.length }} مورد</p><h3 id="deliveries-heading">صف تحویل‌ها</h3></div></div>
        <div class="bo-contract-grid">
          <article v-for="delivery in deliveries" :key="delivery.uuid" class="bo-card bo-contract-card">
            <div class="bo-contract-head"><div><p class="bo-eyebrow">کاربر {{ delivery.user_id }}</p><h4>درخواست تحویل</h4></div><span class="bo-badge">{{ label(delivery.status) }}</span></div>
            <dl class="bo-data-list">
              <div><dt>شعبه</dt><dd>{{ text(delivery.branch_code) }}</dd></div>
              <div><dt>زمان درخواستی</dt><dd>{{ date(delivery.requested_for) }}</dd></div>
              <div><dt>ثبت</dt><dd>{{ date(delivery.created_at) }}</dd></div>
            </dl>
          </article>
        </div>
      </section>
    </template>

    <aside class="bo-card bo-safety-note">این نما فقط اطلاعات واقعی صف را نمایش می‌دهد؛ تغییر مانده، Kimia Write و منطق مالی در Frontend انجام نمی‌شود.</aside>
  </section>
</template>
