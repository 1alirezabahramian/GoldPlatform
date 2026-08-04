<script setup lang="ts">
defineProps<{
  items: Array<{ label: string; value: number | null }>
}>()
</script>

<template>
  <section class="summary-grid" aria-label="خلاصه وضعیت عملیاتی">
    <article v-for="item in items" :key="item.label" class="summary-card">
      <span>{{ item.label }}</span>
      <strong>{{ item.value === null ? 'ناموجود' : item.value.toLocaleString('fa-IR') }}</strong>
    </article>
  </section>
</template>
