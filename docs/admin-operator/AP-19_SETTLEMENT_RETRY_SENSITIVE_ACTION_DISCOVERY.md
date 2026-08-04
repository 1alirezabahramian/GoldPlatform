# AP-19 — Settlement Retry & Sensitive Action Discovery

## نتیجه

در شاخه بررسی‌شده، Service یا Workflow تأییدشده‌ای برای Retry، Approve، Cancel یا Kimia Write در Settlement پیدا نشد. بنابراین هیچ Endpoint نوشتنی فعال نشده است.

## API

`GET /api/v1/admin/settlement-actions/overview`

Permission: `settlement-actions.view`

## وضعیت قابلیت‌ها

- Settlement Retry: غیرفعال
- Settlement Approval: غیرفعال
- Settlement Cancellation: غیرفعال
- Kimia Write: غیرفعال

## کنترل‌های اجباری برای مرحله آینده

- Idempotency
- Row locking
- Audit log
- Outbox
- Permission مستقل
- Approval workflow
- قرارداد قطعی Kimia Write

## مرزهای ایمنی

- هیچ Retry اجرا نمی‌شود.
- هیچ وضعیت Settlement تغییر نمی‌کند.
- هیچ درخواست شبکه‌ای به Kimia ارسال نمی‌شود.
- هیچ Wallet یا Ledger تغییر نمی‌کند.
- Endpointهای نوشتنی عمداً وجود ندارند.

## تست‌ها

- Admin مجاز می‌تواند Capability Overview را بخواند.
- Operator دسترسی ندارد.
- مسیر Retry پاسخ 404 می‌دهد.

## ریسک باقی‌مانده

قبل از فعال‌سازی Retry باید Service معتبر، Transitionهای مجاز، Idempotency پایدار، رفتار Kimia در تکرار درخواست، Approval Workflow و Rollback دقیق تأیید شوند.
