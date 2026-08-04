# CP-11 — Customer OpenAPI Foundation

## هدف

تثبیت قرارداد قابل‌اعتبارسنجی OpenAPI برای Customer API موجود، بدون ساخت Rule مالی، Kimia Rule یا Endpoint عملیاتی جدید.

## منبع موجود

فایل اصلی:

`docs/api/customer-v1.openapi.yaml`

این فایل از قبل وجود داشت و در CP-11 فایل موازی یا تکراری ساخته نشد.

## کنترل‌های افزوده‌شده

`CustomerOpenApiFoundationTest` بررسی می‌کند:

- OpenAPI 3.1 بودن سند
- Scope نسخه‌دار `/api/v1/customer`
- Sanctum authentication
- ثبت Read Contractهای فعلی Dashboard و Assets
- استفاده از string برای مقادیر مالی دقیق
- عدم انتشار `account_id`، `product_id`، `transaction_code` و `action_code`
- وجود reference عمومی و request_id

## ادامه همین Stage

پس از Merge شدن CP-10، همان فایل OpenAPI برای قراردادهای زیر تکمیل می‌شود:

- Bootstrap
- Profile
- Activity Timeline
- Custody detail
- Delivery detail
- Error Envelope استاندارد CP-10

## مرزهای ایمنی

- بدون تغییر Ledger یا Wallet
- بدون فرمول قیمت و کارمزد
- بدون Migration
- بدون Kimia Read/Write
- بدون ایجاد فایل OpenAPI تکراری
