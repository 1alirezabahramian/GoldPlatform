# AP-14 — Branch Management Read Foundation

## نتیجه Discovery
در شاخه فعلی هیچ Model، Migration یا جدول مستقل و تأییدشده‌ای برای Branch پیدا نشد. تنها Ground Truth موجود، فیلد `branch_code` در `custody_assets` و `delivery_requests` است.

## Endpoint
- `GET /api/v1/admin/branches`

## Permission
- `branches.view`

## رفتار
API کدهای شعبه موجود در داده‌های واقعی امانت و تحویل را جمع‌آوری و برای هر کد، تعداد امانات و درخواست‌های تحویل را گزارش می‌کند.

## اعلام صریح قابلیت‌های ناموجود
- `branch_entity_supported=false`
- `tenant_scope_supported=false`
- `user_branch_assignment_supported=false`
- `inventory_branch_scope_supported=false`

## مرز ایمنی
- بدون ساخت Branch Entity یا Migration
- بدون Create/Update/Delete
- بدون حدس نام، آدرس یا وضعیت شعبه
- بدون Tenant/White-label assumption
- بدون انتقال موجودی یا تحویل

## تست
Feature Test برای دسترسی Admin، جلوگیری از دسترسی Operator و نمایش Branch Code واقعی نوشته شده است. تست در محیط فعلی اجرا نشده است.
