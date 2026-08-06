<template>
  <section class="gp-page">
    <header class="gp-card gp-page-header">
      <div>
        <p class="gp-eyebrow">دارایی‌های مالی</p>
        <h2>دارایی‌ها</h2>
        <p class="gp-page-description">
          پول، طلا، سکه و ارز فقط از مسیر Backend و با اتکا به مانده رسمی Kimia نمایش داده می‌شوند.
        </p>
      </div>
      <span class="gp-source-badge">بدون مانده داخلی رقیب</span>
    </header>

    <div class="gp-card gp-page-state" aria-live="polite">
      <span class="gp-state-icon gp-state-icon-warning" aria-hidden="true">!</span>
      <div>
        <h3>اتصال حساب مالی هنوز کامل نشده است</h3>
        <p class="gp-state">
          تا زمان شناسایی حساب مشتری در Kimia، هیچ مانده، ارزش یا عدد جایگزینی نمایش داده نمی‌شود.
        </p>
      </div>
    </div>

    <CustomerReadPage
      title="وضعیت دریافت دارایی‌ها"
      description="این صفحه پاسخ واقعی قرارداد /assets را نمایش می‌دهد و اطلاعات ناموجود را صفر نمی‌کند."
      endpoint="/assets"
    />
  </section>
</template>
