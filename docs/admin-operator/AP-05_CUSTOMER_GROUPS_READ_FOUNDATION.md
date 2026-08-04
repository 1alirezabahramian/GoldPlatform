# AP-05 — Customer Groups Read Foundation

## هدف
ایجاد API فقط‌خواندنی و نسخه‌دار برای گروه‌های مشتری و سیاست‌های معاملاتی ذخیره‌شده مرتبط، بدون تغییر هیچ قانون مالی.

## Ground Truth
- `UserGroup` دارای عنوان، اولویت، وضعیت فعال و کاربران مرتبط است.
- `CustomerTradingPolicy` با `user_group_id` به گروه متصل است.
- چون یکتابودن Policy در Schema این مرحله تأیید نشد، خروجی به‌صورت آرایه `policies` است.
- مقادیر Policy فقط از دیتابیس خوانده می‌شوند و هیچ مقدار پیش‌فرض یا محاسبه جدیدی ساخته نمی‌شود.

## Endpoint
- `GET /api/v1/admin/customer-groups`

## Permission
- `customer-groups.view`

## خروجی
- id
- title
- priority
- is_active
- users_count
- created_at
- policies ذخیره‌شده شامل فیلدهای صریح مدل، بدون metadata

## فیلترها
- `status=active|inactive`
- `search`
- `per_page` بین 1 و 50

## مرز ایمنی
- بدون Create/Update/Delete
- بدون تغییر کمیسیون، اعتبار، Freeze یا Anti-scalping
- بدون metadata
- بدون Migration
- بدون Kimia Call/Write
- بدون Tenant/Branch assumption

## تست‌های مورد انتظار
- Authentication و Permission
- جلوگیری از دسترسی Operator
- Pagination و فیلتر محدود
- شمارش کاربران
- نمایش Policy واقعی
- عدم نمایش metadata
