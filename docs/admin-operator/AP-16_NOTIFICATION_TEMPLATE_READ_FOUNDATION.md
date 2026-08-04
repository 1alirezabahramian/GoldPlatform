# AP-16 — Notification & Template Read Foundation

## هدف
ایجاد API فقط‌خواندنی برای نمایش وضعیت واقعی کانال‌های اعلان، بدون ارسال پیام، تغییر Template یا افشای Credential.

## Endpoint
- `GET /api/v1/admin/notifications/overview`

## Permission
- `notifications.view`

## Ground Truth
- SMS.ir در `config/services.php` وجود دارد و فقط Template ورود در تنظیمات دیده شد.
- Email از تنظیمات استاندارد Laravel استفاده می‌کند.
- Outbox عمومی پروژه وجود دارد، اما Notification Delivery Store اختصاصی نیست.
- Telegram، Push، In-App Notification Center، Channel Preferences و Template Store مستقل پیدا نشدند.

## خروجی
- وضعیت پیکربندی SMS.ir بدون نمایش API Key یا Base URL
- نام Driver ایمیل بدون نمایش Host یا Credential
- تعداد کل و Pending Outbox در صورت وجود جدول
- اعلام صریح نبود Telegram Bot، Push Provider، Notification Center و Delivery Log

## مرزهای ایمنی
- بدون ارسال SMS، Email، Telegram یا Push
- بدون ساخت Bot یا Webhook
- بدون Retry
- بدون تغییر Template
- بدون Migration
- بدون افشای Token، Password، API Key یا Recipient

## تصمیم محصول
Telegram در معماری هدف، کانال کمکی Admin/Operator و کانال اختیاری اعلان مشتری است؛ اما در شاخه فعلی هنوز پیاده‌سازی نشده است.

## وضعیت تست
Feature Test نوشته شده، اما در محیط فعلی اجرا نشده است. PASS نهایی فقط پس از CI اعلام می‌شود.
