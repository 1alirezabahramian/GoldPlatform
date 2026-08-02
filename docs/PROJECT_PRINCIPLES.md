# GoldPlatform — اصول الزام‌آور پروژه

**وضعیت:** Accepted

**آخرین بازنگری:** 2026-08-02

**جایگزین:** نسخه قدیمی‌ای که Wallet داخلی را تنها مرجع موجودی می‌دانست.

## 1. منبع حقیقت و منع حدس

- خروجی واقعی Kimia و Swagger رسمی بر فرضیات فنی اولویت دارند.
- هیچ قانون مالی، Action، شناسه، فرمول، Payload یا Wallet Rule حدس زده نمی‌شود.
- هنگام تناقض: `STOP → اعلام دقیق → تصمیم علیرضا → پیاده‌سازی`.

## 2. مرز Kimia و GoldPlatform

- Kimia مرجع مانده‌های مالی مشتری و موجودی حسابداری فروشگاه است.
- GoldPlatform مرجع سفارش، قیمت‌گذاری، Price Lock، سیاست‌های مشتری، رزرو، امانات، تحویل، هماهنگی اتصال‌ها، خطاهای Integration و Audit است.
- داده‌های محلی Kimia، Projection/Cache قابل بازسازی هستند؛ Wallet داخلی نباید دفتر مالی مستقل و رقیب Kimia ایجاد کند.
- تغییر مستقیم Balance ممنوع است؛ هر تغییر مالی باید از سند/رویداد تأییدشده و قابل ردگیری ناشی شود.

## 3. تفکیک دارایی

- `Money / Gold / Coin / Currency` دارایی مالی‌اند و می‌توانند طبق قواعد گروه اعتباری مثبت، صفر یا منفی باشند.
- `Amanat / Custody` دارایی فیزیکی است و در Balance مالی ادغام نمی‌شود.
- Coin و Currency از Kimia به‌شکل Dynamic دریافت می‌شوند و فهرست نمونه Hard-code نمی‌شود.

## 4. عملیات مالی

- پول و وزن حساس با float محاسبه نمی‌شوند؛ decimal یا نمایش دقیق رشته‌ای استفاده می‌شود.
- هر عملیات مالی باید idempotent، Atomic، قابل Retry امن و دارای Audit Trail باشد.
- `RequestId` یک عملیات در Retry ایمن ثابت می‌ماند.
- هیچ سفارش تکمیل‌شده یا سند مالی بدون سیاست نگهداری و Audit حذف نمی‌شود.
- ارسال زنده سند Kimia تا تأیید کامل Payload، زمان ارسال، Idempotency، Retry و Failure Handling غیرفعال است.

## 5. معماری برنامه

```text
Controller → Application Service → Domain Service → Repository/Adapter → External System
```

- Controller فقط ورودی، مجوز، فراخوانی Use Case و پاسخ را مدیریت می‌کند.
- Business Logic در Controller نوشته نمی‌شود.
- فیلدها و کدهای Kimia در مرز Integration متوقف می‌شوند و به UI نشت نمی‌کنند.
- عملیات سنگین و قابل Retry از Queue مناسب استفاده می‌کنند.
- Service یا Controller نباید مرزهای معماری را دور بزند.

## 6. White-label و Multi-tenancy

- Khalifeh Coin پایلوت اول است، نه هویت دائمی هسته.
- برند، دامنه، محدودیت‌ها، کارمزدها، شعب، قابلیت‌ها و اتصال‌ها باید Tenant-configurable باشند.
- هیچ Tenant نباید داده Tenant دیگر را بخواند یا تغییر دهد.
- منطق اختصاصی مشتری از طریق Policy، Setting، Feature Flag یا Connector اعمال می‌شود؛ نه Hard-code در Core.

## 7. تجربه کاربری

- اصل پروژه: `Complex Backend — Simple Frontend`.
- مشتری نباید اصطلاحات حسابداری مانند Voucher، AccountId، Action و Debit/Credit را ببیند.
- Frontend و Backend می‌توانند موازی پیش بروند، اما UI نباید رفتار مالی حل‌نشده را اختراع کند.

## 8. کیفیت و توسعه

- تغییرات کوچک، قابل بازگشت و محدود به Scope باشند.
- قبل از ایجاد Service، Controller، Migration یا Entity، وجود نمونه قبلی بررسی شود.
- هر قابلیت با تست متناسب و مستندات هم‌زمان همراه است.
- فقط نتیجه‌ای که واقعاً اجرا یا با شاهد معتبر کنترل شده «سالم/کامل» اعلام می‌شود.
- Secret، Token، Password و داده حساس در کد، لاگ، مستندات یا Git ثبت نمی‌شود.
