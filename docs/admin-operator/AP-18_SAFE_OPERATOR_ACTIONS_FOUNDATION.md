# AP-18 — Safe Operator Actions Foundation

## هدف
نسخه‌دار کردن فقط عملیات اپراتوری‌ای که Service معتبر و Transition کنترل‌شده دارند.

## Ground Truth
`DeliveryService` عملیات `approve`، `markReady` و `deliver` را با Transaction، Row Lock و کنترل وضعیت انجام می‌دهد. مسیر معتبر مشابهی برای Approve/Reject سفارش در این مرحله تأیید نشد.

## Endpointهای فعال
- `POST /api/v1/operator/deliveries/{deliveryRequest}/approve`
- `POST /api/v1/operator/deliveries/{deliveryRequest}/ready`
- `POST /api/v1/operator/deliveries/{deliveryRequest}/complete`

## کنترل‌ها
- Role: `operator|admin`
- Permission مستقل برای هر عملیات
- Idempotency-Key اجباری
- اجرای Business Transition فقط از `DeliveryService`
- Audit و Outbox در همان Transaction
- پاسخ نسخه‌دار و Presenter صریح
- عدم نمایش نام و شناسه گیرنده در پاسخ
- خطای Transition نامعتبر با کد عمومی و بدون متن داخلی

## عملیات عمداً بسته
- Approve/Reject/Cancel سفارش
- Settlement Retry
- Kimia Write
- تغییر مستقیم Custody یا Wallet

## تست‌ها
- منع دسترسی ناشناس
- منع Operator فاقد Permission
- نبود مسیر Approve سفارش
- الزام Idempotency برای تکمیل تحویل

## ریسک باقی‌مانده
- تست کامل Transitionهای موفق نیازمند Fixture معتبر Custody/Delivery است.
- Routeهای Legacy اپراتور هنوز وجود دارند و باید در مرحله تثبیت Deprecated شوند.
- متن کامل Audit داخلی شامل before/after است و فقط API امن Audit باید برای UI استفاده شود.
