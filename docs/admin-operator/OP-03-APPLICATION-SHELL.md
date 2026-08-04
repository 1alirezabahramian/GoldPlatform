# OP-03 — Admin & Operator Application Shell

## هدف
ایجاد پوسته مستقل Nuxt برای پنل‌های Admin و Operator بر پایه قرارداد Session Bootstrap مرحله OP-02.

## قابلیت‌ها
- رابط فارسی و RTL
- Session Store مبتنی بر `/api/v1/{panel}/bootstrap`
- تشخیص پنل Admin یا Operator
- Route Guard سراسری
- Sidebar تولیدشده از Navigation واقعی Backend
- صفحه‌های امن 401، 403 و خطای موقت
- Logout و پاک‌سازی Session محلی
- Landing Page مستقل برای Admin و Operator

## مرزهای امنیتی
- Navigation فقط برای UX است و جایگزین Authorization سمت Backend نیست.
- هیچ Permission، Role یا Capability در Frontend ساخته یا تخصیص داده نمی‌شود.
- هیچ محاسبه مالی، Wallet/Ledger mutation یا Kimia call وجود ندارد.
- Cookie Session با `credentials: include` ارسال می‌شود و Token ثابت ذخیره نمی‌شود.
- پاسخ خطا جزئیات داخلی Backend را نمایش نمی‌دهد.

## تست
`frontend-admin/tests/application-shell.test.mjs` قرارداد Route Guard و Navigation را بررسی می‌کند.

## موارد نیازمند تست اجرایی
- `npm install`
- `npm run test`
- `npm run typecheck`
- `npm run build`
- اتصال مرورگر به Sanctum و Bootstrap واقعی
