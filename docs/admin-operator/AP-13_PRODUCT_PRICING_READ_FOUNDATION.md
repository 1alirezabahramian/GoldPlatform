# AP-13 — Product & Pricing Read Foundation

## هدف
ایجاد API فقط‌خواندنی و نسخه‌دار برای دسته‌بندی محصولات، محصولات و وضعیت قابلیت‌های Pricing، بدون تغییر قیمت یا ساخت Rule مالی جدید.

## Endpointها
- `GET /api/v1/admin/product-categories`
- `GET /api/v1/admin/products`
- `GET /api/v1/admin/pricing/overview`

## Permissionها
- `product-categories.view`
- `products.view`
- `pricing.view`

## Ground Truth تأییدشده
در Schema فعلی فقط جدول‌های زیر برای این حوزه تأیید شدند:
- `product_categories`
- `products`

ستون‌های قیمت موجود در جدول Product:
- `buy_price`
- `sell_price`

واحد پول این ستون‌ها در خود Schema مشخص نشده است؛ بنابراین API آن را حدس نمی‌زند و مقدار `unspecified_in_schema` برمی‌گرداند.

## قابلیت‌های پشتیبانی‌نشده در وضعیت فعلی
- Formula management
- Spread management
- Rounding management
- Dynamic Coin catalog
- Dynamic Currency catalog
- Kimia product sync tracking

این موارد با `supported=false` گزارش می‌شوند و داده ساختگی برای آن‌ها تولید نمی‌شود.

## مرزهای ایمنی
- بدون Create/Update/Delete محصول
- بدون تغییر buy/sell price
- بدون Spread یا Round
- بدون Kimia Sync
- بدون Migration
- بدون Hard-code کردن Coin یا Currency
- بدون نمایش Credential یا Payload کیمیا

## تست‌ها
- دسترسی Admin
- جلوگیری از دسترسی Operator
- قرارداد Product و Category
- اعلام صریح قابلیت‌های پشتیبانی‌نشده
- محدودیت Pagination

## وضعیت صداقت
کد، تست و مستندات ثبت شده‌اند. تست‌ها در این محیط اجرا نشده‌اند و PASS نهایی فقط پس از سبز شدن GitHub Actions روی SHA نهایی اعلام می‌شود.
