# AP-12 — Monitoring & System Health Read Foundation

## هدف
ایجاد API فقط‌خواندنی و امن برای وضعیت اجزای داخلی سامانه، بدون Restart، Retry، Clear یا عملیات مخرب.

## Endpoint
- `GET /api/v1/admin/system/health`

## Permission
- `system-health.view`

## بررسی‌های مجاز
- Database: اجرای `SELECT 1`
- Redis: اجرای `PING` فقط در صورت استفاده از Redis
- Cache: نمایش Driver پیکربندی‌شده و وضعیت Probe کنترل‌شده
- Queue: نمایش Connection و تعداد رکوردهای `jobs` در صورت وجود جدول
- Failed Jobs: تعداد رکوردهای `failed_jobs`
- Outbox: تعداد کل و Pending بر اساس داده واقعی
- Storage: وجود و قابل‌نوشتن بودن مسیر محلی برنامه
- Runtime: محیط اجرا، Debug، PHP و Laravel Version

## اطلاعات ممنوع
- Host، Port، Username و Password دیتابیس یا Redis
- DSN و URLهای اتصال
- مسیر مطلق سرور
- Stack trace و متن خام Exception
- محتوای Job، Payload یا Outbox
- Environment variables

## مرز ایمنی
- بدون Queue Retry یا Forget
- بدون Cache Clear
- بدون Redis Flush
- بدون Restart
- بدون Migration
- بدون Docker command
- بدون Kimia call
- بدون تغییر تنظیمات

## وضعیت صداقت
Health فقط وضعیت لحظه‌ای Probeهای سبک و داده‌های موجود را گزارش می‌کند. Docker/container health از داخل Laravel Ground Truth معتبری ندارد و `supported=false` گزارش می‌شود.
