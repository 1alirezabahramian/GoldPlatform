# OP-04 — Operational Dashboard Integration

## هدف

اتصال پنل Admin و Operator به داده‌های عملیاتی واقعی Backend، بدون ساخت KPI مالی یا محاسبه تأییدنشده.

## APIها

- `GET /api/v1/admin/dashboard`
- `GET /api/v1/operator/dashboard`

## داده‌های Admin

- تعداد سفارش‌های باز
- تعداد تحویل‌های فعال
- تعداد Settlementهای شکست‌خورده
- تعداد امانات فعال
- تعداد Outboxهای پردازش‌نشده، در صورت وجود ستون واقعی `processed_at`
- Preview محدود صف سفارش و تحویل

## داده‌های Operator

- سفارش‌های Pending و Approved
- تحویل‌های Requested و Ready
- Preview محدود صف سفارش و تحویل

## مرزهای ایمنی

- بدون Revenue، Profit، Gold Valuation یا KPI مالی
- بدون اطلاعات هویتی مشتری یا گیرنده
- بدون Kimia reference یا payload خام
- بدون تغییر وضعیت سفارش، تحویل یا Settlement
- مقدار `null` به معنی در دسترس نبودن منبع شمارش است و نباید در Frontend به صفر تبدیل شود

## Frontend

Dashboardها فقط مقادیر دریافتی Backend را نمایش می‌دهند و از Queue Preview برای هدایت اپراتور استفاده می‌کنند. Frontend هیچ شمارش، جمع مالی یا وضعیت دامنه‌ای تولید نمی‌کند.
