<script setup lang="ts">
type FoundationState = 'loading' | 'empty' | 'error' | 'unavailable'

const states: Array<{ key: FoundationState; title: string; description: string }> = [
  { key: 'loading', title: 'در حال دریافت اطلاعات', description: 'اطلاعات شما از سرویس رسمی دریافت می‌شود.' },
  { key: 'empty', title: 'اطلاعاتی برای نمایش نیست', description: 'در حال حاضر داده‌ای از API مشتری دریافت نشده است.' },
  { key: 'error', title: 'خطا در دریافت اطلاعات', description: 'امکان نمایش اطلاعات وجود ندارد. دوباره تلاش کنید.' },
  { key: 'unavailable', title: 'اطلاعات فعلاً در دسترس نیست', description: 'مقدار نامشخص به‌عنوان صفر نمایش داده نمی‌شود.' }
]
</script>

<template>
  <section>
    <div class="gp-card">
      <p class="gp-eyebrow">زیرساخت رابط مشتری</p>
      <h2>مدیریت دارایی‌ها، ساده و روشن</h2>
      <p>این نسخه هیچ مانده، قیمت یا نتیجه مالی ساختگی نمایش نمی‌دهد و فقط آماده اتصال به قرارداد تأییدشده Customer API است.</p>
    </div>

    <div class="gp-grid" aria-label="وضعیت‌های استاندارد داده">
      <article v-for="state in states" :key="state.key" class="gp-card">
        <h3>{{ state.title }}</h3>
        <p class="gp-state">{{ state.description }}</p>
      </article>
    </div>
  </section>
</template>
