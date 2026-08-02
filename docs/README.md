# نقشه مستندات GoldPlatform

این پوشه مرجع فنی و کسب‌وکاری پروژه است. در صورت تناقض، ترتیب اعتبار زیر اعمال می‌شود:

1. خروجی واقعی API کیمیا
2. Swagger/OpenAPI رسمی کیمیا
3. `00_PROJECT_MEMORY.md` و ADRهای پذیرفته‌شده
4. قواعد و نمونه‌های واقعی تأییدشده توسط علیرضا بهرامیان
5. کد جاری شاخه مرجع GitHub
6. دانش عمومی، فقط برای توضیح

## نقطه شروع

- [`00_PROJECT_MEMORY.md`](00_PROJECT_MEMORY.md) — حافظه Ground Truth؛ قبل از تغییر معماری یا منطق مالی خوانده شود.
- [`project_state.md`](project_state.md) — وضعیت اجرایی و نتایج تست.
- [`01_PROJECT_PHASES.md`](01_PROJECT_PHASES.md) — ترتیب فازها و مسیرهای موازی Frontend/Backend.
- [`PROJECT_PRINCIPLES.md`](PROJECT_PRINCIPLES.md) — اصول الزام‌آور و مرز Kimia/GoldPlatform.

## Kimia

- [`08_KIMIA_INTEGRATION_AUDIT.md`](08_KIMIA_INTEGRATION_AUDIT.md) — Endpointها، Schemaها، وضعیت پیاده‌سازی و ناشناخته‌ها.
- [`integrations/40_KIMIA_REVERSE_ENGINEERING.md`](integrations/40_KIMIA_REVERSE_ENGINEERING.md) — داده‌های Reverse Engineering.
- [`integrations/41_KIMIA_UI_EVIDENCE_2026-08-02.md`](integrations/41_KIMIA_UI_EVIDENCE_2026-08-02.md) — شواهد فرم‌های واقعی تعریف حساب و جنس.
- [`ADR/ADR-023-kimia-customer-trade-action-mapping.md`](ADR/ADR-023-kimia-customer-trade-action-mapping.md) — نگاشت قطعی `32/64`.
- [`ADR/ADR-024-platform-user-kimia-account-binding.md`](ADR/ADR-024-platform-user-kimia-account-binding.md) — اتصال یک‌به‌یک اکانت پلتفرم و `AccountId` کیمیا.

## معماری و دامنه

- [`architecture`](architecture) — معماری سیستم، Domain Model و Blueprintها.
- [`domain`](domain) — Auth، Wallet، Trading، Custody، Settlement، OMS و گزارش‌ها.
- [`business/Business-Rules.md`](business/Business-Rules.md) — قواعد کسب‌وکار پذیرفته‌شده.
- [`database`](database) — جدول‌ها، Migrationها، Indexها و ERD.
- [`ui/UI-UX.md`](ui/UI-UX.md) — تصمیم‌ها و ایده‌های Frontend.

## قاعده نگهداری

- اطلاعات تاریخی حذف نمی‌شود، اما باید با تاریخ و وضعیت `Historical` یا `Superseded` مشخص شود.
- تغییر کد، تصمیم معماری، نتیجه تست و شاهد Kimia هم‌زمان در سند مرتبط ثبت می‌شود.
- هیچ Secret، Token، Password یا داده حساس مشتری در مستندات یا Git ثبت نمی‌شود.
