# CP-11 — Customer OpenAPI Foundation

## وضعیت

پیاده‌سازی کامل روی شاخه `work/customer-openapi-foundation` و در انتظار CI.

## هدف

تثبیت قرارداد قابل‌اعتبارسنجی OpenAPI برای Customer API موجود، بدون ساخت Rule مالی، Kimia Rule یا Endpoint عملیاتی جدید.

## منبع اصلی

`docs/api/customer-v1.openapi.yaml`

فایل از قبل وجود داشت و در CP-11 هیچ فایل OpenAPI موازی یا تکراری ساخته نشد.

## Endpointهای مستندشده

- Bootstrap
- Dashboard
- Profile
- Activity Timeline
- Assets: Money، Gold، Coin و Currency
- Orders و Order Statuses
- Custody list/detail
- Custody delivery request
- Delivery list/detail

## قراردادهای مشترک

- OpenAPI 3.1
- Scope نسخه‌دار `/api/v1/customer`
- Sanctum authentication
- شناسه عمومی `reference`
- Header اجباری `Idempotency-Key` برای درخواست تحویل
- Pagination با سقف ۵۰
- Envelope موفق شامل `data`، `meta` و `message`
- Error Envelope شامل `message`، `code`، `errors` و `request_id`
- کدهای خطای 401، 403، 404، 422، 429 و خطای داخلی امن
- مقادیر مالی به‌صورت string و بدون float

## Contract Test

`CustomerOpenApiFoundationTest` کنترل می‌کند:

- وجود مسیرهای واقعی Customer API
- OpenAPI 3.1 و Sanctum
- دقت رشته‌ای مقادیر مالی
- عدم انتشار `account_id`، `product_id`، `transaction_code` و `action_code`
- وجود reference عمومی، request_id، api_version و Idempotency-Key
- وجود Error Contract استاندارد CP-10

## مرزهای ایمنی

- بدون تغییر Ledger یا Wallet
- بدون فرمول قیمت و کارمزد
- بدون Migration
- بدون Kimia Read/Write
- بدون ایجاد فایل OpenAPI تکراری
