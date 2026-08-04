# AP-17 — Reports & Export Contract Foundation

## هدف
ایجاد API فقط‌خواندنی برای فهرست گزارش‌های قابل تولید از داده‌های واقعی پروژه و وضعیت قابلیت‌های Export، بدون محاسبه مالی تأییدنشده.

## Endpoint
- `GET /api/v1/admin/reports/catalog`

## Permission
- `reports.view`

## گزارش‌های قابل تعریف بر پایه داده موجود
- سفارش‌ها
- معاملات
- تسویه‌ها
- امانات
- تحویل‌ها
- کاربران و گروه‌ها
- Audit Log
- Outbox

## گزارش‌های فعلاً پشتیبانی‌نشده
- Revenue
- سود و زیان
- Gold Holdings Valuation
- گزارش حسابداری Kimia
- گزارش مالی تجمیعی Tenant/Branch

## Export
در Composer فعلی هیچ کتابخانه Excel یا PDF نصب نیست. بنابراین Excel، PDF و اجرای Export در این مرحله فعال نیستند.

## مرز ایمنی
- بدون Export واقعی
- بدون ساخت فایل
- بدون محاسبه Revenue
- بدون جمع‌بندی موجودی مالی
- بدون تغییر داده
- بدون افزودن Package یا Migration

## تست
`AdminReportCatalogReadContractTest` قرارداد نسخه‌دار، دسترسی Admin، منع Operator و عدم ادعای پشتیبانی Excel/PDF را پوشش می‌دهد.

## وضعیت صداقت
کد و تست ثبت شده‌اند، اما در این محیط اجرا نشده‌اند. PASS نهایی منوط به CI است.
