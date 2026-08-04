# CP-17 — Customer Request ID Header Contract

## هدف

نمایش همان شناسه پیگیری موجود در پاسخ Customer API داخل Header استاندارد `X-Request-ID` برای پشتیبانی، لاگ و عیب‌یابی.

## قرارداد

- پاسخ موفق: `X-Request-ID` برابر `meta.request_id`
- پاسخ خطا: `X-Request-ID` برابر `request_id`
- شناسه جدید داخل Response Contract تولید نمی‌شود.
- منبع شناسه همان `RequestContext` موجود است.

## مرزهای ایمنی

- بدون Migration
- بدون تغییر Body قراردادهای مالی
- بدون تغییر Wallet، Ledger یا Settlement
- بدون Kimia Read/Write
- بدون افشای Stack Trace یا شناسه داخلی
