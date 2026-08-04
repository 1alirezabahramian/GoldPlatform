# CP-06 — Customer Custody & Delivery Contract

## وضعیت

پیاده‌سازی واقعی روی شاخه `work/customer-custody-delivery-contract`.

## هدف

ارائه API نسخه‌دار و امن برای مشاهده جزئیات امانت، مشاهده جزئیات درخواست تحویل و ثبت درخواست تحویل با شناسه عمومی پلتفرم؛ بدون افشای شناسه‌های داخلی یا اطلاعات حساس.

## مسیرها

- `GET /api/v1/customer/custodies/{reference}`
- `GET /api/v1/customer/deliveries/{reference}`
- `POST /api/v1/customer/custodies/{reference}/delivery-request`

## قواعد امنیتی

- `reference` فقط UUID عمومی پلتفرم است.
- هر Query هم‌زمان با `user_id` کاربر احراز هویت‌شده محدود می‌شود.
- رکورد متعلق به مشتری دیگر با پاسخ 404 عمومی مخفی می‌شود.
- درخواست تحویل از Middleware موجود `idempotency:delivery.request` عبور می‌کند.
- پاسخ از `CustomerReadPresenter` عبور می‌کند و Model خام منتشر نمی‌شود.
- پیام خام Exception یا LogicException به مشتری نمایش داده نمی‌شود.

## پاسخ‌های خطا

- `CUSTODY_NOT_FOUND` — HTTP 404
- `DELIVERY_NOT_FOUND` — HTTP 404
- `DELIVERY_NOT_ALLOWED` — HTTP 409

## فیلدهای ممنوع در پاسخ مشتری

- `user_id`
- `custody_asset_id`
- `external_product_id`
- `product_code`
- `barcode`
- `metadata`
- `receiver_name`
- `receiver_identifier`
- `approved_by`
- `delivered_by`

## مرز دامنه

این Stage هیچ‌یک از موارد زیر را تغییر نمی‌دهد:

- قوانین تحویل
- State Machine موجود
- Kimia Write
- Ledger
- Wallet
- Settlement
- مدل مالی Custody

عملیات ثبت درخواست فقط از `DeliveryService::request()` موجود استفاده می‌کند.

## تست‌ها

`CustomerCustodyDeliveryContractTest` کنترل می‌کند:

- وجود Routeهای نسخه‌دار
- استفاده از UUID عمومی
- اعمال Ownership در Query
- وجود Idempotency Middleware
- عدم افشای فیلدهای حساس
- عدم نمایش متن خام Exception
- وجود Error Codeهای پایدار

## ریسک باقی‌مانده

Agent محلی روی نسخه قدیمی Bootstrap قرار دارد و Self-update آن Branch قدیمی را انتظار دارد. اعتبار اصلی این Stage، GitHub Actions روی SHA خود PR است تا زمانی که Agent Bootstrap در Stage مستقل اصلاح شود.
