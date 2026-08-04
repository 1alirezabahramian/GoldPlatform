# AP-04 — User Management Read Foundation

## هدف
ایجاد قرارداد فقط‌خواندنی و امن برای فهرست کاربران پنل مدیریت، بر پایه فیلدها و روابط موجود پروژه.

## Ground Truth استفاده‌شده
- `User` دارای نام، موبایل، وضعیت فعال، تأیید موبایل، آخرین ورود و ارتباط با `UserGroup` است.
- `User` از Spatie `HasRoles` استفاده می‌کند.
- `UserGroup` دارای عنوان و وضعیت فعال است.
- مدل یا وضعیت اجرایی KYC در شاخه بررسی‌شده پیدا نشد؛ بنابراین KYC در این مرحله نمایش داده نمی‌شود.

## Endpoint
- `GET /api/v1/admin/users`

## Permission
- `users.view`

## خروجی مجاز
- شناسه داخلی کاربر برای عملیات داخلی Admin
- نام نمایشی
- موبایل ماسک‌شده
- وضعیت فعال
- تأیید موبایل
- آخرین ورود
- تاریخ ایجاد
- گروه مشتری: شناسه، عنوان و وضعیت فعال
- نام Roleها

## فیلدهای ممنوع
- national_code
- password
- remember_token
- account_id
- email
- tokenها
- Permissionهای مستقیم
- اطلاعات Wallet/Ledger/Kimia

## فیلترهای محدود
- `status=active|inactive`
- `mobile_verified=true|false`
- `group_id=<integer>`
- `role=<existing role name>`
- `search=<name or mobile>` با حداکثر طول 80
- `per_page` بین 1 تا 50

## مرز مرحله
- بدون عملیات فعال/غیرفعال‌کردن
- بدون تغییر گروه
- بدون تخصیص Role/Permission
- بدون KYC
- بدون Migration
- بدون Tenant/Branch assumption
- بدون منطق مالی یا Kimia Write

## تست‌های مورد انتظار
- Authentication و Permission
- Pagination
- فیلترهای محدود
- ماسک موبایل
- عدم افشای فیلدهای حساس
- جلوگیری از دسترسی Operator
