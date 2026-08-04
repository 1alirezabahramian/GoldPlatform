# CP-14 — Customer Sort Contract

## هدف

یکسان‌سازی مرتب‌سازی فهرست‌های سفارش، امانت و تحویل مشتری با قرارداد محدود و امن.

## قرارداد

پارامتر اختیاری:

- `sort=newest` — پیش‌فرض، جدیدترین رکوردها ابتدا
- `sort=oldest` — قدیمی‌ترین رکوردها ابتدا

هر مقدار دیگری با Error Contract استاندارد Customer API و وضعیت 422 رد می‌شود.

## پیاده‌سازی

- Validation مرکزی در `CustomerPaginationRequest`
- نگاشت امن به `asc` یا `desc`
- مرتب‌سازی فقط روی ستون داخلی ثابت `id`
- عدم پذیرش نام ستون یا SQL از Frontend
- اعمال یکسان روی Orders، Custodies و Deliveries
- بازگرداندن مقدار فعال در `meta.filters.sort`

## مرزهای ایمنی

- بدون Migration
- بدون تغییر State Machine
- بدون تغییر Wallet، Ledger یا Settlement
- بدون Kimia Read/Write
- بدون Rule مالی جدید

## Validation

پس از Merge شدن CP-13، این PR روی شاخه مبنای اصلی Retarget شد و باید همه Gateهای مستقل CI را روی همین قرارداد اجرا کند.
