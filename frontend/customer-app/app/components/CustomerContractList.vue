<script setup lang="ts">
import type { CustomerCollection, CustomerCustody, CustomerDelivery, CustomerOrder } from '~/types/customer-contracts'

type Kind = 'orders' | 'custodies' | 'deliveries'
type Item = CustomerOrder | CustomerCustody | CustomerDelivery

const props = defineProps<{
  kind: Kind
  title: string
  description: string
  endpoint: string
}>()

const { read } = useCustomerApi()
const status = ref<'loading' | 'ready' | 'empty' | 'unavailable' | 'error'>('loading')
const items = ref<Item[]>([])
const message = ref('')

const labels: Record<string, string> = {
  draft: 'پیش‌نویس', pending: 'در انتظار بررسی', approved: 'تأییدشده', ready: 'آماده تحویل',
  delivered: 'تحویل‌شده', rejected: 'ردشده', cancelled: 'لغوشده', expired: 'منقضی‌شده',
  completed: 'تکمیل‌شده', processing: 'در حال پردازش', requested: 'درخواست‌شده'
}

function statusLabel(value: string | null): string {
  if (!value) return 'وضعیت نامشخص'
  return labels[value] ?? value
}

function safeValue(value: string | null | undefined, suffix = ''): string {
  if (value === null || value === undefined || value === '') return 'ثبت نشده'
  return `${value}${suffix}`
}

function formatDate(value: string | null | undefined): string {
  if (!value) return 'ثبت نشده'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return new Intl.DateTimeFormat('fa-IR', { dateStyle: 'medium', timeStyle: 'short' }).format(date)
}

async function load() {
  status.value = 'loading'
  message.value = ''

  try {
    const response = await read<CustomerCollection<Item>>(props.endpoint)
    items.value = response.data.items
    status.value = items.value.length ? 'ready' : 'empty'
  } catch (error: unknown) {
    const statusCode = typeof error === 'object' && error !== null && 'statusCode' in error
      ? Number((error as { statusCode?: unknown }).statusCode)
      : null

    status.value = statusCode === 503 ? 'unavailable' : 'error'
    message.value = status.value === 'unavailable'
      ? 'اطلاعات فعلاً از منبع رسمی در دسترس نیست و به‌صورت صفر نمایش داده نمی‌شود.'
      : 'دریافت اطلاعات با خطا روبه‌رو شد.'
  }
}

onMounted(load)
</script>

<template>
  <section class="gp-page">
    <header class="gp-card gp-page-header">
      <div>
        <p class="gp-eyebrow">پنل مشتری</p>
        <h2>{{ title }}</h2>
        <p class="gp-page-description">{{ description }}</p>
      </div>
      <span class="gp-source-badge">داده رسمی</span>
    </header>

    <div v-if="status === 'loading'" class="gp-card gp-page-state" aria-live="polite">
      <span class="gp-state-icon gp-state-icon-loading" aria-hidden="true" />
      <div><h3>در حال دریافت اطلاعات</h3><p class="gp-state">کمی صبر کنید…</p></div>
    </div>

    <div v-else-if="status === 'empty'" class="gp-card gp-page-state" aria-live="polite">
      <span class="gp-state-icon" aria-hidden="true">—</span>
      <div><h3>موردی وجود ندارد</h3><p class="gp-state">در حال حاضر موردی برای نمایش ثبت نشده است.</p></div>
    </div>

    <div v-else-if="status === 'unavailable' || status === 'error'" class="gp-card gp-page-state" aria-live="assertive">
      <span class="gp-state-icon" :class="status === 'error' ? 'gp-state-icon-danger' : 'gp-state-icon-warning'" aria-hidden="true">!</span>
      <div>
        <h3>{{ status === 'error' ? 'خطا در دریافت اطلاعات' : 'اطلاعات موقتاً در دسترس نیست' }}</h3>
        <p class="gp-state">{{ message }}</p>
        <button class="gp-button gp-button-secondary" type="button" @click="load">تلاش دوباره</button>
      </div>
    </div>

    <div v-else class="gp-contract-grid" aria-live="polite">
      <article v-for="(item, index) in items" :key="('reference' in item && item.reference) || `${kind}-${index}`" class="gp-card gp-contract-card">
        <template v-if="kind === 'orders'">
          <div class="gp-contract-head">
            <div><p class="gp-eyebrow">{{ safeValue((item as CustomerOrder).asset_type) }}</p><h3>{{ safeValue((item as CustomerOrder).type) }}</h3></div>
            <span class="gp-status-badge">{{ statusLabel((item as CustomerOrder).status) }}</span>
          </div>
          <dl class="gp-data-list">
            <div><dt>مقدار</dt><dd>{{ safeValue((item as CustomerOrder).quantity, (item as CustomerOrder).unit ? ` ${(item as CustomerOrder).unit}` : '') }}</dd></div>
            <div><dt>ثبت سفارش</dt><dd>{{ formatDate((item as CustomerOrder).created_at) }}</dd></div>
            <div><dt>انقضا</dt><dd>{{ formatDate((item as CustomerOrder).expires_at) }}</dd></div>
          </dl>
          <p v-if="(item as CustomerOrder).status_reason" class="gp-inline-note">{{ (item as CustomerOrder).status_reason }}</p>
        </template>

        <template v-else-if="kind === 'custodies'">
          <div class="gp-contract-head">
            <div><p class="gp-eyebrow">امانت فیزیکی</p><h3>{{ safeValue((item as CustomerCustody).title) }}</h3></div>
            <span class="gp-status-badge">{{ statusLabel((item as CustomerCustody).status) }}</span>
          </div>
          <dl class="gp-data-list">
            <div><dt>نوع دارایی</dt><dd>{{ safeValue((item as CustomerCustody).asset_type) }}</dd></div>
            <div><dt>تعداد</dt><dd>{{ safeValue((item as CustomerCustody).quantity) }}</dd></div>
            <div><dt>وزن</dt><dd>{{ safeValue((item as CustomerCustody).weight) }}</dd></div>
            <div><dt>عیار</dt><dd>{{ safeValue((item as CustomerCustody).fineness) }}</dd></div>
            <div><dt>شعبه</dt><dd>{{ safeValue((item as CustomerCustody).branch_code) }}</dd></div>
          </dl>
        </template>

        <template v-else>
          <div class="gp-contract-head">
            <div><p class="gp-eyebrow">درخواست تحویل</p><h3>{{ safeValue((item as CustomerDelivery).reference) }}</h3></div>
            <span class="gp-status-badge">{{ statusLabel((item as CustomerDelivery).status) }}</span>
          </div>
          <dl class="gp-data-list">
            <div><dt>شعبه</dt><dd>{{ safeValue((item as CustomerDelivery).branch_code) }}</dd></div>
            <div><dt>زمان درخواستی</dt><dd>{{ formatDate((item as CustomerDelivery).requested_for) }}</dd></div>
            <div><dt>زمان ثبت</dt><dd>{{ formatDate((item as CustomerDelivery).created_at) }}</dd></div>
          </dl>
          <p v-if="(item as CustomerDelivery).status_reason" class="gp-inline-note">{{ (item as CustomerDelivery).status_reason }}</p>
        </template>
      </article>
    </div>
  </section>
</template>
