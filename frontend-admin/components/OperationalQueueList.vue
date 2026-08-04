<script setup lang="ts">
import type { OperationalQueueItem } from '~/types/dashboard'

defineProps<{
  title: string
  items: OperationalQueueItem[]
}>()
</script>

<template>
  <section class="queue-card">
    <header><h2>{{ title }}</h2></header>
    <p v-if="items.length === 0" class="empty-state">موردی در صف نیست.</p>
    <ul v-else>
      <li v-for="item in items" :key="item.id">
        <span>#{{ item.id.toLocaleString('fa-IR') }}</span>
        <strong>{{ item.status }}</strong>
        <time v-if="item.created_at" :datetime="item.created_at">
          {{ new Date(item.created_at).toLocaleString('fa-IR') }}
        </time>
      </li>
    </ul>
  </section>
</template>
