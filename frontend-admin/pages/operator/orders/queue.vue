<script setup lang="ts">
definePageMeta({ middleware: 'backoffice' })
const { page, status, queue, pending, error, refresh } = useOperatorQueue('orders')
</script>

<template>
  <OperatorQueueWorkspace
    title="صف سفارش‌ها"
    :queue="queue"
    :pending="pending"
    :error="error"
    :status="status"
    :statuses="['pending', 'approved', 'executing', 'settling']"
    @update:status="value => { status = value; page = 1 }"
    @refresh="refresh"
    @page="value => page = value"
  />
</template>
