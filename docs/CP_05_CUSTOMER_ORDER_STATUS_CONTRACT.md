# CP-05 — Customer Order Status Contract

## وضعیت

این Stage فقط بخش دارای Ground Truth از قرارداد معاملات را پیاده‌سازی می‌کند: کاتالوگ وضعیت‌های واقعی سفارش.

## Endpoint

`GET /api/v1/customer/order-statuses`

## منبع حقیقت

پاسخ مستقیماً از `App\Enums\OrderStatus` تولید می‌شود.

وضعیت‌های فعلی:

- pending
- approved
- executing
- settling
- completed
- rejected
- expired
- cancelled
- failed

وضعیت‌های terminal طبق متد موجود `OrderStatus::isTerminal()` مشخص می‌شوند.

## مرزهای ایمنی

در این Stage موارد زیر عمداً پیاده‌سازی نشده‌اند:

- Quote و Price Snapshot
- زمان اعتبار قیمت
- کارمزد
- ثبت یا تأیید سفارش نسخه v1
- لغو سفارش
- Public Reference سفارش
- تغییر State Machine
- Kimia Write

این موارد به Rule مالی، رفتار دامنه یا Migration تأییدشده نیاز دارند.

## امنیت

- Sanctum الزامی است.
- نقش customer الزامی است.
- پاسخ فاقد شناسه داخلی، Kimia ID و اطلاعات مالی است.

## تست‌ها

- Authentication
- تطبیق کامل پاسخ با Enum واقعی Backend
- تطبیق terminal flag با State Machine موجود
- ساختار Envelope نسخه v1

## گام بعد

Public Reference سفارش باید در Stage مستقل Audit شود. هیچ Migration یا شناسه جدیدی بدون بررسی مدل، روابط، Route Binding و سازگاری داده‌های موجود اضافه نمی‌شود.
