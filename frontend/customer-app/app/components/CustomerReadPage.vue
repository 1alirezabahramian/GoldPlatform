<script setup lang="ts">
import type { CustomerApiState, CustomerEnvelope } from '~/composables/useCustomerApi'

const props = defineProps<{
  title: string
  description: string
  endpoint: string
}>()

const { read } = useCustomerApi()
const state = ref<CustomerApiState<unknown>>({ status: 'loading', data: null, message: null })

const load = async () => {
  state.value = { status: 'loading', data: null, message: null }

  try {
    const response: CustomerEnvelope<unknown> = await read<unknown>(props.endpoint)
    const data = response.data

    if (Array.isArray(data) && data.length === 0) {
      state.value = { status: 'empty', data: null, message: 'در حال حاضر موردی برای نمایش وجود ندارد.' }
      return
    }

    if (data && typeof data === 'object' && 'items' in data && Array.isArray((data as { items?: unknown[] }).items) && (data as { items: unknown[] }).items.length === 0) {
      state.value = { status: 'empty', data: null, message: 'در حال حاضر موردی برای نمایش وجود ندارد.' }
      return
    }

    state.value = { status: 'ready', data, message: null }
  } catch (error: unknown) {
    const statusCode = typeof error === 'object' && error !== null && 'statusCode' in error
      ? Number((error as { statusCode?: unknown }).statusCode)
      : null

    state.value = statusCode === 503
      ? { status: 'unavailable', data: null, message: 'این اطلاعات فعلاً از منبع رسمی در دسترس نیست و به‌صورت صفر نمایش داده نمی‌شود.' }
      : { status: 'error', data: null, message: 'دریافت اطلاعات با خطا روبه‌رو شد.' }
  }
}

onMounted(load)
</script>

<template>
  <section class="gp-page" :aria-labelledby="`page-title-${endpoint.replaceAll('/', '-')}`">
    <header class="gp-card gp-page-header">
      <div>
        <p class="gp-eyebrow">پنل مشتری</p>
        <h2 :id="`page-title-${endpoint.replaceAll('/', '-')}`">{{ title }}</h2>
        <p class="gp-page-description">{{ description }}</p>
      </div>
      <span class="gp-source-badge">منبع رسمی اطلاعات</span>
    </header>

    <div class="gp-card gp-page-state" aria-live="polite" aria-atomic="true">
      <template v-if="state.status === 'loading'">
        <span class="gp-state-icon gp-state-icon-loading" aria-hidden="true" />
        <div>
          <h3>در حال دریافت اطلاعات</h3>
          <p class="gp-state">لطفاً چند لحظه صبر کنید.</p>
        </div>
      </template>

      <template v-else-if="state.status === 'empty'">
        <span class="gp-state-icon" aria-hidden="true">—</span>
        <div>
          <h3>هنوز موردی ثبت نشده است</h3>
          <p class="gp-state">{{ state.message }}</p>
        </div>
      </template>

      <template v-else-if="state.status === 'unavailable'">
        <span class="gp-state-icon gp-state-icon-warning" aria-hidden="true">!</span>
        <div>
          <h3>اطلاعات موقتاً در دسترس نیست</h3>
          <p class="gp-state">{{ state.message }}</p>
          <button class="gp-button gp-button-secondary" type="button" @click="load">تلاش دوباره</button>
        </div>
      </template>

      <template v-else-if="state.status === 'error'">
        <span class="gp-state-icon gp-state-icon-danger" aria-hidden="true">!</span>
        <div>
          <h3>دریافت اطلاعات انجام نشد</h3>
          <p class="gp-state">{{ state.message }}</p>
          <button class="gp-button gp-button-secondary" type="button" @click="load">تلاش دوباره</button>
        </div>
      </template>

      <template v-else>
        <span class="gp-state-icon gp-state-icon-success" aria-hidden="true">✓</span>
        <div>
          <h3>اطلاعات به‌روز دریافت شد</h3>
          <p class="gp-state gp-state-ready">اطلاعات با موفقیت از API رسمی مشتری دریافت شد.</p>
        </div>
      </template>
    </div>
  </section>
</template>
