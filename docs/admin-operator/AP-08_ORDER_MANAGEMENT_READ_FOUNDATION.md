# AP-08 — Order Management Read Foundation

## هدف
ایجاد API فقط‌خواندنی و نسخه‌دار برای فهرست و جزئیات سفارش‌ها، Timeline وضعیت و Summary واقعی Trade/Settlement، بدون تغییر وضعیت سفارش.

## Endpointها
- `GET /api/v1/admin/orders`
- `GET /api/v1/admin/orders/{order}`

## Permissionها
- `orders.view`
- `orders.detail.view`

## فیلترهای فهرست
- `status`
- `type`
- `asset_type`
- `user_id`
- `per_page` بین 1 و 50

## خروجی فهرست
- شناسه سفارش و کاربر
- نوع سفارش و دارایی
- شناسه خارجی دارایی ذخیره‌شده
- مقدار و واحد دارایی
- وضعیت و state version
- وزن و مبالغ ذخیره‌شده
- دلیل وضعیت
- شمارش Trade و Settlement
- زمان ایجاد، به‌روزرسانی و انقضا

## خروجی جزئیات
- Order امن
- Timeline بر پایه timestampهای واقعی مدل
- Trade summary بر پایه رکوردهای واقعی Trade
- Settlement summary بر پایه رکوردهای واقعی Settlement

## فیلدهای حذف‌شده
- metadata
- idempotency_key
- kimia_reference
- failure_reason کامل Settlement
- financial_transaction_id
- اطلاعات هویتی مشتری
- Wallet/Ledger details

## مرز ایمنی
- بدون Approve/Reject/Cancel
- بدون تغییر State
- بدون Migration
- بدون Kimia Call/Write
- بدون محاسبه مالی جدید
- بدون Tenant/Branch assumption

## تست‌ها
- قرارداد نسخه‌دار فهرست
- قرارداد جزئیات و Timeline
- جلوگیری از افشای فیلدهای حساس
- جلوگیری از دسترسی Operator
- محدودیت Pagination
