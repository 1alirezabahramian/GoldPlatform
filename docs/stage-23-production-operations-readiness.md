# Stage 23 — Production Operations Readiness

**Status:** In progress  
**Owner:** Alireza Bahramian

## Goal

تبدیل RC2 به یک بسته عملیاتی قابل اجرا و قابل بررسی برای محیط مقصد، بدون ادعای استقرار واقعی و بدون فعال‌سازی Kimia Write.

## Existing evidence reused

- Stage 16: operational health and observability baseline
- Stage 17: guarded backup and restore drill
- Stage 18: optional queue worker and scheduler profile
- Stage 22: clean deployment, migration, health and restart validation

## Stage 23 scope

1. اجرای Production Compose با profile کامل `workers`
2. اجرای Migration روی دیتابیس خالی
3. بررسی `ops:validate-production-config`
4. بررسی `ops:health --fail-on-degraded`
5. بررسی فعال بودن Queue Worker و Scheduler
6. بررسی `schedule:list`
7. بررسی Queue Restart و بازگشت Worker
8. Restart هم‌زمان PHP، Nginx، Worker و Scheduler
9. Health نهایی پس از Restart
10. Runbook استقرار، Rollback و Incident Response

## Safety boundaries

- Kimia Read-only فعال می‌ماند.
- Kimia Write غیرفعال می‌ماند.
- هیچ Rule مالی، Mapping یا Payload جدید ساخته نمی‌شود.
- CI جایگزین TLS، WAF، Secret Store، Monitoring Provider و Alert Delivery محیط مقصد نیست.
- Rollback واقعی فقط با Release SHA مشخص، Backup معتبر و تأیید مالک انجام می‌شود.

## Acceptance

Stage 23 فقط وقتی Complete است که:

- Production Operations workflow روی SHA نهایی سبز باشد.
- Workflowهای RC1، Security، Performance، Production Compose و Backup/Restore سبز بمانند.
- Runbook عملیاتی ثبت شود.
- PR Merge شود.
- محدودیت‌های محیط مقصد صریح ثبت شوند.
