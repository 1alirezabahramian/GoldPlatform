<script setup lang="ts">
import type { CustomerApiState, CustomerEnvelope } from '~/composables/useCustomerApi'

const props = defineProps<{
  title: string
  description: string
  endpoint: string
}>()

const { read } = useCustomerApi()
const state = ref<CustomerApiState<unknown>>({ status: 'loading', data: null, message: null })

onMounted(async () => {
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
})
</script>

<template>
  <section>
    <header class="gp-card gp-page-header">
      <p class="gp-eyebrow">پنل مشتری</p>
      <h2>{{ title }}</h2>
      <p>{{ description }}</p>
    </header>

    <div class="gp-card gp-page-state" aria-live="polite">
      <p v-if="state.status === 'loading'" class="gp-state">در حال دریافت اطلاعات…</p>
      <p v-else-if="state.status === 'empty'" class="gp-state">{{ state.message }}</p>
      <p v-else-if="state.status === 'unavailable'" class="gp-state">{{ state.message }}</p>
      <p v-else-if="state.status === 'error'" class="gp-state">{{ state.message }}</p>
      <p v-else class="gp-state gp-state-ready">اطلاعات با موفقیت از API رسمی مشتری دریافت شد.</p>
    </div>
  </section>
</template>
