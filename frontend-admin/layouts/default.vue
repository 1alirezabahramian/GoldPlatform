<script setup lang="ts">
const { session, clear } = useBackofficeSession()
const logout = async () => {
  try { await $fetch('/api/auth/logout', { method: 'POST', credentials: 'include' }) } finally {
    clear()
    await navigateTo('/session-expired')
  }
}
</script>

<template>
  <div class="shell">
    <aside v-if="session" class="sidebar">
      <div>
        <p class="brand">GoldPlatform</p>
        <p class="muted">{{ session.panel === 'admin' ? 'پنل مدیریت' : 'پنل اپراتور' }}</p>
      </div>
      <nav class="nav">
        <NuxtLink v-for="item in session.navigation" :key="item.code" :to="item.path" class="nav-link">
          {{ item.label }}
        </NuxtLink>
      </nav>
      <div class="profile">
        <strong>{{ session.user.display_name }}</strong>
        <span>{{ session.user.mobile_masked }}</span>
        <button type="button" class="logout" @click="logout">خروج</button>
      </div>
    </aside>
    <main class="content"><slot /></main>
  </div>
</template>
