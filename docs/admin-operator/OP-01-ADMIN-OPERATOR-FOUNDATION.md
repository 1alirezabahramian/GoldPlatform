# OP-01 — Admin & Operator Foundation

## هدف

ساخت Contract نسخه‌دار و مستقل برای شروع پنل‌های Admin و Operator، بدون تغییر منطق مالی، Ledger، Wallet یا Kimia.

## وضعیت موجود پیش از OP-01

- Routeهای عملیاتی قدیمی `/api/admin/*` و `/api/operator/*` از قبل وجود داشتند.
- نقش‌های تأییدشده در Routeهای موجود فقط `admin` و `operator` بودند.
- Bootstrap نسخه‌دار و OpenAPI مستقل برای Backoffice وجود نداشت.

## تغییرات پیاده‌سازی‌شده

- افزودن فایل مستقل `routes/backoffice_v1.php`.
- افزودن `GET /api/v1/admin/bootstrap` با نقش `admin`.
- افزودن `GET /api/v1/operator/bootstrap` با نقش `operator|admin`.
- افزودن `BackofficeApiResponse` با Envelope نسخه‌دار و `X-Request-ID`.
- افزودن Navigation و Capability فقط بر اساس Routeهای عملیاتی موجود.
- افزودن OpenAPI اولیه Backoffice V1.
- افزودن Guard Test برای جداسازی Routeها، نقش‌ها و عدم افشای اصطلاحات داخلی Kimia.

## Permission Matrix تأییدشده در این مرحله

| Scope | نقش مجاز | Capabilityهای اعلام‌شده |
|---|---|---|
| Admin Bootstrap | admin | audit logs read, outbox read, customer policies read/update |
| Operator Bootstrap | operator یا admin | order queue read, delivery queue read, delivery approve/ready/deliver |

هیچ نقش جدیدی مانند Super Admin، Auditor یا Support در این مرحله ساخته نشد، چون در منابع تأییدشده فعلی تعریف نشده است.

## مرزهای ایمنی

- بدون Migration
- بدون Rule مالی جدید
- بدون Wallet/Ledger/Settlement
- بدون Kimia Read/Write
- بدون تغییر Routeهای قدیمی Admin و Operator
- بدون تغییر Customer API

## تست

`backend/tests/Unit/Architecture/AdminOperatorFoundationTest.php`

این تست وجود Routeهای نسخه‌دار، Role Middleware و عدم انتشار نام‌های داخلی Kimia را قفل می‌کند.
