<script setup lang="ts">
defineProps<{
  loading: boolean
  error: string | null
}>()

defineEmits<{ refresh: [] }>()
</script>

<template>
  <div v-if="loading" class="state-card" role="status">در حال دریافت وضعیت عملیاتی…</div>
  <div v-else-if="error" class="state-card error-state" role="alert">
    <p>{{ error }}</p>
    <button type="button" @click="$emit('refresh')">تلاش دوباره</button>
  </div>
  <slot v-else />
</template>
