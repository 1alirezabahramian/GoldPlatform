# AP-12 — Monitoring & System Health Read Foundation

## هدف
ایجاد API فقط‌خواندنی و امن برای وضعیت اجزای داخلی سامانه، بدون Restart، Retry، Clear یا عملیات مخرب.

## Endpoint
- `GET /api/v1/admin/system/health`

## Permission
- `system-health.view`

## بررسی‌های مجاز
- Database: اجرای `SELECT 1`
- Redis: اجرای `PING` فقط در صورت استفاده واقعی از Redis
- Cache: نمایش Driver و وضعیت وابسته به Probe موجود
- Queue: Connection و تعداد رکوردهای `jobs` در صورت وجود جدول
- Failed Jobs: تعداد رکوردهای `failed_jobs`
- Outbox: تعداد کل و Pending بر اساس ستون واقعی `processed_at`
- Storage: وجود و قابل‌نوشتن بودن مسیر Storage
- Runtime: Environment، Debug، نسخه PHP و Laravel

## اطلاعات ممنوع
- Host، Port، Username و Password
- DSN و URLهای اتصال
- مسیر مطلق سرور
- Stack trace و متن خام Exception
- Payloadهای Queue و Outbox
- Environment variables

## مرز ایمنی
- بدون Queue Retry/Forget
- بدون Cache Clear
- بدون Redis Flush
- بدون Restart
- بدون Migration
- بدون Docker command
- بدون Kimia call
- بدون تغییر تنظیمات

## محدودیت صادقانه
وضعیت Docker و Containerها از داخل Laravel منبع حقیقت قابل اتکایی ندارد؛ بنابراین `supported=false` و `not_observable_from_application` گزارش می‌شود.

## تست‌ها
- قرارداد Response نسخه‌دار
- الزام Role و Permission
- جلوگیری از دسترسی Operator
- عدم افشای Host و Password

## وضعیت اجرا
کد و تست ثبت شده‌اند؛ تست‌ها در این محیط اجرا نشده‌اند و PASS نهایی فقط پس از CI معتبر اعلام می‌شود.
