<script setup lang="ts">
definePageMeta({ middleware: 'backoffice' })
const { page, status, queue, pending, error, refresh } = useOperatorQueue('deliveries')
</script>

<template>
  <OperatorQueueWorkspace
    title="صف تحویل‌ها"
    :queue="queue"
    :pending="pending"
    :error="error"
    :status="status"
    :statuses="['requested', 'approved', 'ready']"
    @update:status="value => { status = value; page = 1 }"
    @refresh="refresh"
    @page="value => page = value"
  />
</template>
