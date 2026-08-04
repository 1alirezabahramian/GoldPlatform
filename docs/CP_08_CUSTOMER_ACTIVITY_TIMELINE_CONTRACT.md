# CP-08 — Customer Activity Timeline Contract

## وضعیت

پیاده‌سازی واقعی روی شاخه `work/customer-activity-timeline-contract`.

## هدف

ارائه Timeline فقط‌خواندنی و صفحه‌بندی‌شده از فعالیت‌های واقعی مشتری، بدون ساخت Event جدید و بدون افشای اطلاعات داخلی.

## Endpoint

`GET /api/v1/customer/activities`

## منابع داده

- تغییرات سفارش‌های متعلق به مشتری
- تغییرات امانات متعلق به مشتری
- تغییرات درخواست‌های تحویل متعلق به مشتری

## فیلترها

- `page`
- `per_page` با سقف ۵۰
- `event_type` فقط یکی از:
  - `order_status`
  - `custody_status`
  - `delivery_status`

## امنیت

- تمام Queryها با `user_id` کاربر احراز هویت‌شده محدود می‌شوند.
- پاسخ‌ها از `CustomerReadPresenter` عبور می‌کنند.
- Model خام، `metadata`، شناسه داخلی، اطلاعات Kimia و اطلاعات هویتی گیرنده منتشر نمی‌شوند.
- Endpoint زیر `auth:sanctum`، `throttle:customer` و نقش `customer` قرار دارد.

## Pagination

Page-based pagination با فیلدهای زیر:

- `current_page`
- `per_page`
- `total`
- `last_page`
- `has_more`

## مرز دامنه

این Stage موارد زیر را تغییر نمی‌دهد:

- Order State Machine
- Custody State Machine
- Delivery State Machine
- Ledger
- Wallet
- Settlement
- Kimia Write
- Notification Rule

## تست‌ها

`CustomerActivityContractTest` کنترل می‌کند:

- Route نسخه‌دار و نقش مشتری
- Ownership روی هر سه منبع داده
- محدودیت Pagination
- فیلتر معتبر Event Type
- استفاده از Presenter امن
- عدم Serialize مستقیم Model
