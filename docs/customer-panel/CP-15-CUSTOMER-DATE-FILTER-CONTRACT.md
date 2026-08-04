# CP-15 — Customer Date Filter Contract

## هدف
افزودن فیلتر بازه زمانی امن و یکسان برای فهرست سفارش‌ها، امانات و درخواست‌های تحویل مشتری.

## قرارداد
- پارامترهای اختیاری `from` و `to`
- فرمت ورودی: `YYYY-MM-DD`
- `to` باید مساوی یا بعد از `from` باشد
- فیلتر فقط روی `created_at` اعمال می‌شود
- فیلتر پس از Ownership scope اعمال می‌شود
- مقادیر فعال در `meta.filters.from` و `meta.filters.to` بازگردانده می‌شوند

## مرزهای ایمنی
- بدون Migration
- بدون تغییر Rule مالی
- بدون تغییر State Machine
- بدون Kimia Read/Write
- بدون پذیرش نام ستون یا SQL از Frontend
- تاریخ محلی یا شمسی به‌عنوان داده اصلی API استفاده نمی‌شود

## تست
`CustomerDateFilterContractTest` اعتبارسنجی ISO، استفاده محدود از `created_at` و عدم استفاده از `whereRaw` را کنترل می‌کند.

## اعتبارسنجی مستقل
این Stage پس از Merge شدن CP-14 روی Branch مبنای اصلی قرار گرفته و باید شش Gate استاندارد CI را روی SHA مستقل خود با موفقیت طی کند.
