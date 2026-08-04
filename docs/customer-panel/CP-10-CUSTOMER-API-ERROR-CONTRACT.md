# CP-10 — Customer API Error Contract

## هدف

یکپارچه‌سازی پاسخ خطا برای مسیرهای نسخه‌دار پنل مشتری، بدون تغییر در قوانین مالی، Ledger، Wallet، Settlement، Delivery یا Kimia.

## محدوده

فقط درخواست‌های زیر پوشش داده می‌شوند:

`/api/v1/customer/*`

مسیرهای Admin، Operator، Auth عمومی و Kimia در این Stage تغییر نمی‌کنند.

## قرارداد خطا

تمام خطاها از `CustomerApiResponse::error` عبور می‌کنند و این فیلدها را دارند:

- `message`
- `code`
- `errors`
- `request_id`

## Mapping

| HTTP | Code |
|---|---|
| 401 | `UNAUTHENTICATED` |
| 403 | `FORBIDDEN` |
| 404 | `RESOURCE_NOT_FOUND` |
| 405 | `METHOD_NOT_ALLOWED` |
| 422 | `VALIDATION_FAILED` |
| 429 | `RATE_LIMITED` |
| 500 | `INTERNAL_ERROR` |

## امنیت

- پیام خام Exception نمایش داده نمی‌شود.
- Stack Trace منتشر نمی‌شود.
- پیام SQL یا Kimia به مشتری منتقل نمی‌شود.
- خطای داخلی با پیام عمومی پاسخ داده و در Backend گزارش می‌شود.
- خطای Validation فقط خطاهای ساختاریافته فیلدها را منتشر می‌کند.
- خطای نقش Spatie صریحاً به 403 نگاشت می‌شود.

## تست

`CustomerApiErrorContractTest` کنترل می‌کند:

- Scope محدود به API نسخه‌دار مشتری
- وجود کدهای استاندارد خطا
- استفاده از Envelope مشترک
- عدم استفاده از پیام و Trace خام Exception
- گزارش‌شدن خطاهای داخلی

## پاک‌سازی

دو فایل Marker که اشتباهاً روی Base ایجاد شده بودند، در همین Branch حذف شده‌اند و بعد از Merge دیگر در نسخه جاری وجود نخواهند داشت.
