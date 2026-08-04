# AP-09 — Delivery & Custody Read Foundation

## هدف
ایجاد API فقط‌خواندنی و نسخه‌دار برای امانات فیزیکی و درخواست‌های تحویل، بدون هیچ عملیات تحویل، تبدیل، فروش مجدد یا خروج فیزیکی.

## Endpointها
- `GET /api/v1/admin/custodies`
- `GET /api/v1/admin/custodies/{custodyAsset}`
- `GET /api/v1/admin/deliveries`
- `GET /api/v1/admin/deliveries/{deliveryRequest}`

## Permissionها
- `custodies.view`
- `custodies.detail.view`
- `deliveries.view`
- `deliveries.detail.view`

## Ground Truth
- `CustodyAsset` دارایی فیزیکی مستقل از Wallet/Ledger است.
- وضعیت‌های Custody از Enum موجود خوانده می‌شوند.
- DeliveryRequest شامل شعبه، زمان درخواستی و timestampهای واقعی Workflow است.
- Timeline فقط از timestampهای ذخیره‌شده ساخته می‌شود.

## خروجی امن
- UUID عمومی
- نوع، عنوان، مقدار، وزن و عیار
- بارکد و شعبه ذخیره‌شده
- وضعیت و زمان‌های واقعی
- شمارش و خلاصه درخواست‌های تحویل

## فیلدهای حذف‌شده
- metadata
- receiver_name
- receiver_identifier
- status_reason
- اطلاعات هویتی کاربر
- اطلاعات Wallet/Ledger/Kimia

## مرز ایمنی
- بدون Approve/Ready/Deliver
- بدون Convert/Resell/Cancel
- بدون Migration
- بدون Kimia Call یا Write
- بدون محاسبه مالی جدید
- بدون Tenant/Branch assumption

## تست‌های مورد انتظار
- قرارداد فهرست و جزئیات
- عدم نمایش metadata و هویت گیرنده
- جلوگیری از دسترسی Operator
- Pagination محدود
- حفظ Routeهای نسخه‌دار قبلی
