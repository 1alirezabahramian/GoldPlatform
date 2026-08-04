# AP-07 — Roles & Permissions Read Foundation

## هدف
ایجاد API فقط‌خواندنی و نسخه‌دار برای مشاهده Roleها، Permissionها و ماتریس دسترسی واقعی Spatie، بدون هیچ عملیات تخصیص یا تغییر دسترسی.

## Endpointها
- `GET /api/v1/admin/roles`
- `GET /api/v1/admin/permissions`
- `GET /api/v1/admin/access-matrix`

## Permission
- `roles-permissions.view`

## خروجی Role
- id
- name
- guard_name
- users_count
- permissions

## خروجی Permission
- id
- name
- guard_name
- roles_count
- classification

## طبقه‌بندی Permission
طبقه‌بندی فقط بر اساس نام‌های موجود در Catalog انجام می‌شود و رفتار جدیدی ایجاد نمی‌کند:
- `access`
- `read`
- `write`
- `approval`
- `system`
- `other`

## مرز ایمنی
- بدون Create/Update/Delete
- بدون assign/sync/revoke
- بدون نمایش کاربران هر Role
- بدون نمایش Direct Permission کاربران
- بدون Migration
- بدون Kimia/Wallet/Ledger/Settlement
- بدون Tenant/Branch assumption

## تست‌های مورد انتظار
- دسترسی فقط Admin دارای Permission
- جلوگیری از دسترسی Operator
- نمایش Roleها و Permissionهای واقعی
- شمارش کاربران و Roleها
- ماتریس دسترسی
- نبود Endpointهای Write
