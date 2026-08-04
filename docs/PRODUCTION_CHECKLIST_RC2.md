# GoldPlatform Backend — Production Checklist RC2

**Owner:** Alireza Bahramian  
**Candidate:** Backend RC2  
**Target branch:** `feature/goldplatform-developer-mcp`

این Checklist فقط با شواهد CI و محیط مقصد تکمیل می‌شود. علامت سبز CI به‌تنهایی به معنی فعال‌بودن سرویس‌های خارجی Production نیست.

## 1. Source and release integrity

- [ ] تمام تغییرات Candidate در Pull Request مستقل ثبت شده‌اند.
- [ ] SHA نهایی Candidate مشخص و تغییرناپذیر است.
- [ ] `composer.lock` معتبر و Dependency Audit بدون Advisory است.
- [ ] Secret Scan پاس شده است.
- [ ] هیچ `.env`، Token، Password یا API Key در Git ثبت نشده است.

## 2. Regression gate

- [ ] Migration Fresh روی MySQL 8.4 پاس شده است.
- [ ] Unit Tests پاس شده‌اند.
- [ ] Feature Tests پاس شده‌اند.
- [ ] Financial و Ledger Tests پاس شده‌اند.
- [ ] Order Lifecycle و Idempotency پاس شده‌اند.
- [ ] Settlement، Custody و Delivery پاس شده‌اند.
- [ ] Permission Isolation پاس شده است.
- [ ] Kimia Mock و Read-only Contract پاس شده‌اند.
- [ ] Security و Performance Gateها پاس شده‌اند.

## 3. Deployment gate

- [ ] Production Compose معتبر است.
- [ ] MySQL و Redis پورت عمومی ندارند.
- [ ] PHP و Nginx Image از Lockfile Candidate ساخته می‌شوند.
- [ ] Stack از وضعیت خالی بالا می‌آید.
- [ ] تنظیمات Production توسط `ops:validate-production-config` تأیید می‌شوند.
- [ ] DB، Redis، Queue، Outbox و Storage توسط `ops:health` سالم‌اند.
- [ ] `/up` پاسخ موفق می‌دهد.
- [ ] Restart عادی PHP و Nginx سلامت را حفظ می‌کند.
- [ ] Migration Status داخل Image قابل خواندن است.

## 4. Operations and recovery

- [ ] Backup و Restore Drill ایزوله پاس شده است.
- [ ] Checksum Backup تأیید شده است.
- [ ] Restore مخرب روی دیتابیس مقصد محافظت می‌شود.
- [ ] Runbookهای Deployment، Health، Backup و Restore در مخزن موجودند.
- [ ] Rollback نسخه برنامه و Migrationهای Candidate پیش از استقرار واقعی مرور شده‌اند.

## 5. Kimia safety boundary

- [ ] `KIMIA_READ_ONLY=true` است.
- [ ] `KIMIA_WRITE_ENABLED=false` است.
- [ ] هیچ Payload، Action Code یا Endpoint نوشتنی حدس زده نشده است.
- [ ] Credential واقعی Kimia فقط در Secret Store محیط مقصد قرار دارد.
- [ ] فعال‌سازی Kimia Write نیازمند مرحله و تأیید جداگانه علیرضا است.

## 6. Environment-specific items — must be completed at actual deployment

- [ ] دامنه و TLS واقعی تأیید شده‌اند.
- [ ] WAF/Reverse Proxy و محدودیت IP در صورت نیاز تنظیم شده‌اند.
- [ ] APP_KEY واقعی و Secretهای Production در Secret Store قرار گرفته‌اند.
- [ ] DB، Redis و Storage مقصد Backup معتبر دارند.
- [ ] Queue Worker و Scheduler تحت Process Manager محیط مقصد فعال‌اند.
- [ ] Alerting و Log retention محیط مقصد تأیید شده‌اند.
- [ ] Smoke Test با حساب آزمایشی مجاز انجام شده است.

## Release decision

RC2 فقط زمانی **Production Candidate** است که گیت‌های CI این سند سبز باشند. ورود به Production واقعی تا تکمیل بخش ۶ و تأیید صریح علیرضا مجاز نیست.
