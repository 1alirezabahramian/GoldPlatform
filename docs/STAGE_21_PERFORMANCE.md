# Stage 21 — Performance

**Phase:** C — Performance  
**Status:** Implemented; completion requires green CI evidence  
**Branch:** `work/stage-21-performance`

## هدف

ایجاد یک Performance Gate قابل‌اندازه‌گیری برای GoldPlatform، بدون تغییر قانون مالی، بدون فعال‌سازی Kimia Write و بدون Cache کردن حقیقت مالی.

## Query Optimization

مسیرهای خواندنی فعلی بررسی شدند:

- سفارش‌های مشتری: `user_id + status + id`
- صف سفارش اپراتور: `status + id`
- امانات مشتری: `user_id + id`
- تحویل‌های مشتری: `user_id + status + id`
- صف تحویل اپراتور: `status + id`

Migration مرحله ۲۱ برای این Query Pathها Composite Index اضافه می‌کند.

## Index Review

Indexهای جدید:

- `orders_user_status_id_idx`
- `orders_status_id_idx`
- `custody_assets_user_id_idx`
- `delivery_requests_user_status_id_idx`
- `delivery_requests_status_id_idx`

Migration قابل Rollback است و هیچ داده‌ای را حذف یا بازنویسی نمی‌کند.

## N+1 Detection / Query Budget

تست `Stage21PerformanceTest` تعداد Queryهای صف سفارش و صف تحویل را کنترل می‌کند.

هر Pagination باید حداکثر دو Query داشته باشد:

1. Query شمارش
2. Query دریافت صفحه

افزایش Query بر اساس تعداد رکورد، Fail محسوب می‌شود.

## Cache Review

### مواردی که فعلاً نباید Cache شوند

- Ledger
- Wallet balance
- blocked balance
- available balance
- وضعیت سفارش در حال اجرا
- وضعیت Settlement
- نتیجه Kimia Write

این داده‌ها منبع حقیقت مالی یا عملیاتی هستند و Cache بدون Invalidation قطعی می‌تواند اطلاعات کهنه ایجاد کند.

### موارد قابل Cache در مراحل بعد، پس از تعریف TTL و Invalidation

- فهرست Dynamic Coin از Kimia
- فهرست Dynamic Currency از Kimia
- Account Groups خواندنی
- تنظیمات عمومی غیرمالی White-label

در Stage 21 هیچ Cache مالی جدیدی فعال نشده است.

## Load Test

سناریوی ثابت:

- 10 کاربر مجازی
- 20 ثانیه
- Endpoint: `/up`

## Stress Test

سناریوی افزایشی:

- افزایش تا 40 کاربر مجازی
- سپس کاهش کنترل‌شده

## Concurrency Test

- 25 کاربر مجازی هم‌زمان
- 250 درخواست مشترک

## Thresholds

- Error rate کمتر از 1٪
- P95 کمتر از 750ms
- P99 کمتر از 1500ms
- Check success بیشتر از 99٪

این Thresholdها Baseline CI هستند و به معنی ظرفیت Production نهایی نیستند.

## محدودیت شواهد

Load/Stress این مرحله روی Health Endpoint و Stack واقعی CI اجرا می‌شود. این تست ظرفیت نهایی معاملات مالی یا Kimia Production را اثبات نمی‌کند.

تست بار روی Endpointهای احراز هویت‌شده و عملیات Order/Trade باید با Dataset و سناریوی کسب‌وکار تأییدشده در مرحله مستقل انجام شود تا قانون مالی یا رفتار مشتری حدس زده نشود.

## فایل‌های مرحله

- `backend/database/migrations/2026_08_04_090000_add_stage_21_performance_indexes.php`
- `backend/tests/Feature/Performance/Stage21PerformanceTest.php`
- `backend/tests/performance/stage21.js`
- `.github/workflows/stage21-performance.yml`
- `docs/STAGE_21_PERFORMANCE.md`

## Completion Gate

Stage 21 فقط وقتی Complete است که موارد زیر سبز باشند:

- Migration Fresh
- Query Budget tests
- Index existence tests
- Load scenario
- Stress scenario
- Concurrency scenario
- Backend RC regression
- Production Compose regression
