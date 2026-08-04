# CP-12 — Customer Pagination Contract

## هدف

یکسان‌سازی Pagination برای فهرست‌های سفارش، امانت و تحویل مشتری، بدون تغییر Rule مالی یا داده دامنه.

## Contract

پارامترهای ورودی:

- `page`: عدد صحیح، حداقل 1
- `per_page`: عدد صحیح، حداقل 1، حداکثر 50
- مقدار پیش‌فرض `per_page`: 25

## Endpointهای تحت پوشش

- `GET /api/v1/customer/orders`
- `GET /api/v1/customer/custodies`
- `GET /api/v1/customer/deliveries`

## Meta خروجی

- `current_page`
- `per_page`
- `total`
- `last_page`
- `has_more`

## امنیت و کارایی

- سقف 50 از پاسخ‌های بسیار بزرگ جلوگیری می‌کند.
- Ownership Queryهای موجود بدون تغییر باقی مانده است.
- ترتیب فعلی `latest('id')` تغییر نکرده است.
- هیچ شناسه داخلی یا Rule مالی جدیدی وارد پاسخ نشده است.

## تست

`CustomerPaginationContractTest` پیش‌فرض، حدود ورودی، استفاده مشترک سه Endpoint و ثبات Meta را کنترل می‌کند.
