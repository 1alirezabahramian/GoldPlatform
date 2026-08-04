# AP-10 — Settlement Management Read Foundation

## هدف
ایجاد API فقط‌خواندنی و نسخه‌دار برای مشاهده Settlementها، Timeline پردازش و ارتباط با Order/Trade، بدون Retry، Approval یا Kimia Write.

## Ground Truth
- وضعیت‌های واقعی Settlement: `pending`, `processing`, `completed`, `failed`, `cancelled`.
- Settlement دارای ارتباط واقعی با Order و Trade است.
- فیلدهای حساس مدل شامل `kimia_reference`, `idempotency_key`, `metadata`, `failure_reason` و `financial_transaction_id` هستند.

## Endpointها
- `GET /api/v1/admin/settlements`
- `GET /api/v1/admin/settlements/{settlement}`

## Permissionها
- `settlements.view`
- `settlements.detail.view`

## فیلترها
- `status`
- `asset_type`
- `order_id`
- `trade_id`
- `per_page` بین 1 و 50

## خروجی امن
- UUID عمومی
- Order ID و Trade ID داخلی
- وضعیت
- نوع دارایی
- مقدار ذخیره‌شده
- وجود خطا و دسته کنترل‌شده خطا
- زمان شروع پردازش، تکمیل و شکست
- Timeline واقعی
- خلاصه Order و Trade

## فیلدهای حذف‌شده
- `kimia_reference`
- `idempotency_key`
- `metadata`
- متن خام `failure_reason`
- `financial_transaction_id`

## دسته‌بندی خطا
دسته‌بندی صرفاً برای نمایش امن از متن ذخیره‌شده استخراج می‌شود:
- `timeout`
- `connection`
- `validation`
- `processing`

این دسته‌بندی هیچ Rule مالی یا رفتار Retry ایجاد نمی‌کند.

## مرز ایمنی
- بدون Retry
- بدون Approve/Cancel
- بدون تغییر State
- بدون Migration
- بدون Kimia Call یا Write
- بدون محاسبه مالی جدید
- بدون Tenant/Branch assumption

## تست‌های مورد انتظار
- قرارداد نسخه‌دار فهرست و جزئیات
- جلوگیری از افشای فیلدهای حساس
- جلوگیری از دسترسی Operator
- رد status نامعتبر
- محدودیت Pagination
