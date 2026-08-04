# CP-09 — Customer Bootstrap Contract

## وضعیت

پیاده‌سازی واقعی روی شاخه `work/customer-bootstrap-contract`.

## هدف

ارائه یک Endpoint فقط‌خواندنی برای راه‌اندازی اولیه Frontend مشتری، بدون انتقال Rule مالی، Kimia Rule یا Permission فرضی به رابط کاربری.

## Endpoint

`GET /api/v1/customer/bootstrap`

## پاسخ

Bootstrap فقط قراردادهای موجود در کد را منتشر می‌کند:

- نسخه API: `v1`
- Timezone داده اصلی: `UTC`
- نوع رویدادهای Activity Timeline
- وضعیت‌های Order
- وضعیت‌های Custody
- وضعیت‌های Delivery
- مشخص بودن Terminal بودن هر وضعیت بر اساس Enum موجود

## امنیت

Endpoint زیر این Middlewareها قرار دارد:

- `auth:sanctum`
- `throttle:customer`
- `role:customer`

پاسخ از `CustomerApiResponse` استفاده می‌کند.

## موارد عمداً خارج از Contract

- Fee و Commission
- Trading Limits
- Credit Limits
- Anti-speculation Rule
- Wallet Rule
- Ledger Rule
- Kimia identifiers و Action Codes
- قابلیت‌هایی که فقط از روی Route حدس زده شوند
- Notification Rule

## تست

`CustomerBootstrapContractTest` کنترل می‌کند:

- نسخه‌دار و Customer-scoped بودن Route
- استفاده از Enumها و Contractهای موجود
- عدم انتشار Rule مالی یا Kimia
- استفاده از Envelope استاندارد مشتری

## تغییرات دامنه

این Stage هیچ مدل، Migration، State Machine، Rule مالی یا Kimia Write را تغییر نمی‌دهد.
