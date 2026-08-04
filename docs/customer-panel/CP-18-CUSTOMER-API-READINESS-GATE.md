# CP-18 — Customer API Readiness Gate

## هدف
ایجاد یک Gate نهایی برای جلوگیری از عقب‌گرد خاموش در قرارداد API پنل مشتری.

## پوشش Gate
- Routeهای نسخه‌دار Customer V1
- Middleware نقش مشتری و No-Store
- Bootstrap، Dashboard، Activities، Assets، Orders، Custodies، Deliveries و Profile
- Pagination امن
- فیلتر وضعیت
- مرتب‌سازی محدود `newest/oldest`
- فیلتر تاریخ استاندارد `YYYY-MM-DD`
- Header رهگیری `X-Request-ID`
- Headerهای جلوگیری از Cache
- وجود OpenAPI 3.1 و مسیرهای کلیدی
- وجود Error Contract مرکزی

## مرزهای ایمنی
- بدون Migration
- بدون تغییر Business Rule
- بدون تغییر State Machine
- بدون Wallet، Ledger یا Settlement
- بدون Kimia Read/Write
- بدون ایجاد Endpoint جدید

## نتیجه مورد انتظار
هر حذف یا تغییر ناخواسته در اجزای پایه Customer API باید تست `CustomerApiReadinessGateTest` را قرمز کند و مانع Merge شود.
