# CP-02 — Customer Read Resources

## هدف

جایگزینی Presenter عمومی در لیست‌های Customer V1 با Resourceهای صریح و محدود، بدون تغییر منطق مالی، State Machine، Ledger، Wallet یا Kimia.

## فایل‌های افزوده‌شده

- `backend/app/Http/Resources/Api/V1/Customer/OrderResource.php`
- `backend/app/Http/Resources/Api/V1/Customer/CustodyResource.php`
- `backend/app/Http/Resources/Api/V1/Customer/DeliveryResource.php`
- `backend/tests/Unit/Architecture/CustomerReadResourcesTest.php`

## فایل تغییرکرده

- `backend/app/Http/Controllers/Api/V1/CustomerReadController.php`

## قراردادهای حفظ‌شده

- مقادیر وزن، تعداد و عیار در مرز API به‌صورت رشته باقی می‌مانند.
- شناسه داخلی Kimia، شناسه کاربر، metadata و فیلدهای اپراتوری منتشر نمی‌شوند.
- Envelope، Pagination، Filter و Routeهای موجود تغییر نکرده‌اند.
- Custody همچنان دارایی فیزیکی مستقل است.

## موارد خارج از محدوده

- تغییر Business Rule مالی
- تغییر Wallet یا Ledger
- تغییر State Machine
- Kimia Read/Write
- Migration
- انتخاب فناوری Frontend

## تست افزوده‌شده

`CustomerReadResourcesTest` اتصال Controller به Resourceها، عدم انتشار فیلدهای داخلی و حفظ Decimal string boundary را Guard می‌کند.
