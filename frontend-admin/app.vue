<script setup lang="ts">
const route = useRoute()
const items = [
  { label: 'داشبورد مدیر', to: '/admin', permission: 'admin.dashboard.view' },
  { label: 'داشبورد اپراتور', to: '/operator', permission: 'operator.dashboard.view' },
  { label: 'کاربران', to: '/admin/users', permission: 'users.view' },
  { label: 'سفارش‌ها', to: '/admin/orders', permission: 'orders.view' },
  { label: 'تحویل‌ها', to: '/admin/deliveries', permission: 'deliveries.view' },
  { label: 'تسویه‌ها', to: '/admin/settlements', permission: 'settlements.view' },
  { label: 'سلامت سیستم', to: '/admin/system-health', permission: 'system-health.view' }
]
const { permissions } = useSession()
const visibleItems = computed(() => items.filter(item => permissions.value.includes(item.permission)))
</script>

<template>
  <div class="app-shell">
    <aside class="sidebar">
      <div class="brand">GoldPlatform</div>
      <nav>
        <NuxtLink v-for="item in visibleItems" :key="item.to" :to="item.to" :class="{ active: route.path === item.to }">
          {{ item.label }}
        </NuxtLink>
      </nav>
    </aside>
    <main class="content"><NuxtPage /></main>
  </div>
</template>
