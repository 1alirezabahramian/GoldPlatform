# Customer Panel — Final Regression Stage

## هدف

بستن زنجیره CP-01 تا CP-18 با یک Gate نهایی مشترک برای مسیرهای Customer V1، OpenAPI، Header رهگیری و جلوگیری از افشای شناسه‌های داخلی Kimia.

## کنترل‌ها

- وجود گروه مسیرهای `/api/v1/customer/*`
- OpenAPI 3.1 معتبر و قابل‌کشف
- همگامی `per_page=25` با Backend
- وجود `X-Request-ID` در Contract و Response
- عدم انتشار `AccountId`، `ProductId`، Voucher، Debit، Credit و Transaction Code
- بدون Migration و بدون Rule مالی جدید

## وضعیت

این Stage فقط پس از Merge شدن CP-15 تا CP-18 روی Branch اصلی Retarget و با شش Gate استاندارد CI نهایی می‌شود.
