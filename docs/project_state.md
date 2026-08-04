# GoldPlatform
## Project State

> این فایل شامل تاریخچه وضعیت‌های قبلی پروژه است. آخرین وضعیت معتبر در انتهای فایل ثبت می‌شود.

آخرین بروزرسانی تاریخی پیشین: 2026-07-22

---

# وضعیت‌های تاریخی

محتوای پیشین این فایل به‌عنوان تاریخچه نگهداری شده است.

---

# وضعیت معتبر فعلی — 2026-08-04

## Business Engine — Stage 00

**عنوان:** Baseline Recovery & Domain Safety Gate  
**Branch:** `work/business-engine-stage00`  
**Pull Request:** `#88`  
**Status:** CI PASS / Ready for Review

## اقدامات انجام‌شده

- ایجاد شاخه مستقل برای Business Engine
- افزودن GitHub Actions اختصاصی برای Baseline
- اصلاح خطاهای نحوی قطعی در مسیر قدیمی Kimia
- اصلاح تعریف تکراری `StoreOrderRequest::rules()`
- هماهنگ‌سازی `RegistrationService` با Schema واقعی جدول `users`
- حذف وابستگی تست به فهرست Hard-code شده ارز و سکه
- نگه‌داشتن فقط حساب‌های فعلی ثبت‌نام: `RIAL` و `GOLD18`

## نتیجه Health Gate

```text
Composer validation    PASS
Dependency install     PASS
PHP syntax             PASS
Migration fresh        PASS
Existing test suite    PASS
Live Kimia calls       DISABLED
Kimia write            NOT TESTED / NOT ENABLED
```

## محدودیت این PASS

این نتیجه فقط سلامت Baseline فعلی را روی PHP 8.4 و SQLite آزمایشی تأیید می‌کند.

موارد زیر هنوز تأیید نشده‌اند:

- MySQL integration test
- Docker runtime test
- Kimia live read-only test
- Kimia write operations
- Financial correctness of Wallet/Ledger
- Trading, Settlement, Delivery و Custody engines

## ریسک‌های باقی‌مانده

- چند مسیر موازی و ناسازگار برای Kimia وجود دارد.
- هشدارهای PSR-4 در چند فایل قدیمی وجود دارد.
- Ledger هنوز منبع حقیقت مالی نیست.
- Wallet و Registration همچنان نیازمند طراحی Financial Kernel هستند.
- Coin و Currency باید Dynamic باقی بمانند و Hard-code نشوند.

## مرحله بعد

```text
Stage 01 — Kimia Read-Only Foundation
```

هدف مرحله بعد:

- تعیین تنها مسیر معتبر Kimia Read
- تثبیت Headerها و Configuration
- ساخت Contract Test با HTTP Fake
- پوشش Account Groups، Accounts، Coins، Currencies و Balance
- جلوگیری قطعی از عملیات Write در این Stage
