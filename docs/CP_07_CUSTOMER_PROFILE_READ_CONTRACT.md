# CP-07 — Customer Profile Read Contract

## وضعیت

پیاده‌سازی واقعی روی شاخه `work/customer-profile-read-contract`.

## هدف

ارائه پروفایل فقط‌خواندنی، نسخه‌دار و امن برای مشتری احراز هویت‌شده، بدون انتشار شناسه‌های داخلی، داده‌های Kimia، رمز، Token یا اطلاعات هویتی حساس.

## مسیر

- `GET /api/v1/customer/profile`

## پاسخ مجاز

- `first_name`
- `last_name`
- `display_name`
- `mobile`
- `mobile_verified`
- `is_active`
- `roles`
- `last_login_at`

## فیلدهای خارج از Contract

- `password`
- `remember_token`
- `account_id`
- `group_id`
- `national_code`
- `email`
- Personal Access Tokens
- شناسه‌ها یا داده‌های Kimia

## امنیت

- مسیر زیر `auth:sanctum` و `role:customer` قرار دارد.
- پاسخ با `CustomerApiResponse` استاندارد ساخته می‌شود.
- مدل User به‌صورت مستقیم Serialize نمی‌شود.
- هیچ `user_id` از Frontend دریافت نمی‌شود؛ هویت فقط از کاربر احراز هویت‌شده گرفته می‌شود.

## مرز مرحله

این Stage عمداً موارد زیر را پیاده‌سازی نمی‌کند:

- ویرایش پروفایل
- تغییر شماره موبایل
- مدیریت دستگاه‌ها و نشست‌ها
- خروج از سایر دستگاه‌ها
- تکمیل `verifyOtp` یا `logout`
- KYC

دلیل: قرارداد و رفتار اجرایی این موارد هنوز در Backend فعلی تثبیت نشده است و ساخت آن‌ها بدون Ground Truth مجاز نیست.

## تست

`CustomerProfileReadContractTest` کنترل می‌کند:

- وجود Route نسخه‌دار و Customer-scoped
- استفاده از Envelope استاندارد
- وجود فقط فیلدهای مجاز
- عدم افشای فیلدهای حساس و داخلی
