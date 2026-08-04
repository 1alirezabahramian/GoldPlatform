# CP-13 — Customer Status Filter Contract

## هدف

افزودن فیلتر وضعیت معتبر و امن به فهرست‌های سفارش، امانت و تحویل مشتری، بدون ایجاد وضعیت جدید یا تغییر State Machine.

## Contract

هر سه Endpoint از پارامتر اختیاری `status` پشتیبانی می‌کنند:

- `GET /api/v1/customer/orders?status=...`
- `GET /api/v1/customer/custodies?status=...`
- `GET /api/v1/customer/deliveries?status=...`

اعتبارسنجی هر Endpoint مستقیماً از Enum موجود همان دامنه انجام می‌شود:

- `OrderStatus`
- `CustodyStatus`
- `DeliveryStatus`

## امنیت و مالکیت

فیلتر وضعیت پس از محدودکردن Query به `user_id` کاربر احراز هویت‌شده اعمال می‌شود. هیچ شناسه داخلی Kimia یا Rule مالی وارد پاسخ نشده است.

## پاسخ

Meta پاسخ علاوه بر Pagination شامل فیلتر اعمال‌شده است:

```json
{
  "filters": {
    "status": "pending"
  }
}
```

در نبود فیلتر، مقدار `status` برابر `null` است.

## Validation

وضعیت نامعتبر با Error Contract استاندارد مشتری و HTTP 422 رد می‌شود.

## مرزهای مرحله

- بدون Migration
- بدون تغییر State Machine
- بدون تغییر Wallet/Ledger/Settlement
- بدون Kimia Read/Write
- بدون Rule مالی جدید
