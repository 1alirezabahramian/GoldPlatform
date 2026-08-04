<script setup lang="ts">
onMounted(async () => {
  const { load } = useBackofficeSession()
  const admin = await load('admin')
  if (admin) return navigateTo('/admin')
  const operator = await load('operator')
  if (operator) return navigateTo('/operator')
  return navigateTo('/session-expired')
})
</script>

<template><section class="state-card"><h1>در حال بررسی دسترسی…</h1></section></template>
