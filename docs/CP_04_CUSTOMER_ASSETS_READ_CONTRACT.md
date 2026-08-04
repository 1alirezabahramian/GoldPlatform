# CP-04 — Customer Assets Read Contract

**Status:** Implemented; completion requires green CI evidence  
**Branch:** `work/customer-assets-read-contract`

## هدف

ایجاد API خواندنی و نسخه‌دار برای دارایی‌های مالی مشتری، بدون تغییر Rule مالی، بدون Kimia Write و بدون وابستگی Frontend به شناسه‌های داخلی.

## Endpointها

- `GET /api/v1/customer/assets`
- `GET /api/v1/customer/assets/money`
- `GET /api/v1/customer/assets/gold`
- `GET /api/v1/customer/assets/coins`
- `GET /api/v1/customer/assets/currencies`

## قواعد تثبیت‌شده

- Money، Gold، Coin و Currency دارایی مالی هستند.
- مانده می‌تواند مثبت، صفر یا منفی باشد.
- تمام مقادیر حساس به‌صورت string برگردانده می‌شوند.
- Coin و Currency به‌صورت Dynamic از Wallet Accountهای واقعی خوانده می‌شوند.
- `external_asset_id` و سایر شناسه‌های Kimia در پاسخ مشتری منتشر نمی‌شوند.
- Snapshot هر حساب از Ledger Entry و Reservation فعال ساخته می‌شود.
- Frontend هیچ محاسبه مالی انجام نمی‌دهد.

## تصمیم‌های عمداً باز

- تبدیل نمایشی IRR به IRT در این Stage انجام نمی‌شود.
- واژگان «بدهکار/بستانکار» یا معادل UX هنوز تثبیت نشده‌اند.
- مقدار `unit` همان واحد ثبت‌شده Backend است.
- هیچ Formatted Persian Amount در این Stage تولید نمی‌شود تا Rule تبدیل واحد حدس زده نشود.

## Performance

Ledger Entryها و Balance Reservationهای تمام حساب‌های انتخاب‌شده با eager loading دریافت می‌شوند. تعداد Query نباید بر اساس تعداد حساب‌های Dynamic Coin/Currency رشد خطی داشته باشد.

## Security

- احراز هویت با Sanctum
- الزام نقش customer
- انتخاب Wallet فقط از کاربر احراز هویت‌شده
- عدم دریافت `user_id` از Frontend
- عدم انتشار `account_id`، `asset_id`، `external_asset_id` یا Ledger داخلی

## تست‌ها

`CustomerAssetsReadContractTest` موارد زیر را پوشش می‌دهد:

- Authentication
- Contract Structure
- Negative Balance
- Decimal Precision
- Dynamic Coin
- Dynamic Currency
- Type Filtering
- Sensitive Identifier Exposure

## فایل‌ها

- `backend/app/Http/Controllers/Api/V1/CustomerAssetReadController.php`
- `backend/routes/api.php`
- `backend/tests/Feature/CustomerAssetsReadContractTest.php`
- `docs/api/customer-v1.openapi.yaml`
- `docs/CP_04_CUSTOMER_ASSETS_READ_CONTRACT.md`

## Completion Gate

CP-04 فقط پس از موفقیت موارد زیر PASS است:

1. Backend RC1 Validation
2. Security Hardening
3. Production Compose Validation
4. Backup and Restore Drill
5. Stage 21 Performance
6. CustomerAssetsReadContractTest در Regression Suite
