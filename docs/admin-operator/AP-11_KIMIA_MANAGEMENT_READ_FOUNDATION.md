# AP-11 — Kimia Management Read Foundation

## هدف
ایجاد API فقط‌خواندنی و امن برای نمایش وضعیت پیکربندی و حالت ایمنی Kimia، بدون اجرای Probe، Sync، Retry یا Write.

## Ground Truth
- `KimiaClient` دارای تنظیمات base URL، credential، timeout، retry و حالت `read_only` است.
- Client در حالت read-only تمام درخواست‌های غیر GET را مسدود می‌کند.
- جدول یا Store تأییدشده‌ای برای Health History، Latency History، Sync History یا Request Log در شاخه فعلی وجود ندارد.
- فایل `routes/kimia.php` در شاخه بررسی‌شده Route عملیاتی ندارد.

## Endpoint
- `GET /api/v1/admin/kimia/overview`

## Permission
- `kimia.read`

## خروجی مجاز
- کامل یا ناقص بودن پیکربندی
- تنظیم بودن Base URL بدون نمایش مقدار
- تنظیم بودن Credential بدون نمایش مقدار
- timeout و retry policy
- حالت read-only
- فعال یا غیرفعال بودن Write طبق تنظیم واقعی
- وضعیت پشتیبانی Observability

## خروجی‌های صریحاً ناموجود
- `last_sync_at = null`
- `latency_ms = null`
- `connection_status = not_probed`

این مقادیر حدس زده نمی‌شوند و Endpoint نیز هیچ تماس شبکه‌ای با Kimia انجام نمی‌دهد.

## اطلاعات ممنوع
- Base URL واقعی
- username
- password
- token یا credential
- payload خام
- response خام
- stack trace و متن خطای داخلی

## مرز ایمنی
- بدون Active Probe
- بدون Sync
- بدون Retry
- بدون Kimia Write
- بدون Migration
- بدون Queue فرضی
- بدون Health یا Latency ساختگی

## تست‌ها
- قرارداد پاسخ نسخه‌دار
- نمایش حالت read-only واقعی
- عدم افشای credential و Base URL
- جلوگیری از دسترسی Operator
