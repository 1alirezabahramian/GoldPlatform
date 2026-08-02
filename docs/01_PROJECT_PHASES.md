# GoldPlatform — فازها و ترتیب اجرای پروژه

**وضعیت سند:** Accepted baseline

**آخرین بازنگری:** 2026-08-03

این سند ترتیب توسعه را مشخص می‌کند تا Frontend و Backend موازی پیش بروند، بدون اینکه UI منطق مالی حل‌نشده را تعیین کند.

## مسیرهای موازی

| مسیر | کار مستقل قابل انجام | وابستگی اصلی |
|---|---|---|
| Backend/Core | Kimia Read/Sync، DTO/Adapter، تست، Auth، Tenant، Catalog | شواهد API و تصمیم‌های کسب‌وکار |
| Frontend/UX | Design system، Shell پنل‌ها، Navigation، حالت‌های Loading/Error، Prototype | قراردادهای پایدار API |
| Documentation | حافظه، ADR، وضعیت، شواهد Kimia، UX تصمیم‌ها | هم‌زمان با هر تغییر |
| Shop Verification | Docker، تست کامل، Kimia زنده فقط خواندنی | حضور علیرضا در سیستم مغازه |

## فاز 0 — کشف دامنه و تثبیت Ground Truth

**وضعیت:** عمدتاً انجام‌شده؛ Living activity

- چهار دارایی مالی و جدایی Custody ثبت شده‌اند.
- سیاست No Guessing و ترتیب منابع پذیرفته شده‌اند.
- نگاشت API معامله `32/64` با خروجی واقعی حساب `350` تأیید شده است.
- موارد حل‌نشده همچنان باید با `Unknown/Stop Condition` مشخص شوند.

## فاز 1 — زیرساخت و پایه Backend

**وضعیت:** تثبیت‌شده، با چند بدهی فنی

- Docker، Nginx، PHP-FPM، MySQL، Redis و Laravel آماده‌اند.
- Auth، Sanctum، OTP tables، Permission و Wallet foundation وجود دارند.
- تست کامل در 2026-08-02: `23 passed / 160 assertions`.
- بدهی باز: Auth flow ناسازگار، مسیرهای ناقص OTP و نبود CI خودکار.

## فاز 2 — پایه محصول و Multi-tenancy

**وضعیت:** معماری پذیرفته‌شده؛ پیاده‌سازی شروع نشده

- Tenant model و Tenant isolation
- Branding، domain و module configuration
- Feature flag و licensing foundation
- Khalifeh Coin به‌عنوان Tenant اول

تصمیم Shared DB در برابر Database-per-tenant و روش Tenant resolution قبل از Migration نیازمند ADR مستقل است.

## فاز 3 — تثبیت Kimia Connector

**وضعیت:** اولویت فنی جاری

انجام‌شده:

- Account، Group، Coin و Currency Sync همراه تست Mock
- اصلاح `GET /api/account → Type`
- تثبیت `GET /api/account/groups → accountType`
- خواندن تراکنش‌ها با pagination و boolean literal صحیح
- نگاشت قطعی `customer buy → 64` و `customer sell → 32`
- مسیر خواندنی Balance و تست‌های Mock آماده شده؛ اجرای Container باقی مانده است.
- قفل fail-closed نوشتن Kimia و فرمان بررسی وضعیت ایمنی آماده شده است.
- اجرای یک‌مرحله‌ای تست‌های مغازه با گزارش متنی واحد آماده شده است.

گام‌های باقی‌مانده:

1. اجرای تست Container برای مسیر Balance.
2. بازآزمایی زنده Account/Group/Balance پس از تثبیت شاخه.
3. تکمیل DTO/Mapperهای خواندنی با داده واقعی.
4. تعیین تکلیف و حذف کنترل‌شده مسیر Integration بلااستفاده فقط پس از اثبات.
5. فعال نکردن هیچ Write تا تأیید Payload و Idempotency.

Checkpoint بعدی با اجرای `scripts/run-shop-verification.ps1` روی سیستم مغازه آغاز می‌شود.
این اجرا Migration را فقط Preview می‌کند و نوشتن زنده Kimia را انجام نمی‌دهد.

## فاز 4 — Catalog و Pricing

**وضعیت:** Production module شروع نشده

- Product/Coin/Currency Dynamic
- تفکیک محصول مالی و فیزیکی
- فرمول، Spread، کارمزد، رندینگ و Price freshness
- نمایش/محدودیت محصول بر اساس Tenant و گروه مشتری

## فاز 5 — Ledger، Projection و Settlement

**وضعیت:** Foundation موجود؛ قرارداد تولیدی کامل نیست

- Local Projection قابل Reconciliation با Kimia
- Holds و locked balance
- Credit limits و ضدنوسان‌گیری
- Immutable audit events
- عدم ایجاد Ledger رقیب برای Kimia

## فاز 6 — OMS و Trading

**وضعیت:** چرخه کامل پیاده‌سازی نشده

- Buy/Sell، Amount/Weight order
- Price snapshot و Freeze timer
- Approval/Reject/Expire
- Idempotent Kimia posting پس از تأیید
- Retry و reconciliation

## فاز 7 — Custody و Delivery

**وضعیت:** دامنه تعریف‌شده؛ پیاده‌سازی شروع نشده

- ورود دارایی فیزیکی به امانات
- Ready for pickup، شعبه و زمان تحویل
- Delivered و بستن چرخه امانت
- فروش مجدد یا تبدیل مجاز به پول/طلا

## فاز 8 — اتصال‌های بیرونی

**وضعیت:** SMS foundation ناقص؛ سایر اتصال‌ها شروع نشده

- SMS، KYC جیبیت، Payment، Telegram، Price API
- Connectorهای قابل تعویض
- Queue، Retry و Dead-letter بدون اثر منفی روی عملیات مالی

## فاز 9 — پنل‌ها و Frontend تولیدی

**وضعیت:** شروع نشده؛ طراحی مقدماتی مجاز است

ترتیب:

1. Design system و RTL foundation
2. Auth shell
3. Admin/Super-admin tenant configuration
4. Operator workflow
5. Customer assets/prices
6. Trading flow
7. Custody/delivery
8. Reports

## فاز 10 — گزارش، عملیات و عرضه تجاری

**وضعیت:** آینده

- Reconciliation، Audit، Alert، Backup/Restore
- Tenant onboarding، plan/licensing، deployment و monitoring

## مایلستون فعلی

```text
Product Foundation + Kimia Read Stabilization
```

خروج از این مایلستون زمانی است که مسیر فعال Kimia واحد باشد، Account/Group/Balance با شواهد واقعی خوانده شوند، Tenant ADR ثبت شود، و تست کامل پس از تغییرات دوباره موفق باشد.
