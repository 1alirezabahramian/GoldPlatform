# AP-06 — Customer Policy Approval Foundation

## هدف
ایجاد Workflow کنترل‌شده برای پیشنهاد و بررسی تغییرات CustomerTradingPolicy، بدون اعمال خودکار تغییرات مالی.

## Workflow
- draft
- submitted
- approved
- rejected
- applied (رزروشده برای مرحله آینده)

## نکته ایمنی اصلی
در این مرحله هیچ Endpoint یا Service برای `apply` وجود ندارد. وضعیت approved فقط به معنی تأیید درخواست برای اعمال کنترل‌شده در مرحله آینده است و CustomerTradingPolicy فعال را تغییر نمی‌دهد.

## Endpointها
- `GET /api/v1/admin/customer-policy-change-requests`
- `POST /api/v1/admin/customer-policy-change-requests`
- `POST /api/v1/admin/customer-policy-change-requests/{reference}/submit`
- `POST /api/v1/admin/customer-policy-change-requests/{reference}/approve`
- `POST /api/v1/admin/customer-policy-change-requests/{reference}/reject`

## Permissionها
- customer-policy-changes.view
- customer-policy-changes.create
- customer-policy-changes.submit
- customer-policy-changes.approve
- customer-policy-changes.reject

## کنترل‌ها
- UUID عمومی
- Validation محدود به فیلدهای واقعی CustomerTradingPolicy
- Row Lock برای Transitionها
- Audit برای draft/submit/approve/reject
- Outbox برای رویدادهای Workflow
- Idempotency Middleware روی عملیات Write
- دلیل اجباری برای Draft
- توضیح بررسی اجباری برای Reject

## خارج از محدوده
- Apply روی Policy فعال
- تغییر Wallet/Ledger/Settlement
- Kimia Write
- Approval چندمرحله‌ای یا Four-eyes قطعی
- Tenant/Branch scope
- Notification delivery provider

## تست‌ها
- Approval نباید Policy فعال را تغییر دهد
- Operator نباید دسترسی داشته باشد
- Reject بدون review_note باید 422 شود

## ریسک باقی‌مانده
قانون نهایی Apply، جداسازی پیشنهاددهنده از تأییدکننده، تعداد تأییدکنندگان و Rollback باید پیش از مرحله Apply توسط مالک پروژه تأیید شود.
