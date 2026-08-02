# GoldPlatform

GoldPlatform یک سامانه White-label و Multi-tenant برای معامله طلا و سکه، مدیریت دارایی مشتری، امانات فیزیکی، تحویل، قیمت‌گذاری و اتصال به سامانه‌های حسابداری است.

`Khalifeh Coin` نخستین Tenant واقعی و محیط پایلوت محصول است؛ قوانین اختصاصی آن نباید در هسته پلتفرم Hard-code شوند.

## وضعیت جاری — 2026-08-02

| بخش | وضعیت |
|---|---|
| زیرساخت Laravel / Docker / MySQL / Redis / Nginx | تثبیت‌شده |
| Kimia Read/Sync | در حال تثبیت؛ Account/Coin/Currency/Group تست‌شده و Balance آماده تست Runtime |
| نگاشت معامله Kimia | تأییدشده: خرید مشتری `→ 64`، فروش مشتری `→ 32` |
| تست کامل | `23 passed`, `160 assertions`, `0 failures` |
| ارسال سند مالی زنده به Kimia | غیرفعال تا تکمیل قرارداد Payload و Idempotency |
| Frontend | پیاده‌سازی تولیدی شروع نشده؛ طراحی می‌تواند موازی با Backend پیش برود |
| White-label / Multi-tenancy | جهت معماری پذیرفته شده؛ هنوز پیاده‌سازی نشده |

شاخه مرجع فعلی توسعه Kimia:

```text
audit/kimia-foundation
```

## مرز معماری

- Kimia مرجع مانده‌های مالی و موجودی حسابداری فروشگاه است.
- GoldPlatform مرجع سفارش، قیمت قفل‌شده، قوانین مشتری، رزرو، امانات، تحویل، هماهنگی اتصال‌ها و Audit است.
- داده محلی Kimia یک Projection/Cache قابل بازسازی است و نباید به دفتر مالی رقیب تبدیل شود.
- `Money / Gold / Coin / Currency` دارایی مالی‌اند؛ `Custody / Amanat` دارایی فیزیکی و مستقل است.
- Backend پیچیدگی مالی و Kimia را جذب می‌کند؛ Frontend فقط مفاهیم انسانی و ساده را نمایش می‌دهد.

## مستندات مرجع

- [`docs/00_PROJECT_MEMORY.md`](docs/00_PROJECT_MEMORY.md) — حافظه و قرارداد Ground Truth
- [`docs/project_state.md`](docs/project_state.md) — وضعیت جاری و تاریخچه Checkpointها
- [`docs/01_PROJECT_PHASES.md`](docs/01_PROJECT_PHASES.md) — فازها و ترتیب اجرا
- [`docs/08_KIMIA_INTEGRATION_AUDIT.md`](docs/08_KIMIA_INTEGRATION_AUDIT.md) — ممیزی Kimia
- [`docs/PROJECT_PRINCIPLES.md`](docs/PROJECT_PRINCIPLES.md) — اصول الزام‌آور فعلی
- [`docs/ADR`](docs/ADR) — تصمیم‌های معماری پذیرفته‌شده

## قانون توسعه

```text
Evidence → Decision → Small Change → Test → Documentation → Commit
```

هیچ شناسه، Action، فرمول مالی، Wallet Rule یا Payload مربوط به Kimia حدس زده نمی‌شود.
