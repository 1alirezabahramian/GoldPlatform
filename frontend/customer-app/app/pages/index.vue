<script setup lang="ts">
const sections = [
  { title: 'پول من', description: 'نمایش مانده و مبالغ قابل‌استفاده از API رسمی مشتری' },
  { title: 'طلای من', description: 'نمایش وزن و وضعیت دارایی بدون اصطلاحات حسابداری' },
  { title: 'سکه‌ها و ارزها', description: 'دارایی‌های پویا بر اساس قرارداد Backend' },
  { title: 'امانات من', description: 'دارایی‌های فیزیکی، جدا از مانده‌های مالی' },
]
</script>

<template>
  <section>
    <div class="gp-card">
      <p class="gp-eyebrow">نسخه پایه رابط مشتری</p>
      <h2>دارایی‌ها و درخواست‌های شما، ساده و شفاف</h2>
      <p>
        این پوسته فقط قراردادهای تأییدشده Customer API را مصرف می‌کند و هیچ محاسبه مالی در مرورگر انجام نمی‌دهد.
      </p>
    </div>

    <div class="gp-grid" style="margin-top: var(--gp-space-4)">
      <article v-for="section in sections" :key="section.title" class="gp-card">
        <h3>{{ section.title }}</h3>
        <p>{{ section.description }}</p>
      </article>
    </div>
  </section>
</template>
