# FE-01 — Customer Frontend Foundation

## وضعیت

Implemented — Awaiting CI and Merge

## هدف

آغاز واقعی فاز پنل مشتری با پایه‌ای مستقل از فریم‌ورک، مطابق اصل `Complex Backend — Simple Frontend`.

## تغییرات

- ایجاد `frontend/customer-foundation/tokens.css`
- ایجاد `frontend/customer-foundation/terminology.fa.json`
- ایجاد `frontend/customer-foundation/README.md`
- ایجاد `CustomerFrontendFoundationTest`

## قراردادهای تثبیت‌شده

- RTL و فارسی
- حداقل Touch Target و Focus قابل‌مشاهده
- متغیرهای Semantic برای White-label
- واژگان انسانی مشتری
- جداسازی دارایی مالی از امانت فیزیکی
- ممنوعیت نمایش اصطلاحات داخلی Kimia به‌عنوان عنوان اصلی UI
- حفظ Decimal به‌صورت رشته در مرز API
- اتصال آینده به OpenAPI موجود Customer V1

## موارد عمداً انجام‌نشده

- انتخاب React/Vue/Next/Nuxt
- ایجاد Application Shell
- ساخت صفحه Dashboard
- افزودن Business Rule مالی
- تغییر Backend API
- تغییر Kimia، Ledger، Wallet یا Settlement

## تست

`backend/tests/Unit/Architecture/CustomerFrontendFoundationTest.php`

این تست وجود فایل‌ها، RTL، Accessibility، White-label hooks و واژگان انسانی مشتری را Guard می‌کند.

## ریسک باقی‌مانده

فناوری اصلی Frontend هنوز در منابع پروژه تأیید نشده است. Stage بعدی که Application Shell واقعی می‌سازد، نیازمند تصمیم معماری صریح مالک پروژه است.
