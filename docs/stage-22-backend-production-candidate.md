# Stage 22 — Backend Production Candidate (RC2)

**Phase:** D — Production RC  
**Status:** Candidate validation in progress  
**Owner:** Alireza Bahramian

## Goal

تبدیل Backend فعلی GoldPlatform به یک Release Candidate قابل‌ردیابی و قابل‌آزمون، بدون ادعای استقرار واقعی و بدون فعال‌سازی Kimia Write.

## Candidate gates

1. Full Regression روی MySQL 8.4 و Redis واقعی
2. Dependency Audit و Secret Scan
3. Deployment پاک با `docker-compose.production.yml`
4. Production Configuration Guard
5. Operational Health برای DB، Redis، Queue، Outbox و Storage
6. Application `/up` Health
7. Restart Test برای PHP و Nginx
8. Production Checklist
9. Backup/Restore، Security و Performance workflowهای موجود

## Added evidence

- `.github/workflows/rc2-candidate.yml`
- `tools/ops/rc2-deployment-test.sh`
- `docs/PRODUCTION_CHECKLIST_RC2.md`

## Safety boundaries

- Kimia Read-only فعال باقی می‌ماند.
- Kimia Write غیرفعال است.
- هیچ Business Rule مالی تغییر نکرده است.
- هیچ Secret واقعی در Workflow یا Repository ثبت نمی‌شود.
- Candidate بودن با Production Deployment واقعی یکی نیست.

## RC2 acceptance

Stage 22 فقط وقتی Complete اعلام می‌شود که:

- RC2 workflow سبز باشد.
- Backend RC1، Security، Performance، Production Compose و Backup/Restore نیز روی SHA نهایی سبز باشند.
- Pull Request Merge شود.
- Candidate SHA و محدودیت‌های باقی‌مانده ثبت شوند.

## Validation evidence — 2026-08-04

- Agent Host و GitHub Issue Queue با اجرای موفق `git-status` تأیید شدند.
- Health Check روی شاخه مبنا اجرا شد و نشان داد شاخه محلی Candidate را در اختیار ندارد؛ بنابراین خروجی آن معیار پذیرش RC2 نیست.
- این Commit برای ایجاد SHA تازه Candidate و اجرای مجدد تمام GitHub Actions مرتبط با PR #78 ثبت شده است.
- نتیجه نهایی فقط از Workflowهای همان SHA و بدون تکیه بر وضعیت قدیمی Agent اعلام می‌شود.

## Environment-specific release blockers

موارد زیر فقط در محیط مقصد قابل تأییدند و خارج از ادعای CI هستند:

- TLS و دامنه واقعی
- Secret Store واقعی
- WAF / IP policy
- Process manager واقعی Queue و Scheduler
- Monitoring و Alert delivery واقعی
- Smoke Test حساب آزمایشی مجاز
- فعال‌سازی هر نوع Kimia Write
