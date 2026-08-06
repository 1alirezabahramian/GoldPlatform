<template>
  <section class="gp-page">
    <header class="gp-card gp-page-header">
      <div>
        <p class="gp-eyebrow">نمای کلی حساب</p>
        <h2>داشبورد</h2>
        <p class="gp-page-description">
          مانده‌های پول، طلا، سکه و ارز فقط پس از شناسایی حساب مشتری در Kimia نمایش داده می‌شوند.
        </p>
      </div>
      <span class="gp-source-badge">منبع نهایی: Kimia</span>
    </header>

    <div class="gp-contract-grid" aria-label="بخش‌های داشبورد مالی">
      <article class="gp-card gp-contract-card">
        <p class="gp-eyebrow">پول</p>
        <h3>مانده ریالی</h3>
        <p class="gp-state">تا زمان اتصال حساب رسمی، مبلغی نمایش داده نمی‌شود.</p>
      </article>
      <article class="gp-card gp-contract-card">
        <p class="gp-eyebrow">طلا</p>
        <h3>مانده طلایی</h3>
        <p class="gp-state">وزن یا ارزش فرضی در Frontend محاسبه نمی‌شود.</p>
      </article>
      <article class="gp-card gp-contract-card">
        <p class="gp-eyebrow">سکه</p>
        <h3>دارایی سکه</h3>
        <p class="gp-state">فهرست سکه‌ها باید به‌صورت پویا از Kimia دریافت شود.</p>
      </article>
      <article class="gp-card gp-contract-card">
        <p class="gp-eyebrow">ارز</p>
        <h3>دارایی ارزی</h3>
        <p class="gp-state">فهرست ارزها باید به‌صورت پویا از Kimia دریافت شود.</p>
      </article>
    </div>

    <CustomerReadPage
      title="وضعیت اتصال حساب مالی"
      description="Backend تا زمان حل نگاشت حساب مشتری به Kimia به‌صورت امن پاسخ ناموجود می‌دهد."
      endpoint="/dashboard"
    />
  </section>
</template>
