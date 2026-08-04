# CP-16 — Customer No-Store Cache Contract

## هدف

جلوگیری از ذخیره‌شدن پاسخ‌های حساس Customer API در Cache مرورگر، Proxy یا واسط‌های اشتراکی.

## قرارداد

تمام مسیرهای `/api/v1/customer/*` باید Headerهای زیر را برگردانند:

- `Cache-Control: private, no-store, no-cache, must-revalidate, max-age=0`
- `Pragma: no-cache`
- `Expires: 0`
- `Vary: Authorization`

## پیاده‌سازی

- Middleware اختصاصی `CustomerNoStore`
- ثبت Alias با نام `customer.no-store`
- اعمال فقط روی گروه نسخه‌دار Customer V1
- عدم اعمال سراسری روی Admin، Operator، Kimia یا سایر APIها

## مرزهای ایمنی

- بدون Migration
- بدون تغییر Rule مالی
- بدون تغییر Wallet، Ledger یا Settlement
- بدون Kimia Read/Write
- بدون تغییر Response Body
