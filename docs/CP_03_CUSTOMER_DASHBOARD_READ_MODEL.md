# CP-03 — Customer Dashboard Read Model

**Status:** Implemented; completion requires green CI evidence  
**Branch:** `work/customer-panel-contract-foundation`  
**Pull Request:** #87

## هدف

تبدیل Dashboard مشتری از مجموعه شمارنده‌های ساده به یک Read Model عملیاتی، بدون تغییر منطق مالی، بدون Kimia Write و بدون Migration.

## خروجی Dashboard

`GET /api/v1/customer/dashboard`

پاسخ شامل این بخش‌ها است:

- `assets`: Snapshot دقیق دارایی‌ها از Ledger و Reservation
- `summary`: تعداد سفارش فعال، امانات، درخواست‌های تحویل و تحویل‌های آماده
- `highlights`: حداکثر سه سفارش فعال و سه تحویل آماده
- `recent_activity`: حداکثر هشت فعالیت آخر از سفارش، امانت و تحویل

## قواعد ایمنی

- فقط داده‌های کاربر احراز هویت‌شده خوانده می‌شوند.
- شناسه داخلی دیتابیس، شناسه Kimia، metadata و اطلاعات هویتی گیرنده منتشر نمی‌شوند.
- وضعیت‌های موجود Backend بدون ساخت وضعیت یا ترجمه مالی جدید بازگردانده می‌شوند.
- مسیرهای Legacy تغییر نکرده‌اند.
- هیچ مقدار مالی با float محاسبه نمی‌شود.

## Performance

ریسک N+1 در Balance Snapshot شناسایی و رفع شد.

Dashboard روابط زیر را برای همه حساب‌ها گروهی بارگذاری می‌کند:

- Ledger entries
- Active balance reservations

فرمول محاسبه Balance تغییر نکرده است. فقط همان محاسبه با روابط از قبل بارگذاری‌شده اجرا می‌شود.

Query Budget این Read Model حداکثر ۱۰ Query است و تعداد Query با افزایش تعداد حساب‌های Money، Gold، Coin و Currency رشد نمی‌کند.

## تست‌ها

- ساختار Dashboard
- عدم افشای فیلدهای داخلی
- Query Budget با چند حساب دارایی
- Regression قرارداد CP-01 و CP-02

## موارد عمداً خارج از محدوده

- ترجمه نهایی وضعیت‌ها برای رابط فارسی
- قرارداد نمایش بدهکار و بستانکار
- تبدیل نمایشی IRR به IRT
- Public Reference سفارش
- KYC و نشست‌های امنیتی
- عملیات Write سفارش، تبدیل یا تحویل

## Completion Gate

این Stage فقط پس از موفقیت Workflowهای GitHub Actions برای SHA نهایی PASS محسوب می‌شود.
