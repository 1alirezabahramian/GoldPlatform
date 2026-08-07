# GoldPlatform Production Operations Runbook

## Scope

این Runbook برای استقرار، کنترل سلامت، Restart، Rollback و Incident Response Backend است. اجرای واقعی فقط در محیط مقصد و با Secretهای خارج از Repository انجام می‌شود.

## Required release inputs

- Release SHA یا Tag تأییدشده
- فایل `backend/.env.production` از Secret Store
- Backup معتبر و checksum تأییدشده
- دامنه و TLS فعال
- مقصد Log/Monitoring و Alert Delivery
- تأیید صریح اینکه `KIMIA_READ_ONLY=true` و `KIMIA_WRITE_ENABLED=false` باقی مانده‌اند

## Deployment sequence

1. دریافت Release SHA تأییدشده.
2. تأیید Secretها و Environment بدون چاپ مقادیر حساس.
3. اجرای Backup قبل از تغییر.
4. اجرای `docker compose -f docker-compose.production.yml --profile workers config --quiet`.
5. Build و Start سرویس‌ها.
6. اجرای Migration با `--force --no-interaction`.
7. اجرای `ops:validate-production-config`.
8. اجرای `/up` و `ops:health --json --fail-on-degraded`.
9. بررسی Queue Worker و Scheduler.
10. Smoke Test فقط‌خواندنی و مجاز.
11. ثبت Release SHA، زمان، اپراتور و نتیجه Health.

## Restart sequence

1. ثبت وضعیت فعلی با `docker compose ps`.
2. اجرای Queue restart کنترل‌شده.
3. Restart سرویس‌های PHP، Nginx، Queue Worker و Scheduler.
4. اجرای Health نهایی.
5. در صورت Degraded یا Fail، ورود به Incident Response.

## Rollback gate

Rollback بدون موارد زیر ممنوع است:

- Release SHA سالم قبلی
- Backup معتبر
- بررسی Migrationهای نسخه جدید
- برنامه Restore یا Forward Fix
- تأیید مالک برای هر ریسک داده یا عملیات مالی

Rollback کد نباید به‌صورت خودکار Migrationهای مالی را Down کند. برای تغییرات ناسازگار دیتابیس، تصمیم جداگانه لازم است.

## Incident response

1. Kimia Write را غیرفعال نگه دار.
2. ثبت Request ID، Release SHA و زمان رخداد.
3. اجرای `ops:health` و جمع‌آوری Log بدون Secret.
4. تشخیص مؤلفه: Database، Redis، Storage، Queue، Outbox، Kimia Safety یا HTTP.
5. انتخاب Restart، Forward Fix یا Rollback براساس شواهد.
6. ثبت Incident، اقدام و نتیجه Health نهایی.

## Environment-only validations

موارد زیر در CI اثبات نمی‌شوند و باید در مقصد بررسی شوند:

- TLS و DNS واقعی
- WAF و IP policy
- Secret Store
- مقصد Monitoring و Alert Delivery
- ظرفیت واقعی Workerها
- Log retention
- Backup retention و Restore واقعی مقصد
- Smoke Test حساب مجاز
- هر نوع Kimia Write
