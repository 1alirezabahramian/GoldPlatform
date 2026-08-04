# CP-18 — Customer Contract Regression Gate

## هدف

قفل‌کردن قرارداد نهایی Backend و Frontend برای Customer API و جلوگیری از اختلاف میان کد واقعی، OpenAPI و رفتار امنیتی پاسخ‌ها.

## پوشش

- Pagination: `page` و `per_page`
- مقدار پیش‌فرض واقعی `per_page=25`
- فیلتر وضعیت
- مرتب‌سازی محدود `newest|oldest`
- بازه تاریخ ISO با `from` و `to`
- Header قابل‌پیگیری `X-Request-ID`
- Header جلوگیری از Cache پاسخ حساس
- عدم انتشار شناسه‌ها و اصطلاحات داخلی Kimia

## اصلاح واقعی

OpenAPI پیش از این مقدار پیش‌فرض `per_page` را 20 اعلام می‌کرد، در حالی که Backend مقدار 25 را اجرا می‌کند. این اختلاف در CP-18 اصلاح و با تست Regression قفل شد.

## مرزهای ایمنی

- بدون Migration
- بدون تغییر Rule مالی
- بدون تغییر Wallet، Ledger، Settlement یا State Machine
- بدون Kimia Read/Write
- بدون افزودن Endpoint عملیاتی جدید

## تست

`CustomerContractRegressionGateTest` هماهنگی OpenAPI با Request، Response و Middlewareهای واقعی را کنترل می‌کند.

## اعتبارسنجی مستقل

این PR پس از Merge شدن CP-17 روی شاخه اصلی Customer Panel منتقل شد تا تمام Gateهای CI به‌صورت مستقل روی قرارداد نهایی CP-18 اجرا شوند.
