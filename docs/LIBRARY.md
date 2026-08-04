# GoldPlatform — Project Library

> وضعیت: Living Library / مستند یکپارچه پروژه
>
> مالک پروژه: Alireza Bahramian
>
> اصل حاکم: **NO GUESSING — NO REINVENTING — NO SILENT CHANGES**

این Library خلاصه یکپارچه تصمیم‌ها، قابلیت‌ها، مراحل اجرایی و مرزهای تأییدشده پروژه از شروع تا Backend RC1 است. منبع جزئیات هر موضوع، مستند تخصصی و ADR مربوط به همان موضوع است.

---

## 1. هویت و هدف محصول

GoldPlatform فروشگاه ساده نیست؛ سامانه معاملات، مدیریت دارایی مشتری، سفارش، تسویه، تحویل، امانات فیزیکی، کیف پول، Ledger و اتصال کنترل‌شده به Kimia است.

هدف تجاری نهایی:

- ارائه به مشتری نهایی با رابط فارسی ساده و قابل‌اعتماد
- ارائه White-label به چند طلافروشی یا شرکت
- پشتیبانی از Money، Gold، Coin و Currency به‌عنوان دارایی مالی
- نگهداری Amanat/Custody به‌عنوان دارایی فیزیکی مستقل
- قابلیت توسعه به چند شعبه و چند برند

اصل معماری:

> **Complex Backend — Simple Frontend**

پیچیدگی مالی، Ledger، Settlement و Kimia در Backend می‌ماند و رابط مشتری اصطلاحات حسابداری خام را نمایش نمی‌دهد.

---

## 2. منابع حقیقت پروژه

ترتیب اعتبار:

1. خروجی واقعی API کیمیا
2. Swagger/OpenAPI رسمی کیمیا
3. `docs/00_PROJECT_MEMORY.md` و ADRهای تأییدشده
4. قواعد و نمونه‌های واقعی تأییدشده توسط مالک پروژه
5. کد جاری مخزن
6. دانش عمومی فقط برای توضیح و پیشنهاد

در صورت تناقض، اجرا باید متوقف و تصمیم مالک پروژه دریافت شود.

---

## 3. زیرساخت و پایه فنی

پایه Backend:

- Laravel 13
- PHP 8.4
- MySQL 8.4
- Redis 7
- Nginx و PHP-FPM در Docker Compose
- Sanctum برای احراز هویت API
- Spatie Permission برای Role و Permission
- Queue، Session و Cache قابل تنظیم
- GitHub Actions برای Migration، تست، Health و Docker validation

اصول اجرایی:

- محاسبات مالی دقیق و بدون float
- تغییر مانده فقط از مسیر Ledger و رویداد مالی
- عملیات حساس Idempotent و قابل Audit
- Migrationهای قابل بازگشت
- عدم ثبت Token، Password و Secret در Git یا Log

---

## 4. احراز هویت و کاربران

قابلیت‌های پیاده‌شده:

- ساختار User و User Group
- ثبت‌نام و ورود
- OTP foundation
- Sanctum token authentication
- ایجاد Wallet و حساب‌های پیش‌فرض هنگام ساخت کاربر
- Roleهای `customer`، `operator` و `admin`
- جلوگیری از تعیین مالک سفارش توسط Client

مرز باقی‌مانده:

- اتصال Production به SMS.ir و Jibit نیازمند Credential و تست محیط واقعی است.

---

## 5. مدل دارایی‌ها

### دارایی‌های مالی

- Money
- Gold
- Coin
- Currency

ویژگی مشترک:

- مانده می‌تواند مثبت، صفر یا منفی باشد.
- منبع حقیقت مانده، Ledger است.
- Coin و Currency پویا و وابسته به داده Kimia هستند.

### دارایی‌های فیزیکی

- Parsian
- Bullion
- Melted Gold
- Jewelry و مصنوعات
- سایر اقلام فیزیکی

این دارایی‌ها در Custody/Amanat نگهداری می‌شوند و با Wallet balance ادغام نشده‌اند.

---

## 6. Kimia Integration

پیاده‌سازی Read Foundation:

- Client مرکزی و Canonical برای درخواست‌های Kimia
- محافظت از Credentialها در Log
- Account Group و Account خواندنی
- پارامتر تأییدشده `Type=3` برای حساب تک‌فروشی
- Voucher transaction خواندنی
- Sync حساب‌ها بدون ایجاد رکورد تکراری
- Sync Coin و Currency به‌صورت پویا
- پشتیبانی پاسخ مستقیم و Wrapped API
- Mock و Contract Test برای مسیرها و Queryهای Swagger

قواعد تأییدشده:

- Kimia واحد ریال و GoldPlatform واحد نمایشی تومان دارد.
- تبدیل واحد باید صریح و تست‌شده باشد.
- Customer Buy در Kimia از سمت کسب‌وکار Sell است.
- Customer Sell در Kimia از سمت کسب‌وکار Buy است.
- شناسه‌های نمونه Coin و Currency نباید در معماری Hard-code شوند.

مرز RC1:

- عملیات Write واقعی Kimia غیرفعال است.
- CI فقط Mock/Contract read-only را اجرا می‌کند.
- تست Production read-only با Credential واقعی خارج از GitHub CI و نیازمند محیط امن متصل است.

---

## 7. Ledger و محاسبات مالی

پیاده‌سازی‌شده:

- FinancialTransaction
- LedgerEntry
- Debit/Credit متوازن به تفکیک واحد دارایی
- جلوگیری از انتقال به همان حساب
- جلوگیری از مبلغ صفر یا منفی
- Decimal arithmetic دقیق به‌صورت رشته‌ای
- عدم وابستگی اجباری به BCMath
- جلوگیری معماری از حذف مستقیم رکوردهای مالی

منبع حقیقت مانده:

> Ledger + اسناد مالی تأییدشده

Wallet balance از Ledger بازسازی می‌شود و تغییر مستقیم مانده، مسیر معتبر مالی محسوب نمی‌شود.

---

## 8. سفارش و چرخه عمر

State Machine سفارش:

- pending
- approved
- executing
- settling
- completed
- rejected
- expired
- cancelled
- failed

کنترل‌ها:

- Transitionهای مجاز و صریح
- Row Lock با `lockForUpdate`
- `state_version`
- timestamp هر مرحله
- دلیل اجباری برای Reject و Failure
- جلوگیری از خروج از وضعیت Terminal
- تکرار Transition یکسان به‌شکل Idempotent
- جلوگیری از تغییر فیلدهای سروری توسط Client

---

## 9. Trading Engine و Settlement

مسیر اتمیک پیاده‌شده:

`Order → Trade → FinancialTransaction → Ledger → Settlement`

کنترل‌ها:

- اجرای Trade فقط برای Order تأییدشده
- جلوگیری از اجرای دوباره
- الزام حساب‌های مبدأ و مقصد صریح
- عدم استفاده از حساب Hard-code
- Rollback کامل در صورت خطا
- تکمیل Order فقط پس از Ledger متوازن و Settlement موفق
- Settlement lifecycle و ارتباط با تراکنش مالی

مرز باقی‌مانده:

- Mapping حساب‌های واقعی هر سناریوی Kimia باید از داده واقعی و تصمیم تأییدشده تأمین شود.

---

## 10. Wallet و Balance Projection

پیاده‌سازی‌شده:

- Wallet و WalletAccount
- Asset identity شامل نوع دارایی، External ID و Unit
- محاسبه `total`، `blocked` و `available`
- Balance Reservation
- Reserve، Release و Consume به‌صورت Idempotent
- پشتیبانی از مانده منفی طبق Policy
- عدم ادغام Custody با Wallet

---

## 11. Coin و Currency پویا

پیاده‌سازی‌شده:

- سفارش Coin و Currency بر اساس Catalog همگام‌شده Kimia
- الزام شناسه واقعی همگام‌شده
- رد Asset ناشناخته یا Hidden
- عدم Hard-code کردن CoinId و CurrencyId
- Quantity، Unit و Price Snapshot دقیق

---

## 12. Custody / Amanat

پیاده‌سازی‌شده:

- مدل مستقل CustodyAsset
- دریافت دارایی فیزیکی
- رزرو
- درخواست تحویل
- آماده تحویل
- تحویل‌شده
- فروش مجدد
- تبدیل به طلا
- تبدیل به پول
- وضعیت‌های Terminal
- الزام Financial Reference برای بستن مالی امانت
- جلوگیری از Transition نامعتبر و عملیات تکراری

---

## 13. Delivery

پیاده‌سازی‌شده:

- ثبت درخواست توسط مالک Custody
- جلوگیری از درخواست برای دارایی متعلق به کاربر دیگر
- جلوگیری از درخواست فعال تکراری
- تأیید توسط Operator
- آماده‌سازی
- ثبت تحویل نهایی
- ثبت نام و شناسه گیرنده
- جلوگیری از تحویل دوباره
- ثبت زمان‌ها و وضعیت‌ها

---

## 14. قوانین مشتری و گروه‌ها

پیاده‌سازی‌شده به‌شکل Data-driven:

- نیاز یا عدم نیاز به موجودی قابل‌استفاده
- امکان مانده منفی
- دوره قفل دارایی
- سقف وزن طلا
- سقف تعداد سکه
- سقف مبلغ
- Credit Limit
- حداقل و حداکثر سفارش
- حداکثر اقلام تحویل

هیچ شناسه گروهی به‌صورت ثابت در منطق معماری Hard-code نشده است.

---

## 15. API پنل‌ها

### Customer API

- Overview
- Balances
- Orders
- Custody
- Delivery requests
- ثبت درخواست تحویل

### Operator API

- صف سفارش‌ها
- صف تحویل
- تأیید تحویل
- آماده‌سازی
- ثبت تحویل نهایی

### Admin API

- Audit Logs
- Outbox
- Customer Trading Policies
- ویرایش Policy

همه مسیرها با Sanctum و Roleهای صریح محافظت شده‌اند.

---

## 16. Audit، Idempotency و Outbox

پیاده‌سازی‌شده:

- `X-Request-Id` برای Correlation
- الزام `Idempotency-Key` در عملیات حساس
- جلوگیری از اجرای دوباره Request یکسان
- جلوگیری از استفاده یک Key برای Payload متفاوت
- Audit شامل Actor، Subject، Action، Before، After و Request ID
- Transactional Outbox در همان Transaction عملیات اصلی
- عدم ذخیره Credential در Audit و Outbox

مرز باقی‌مانده:

- Dispatcher/Publisher غیرهم‌زمان Outbox مرحله زیرساخت بعدی است.

---

## 17. تست و کیفیت

پوشش ایجادشده تا RC1:

- Unit Tests
- Feature Tests
- Financial precision tests
- Ledger guards and balance tests
- Order lifecycle tests
- Trade execution and idempotency tests
- Settlement tests
- Custody tests
- Delivery tests
- Permission isolation tests
- Kimia mock tests
- Kimia read-only contract tests
- Migration fresh
- Laravel health and route validation
- MySQL and Redis health
- Docker Compose validation
- Secret scan

نتیجه هر Release Gate باید در `docs/test-reports/` ثبت شود.

---

## 18. تاریخچه مراحل Backend

1. Kimia Read Foundation
2. Ledger Foundation
3. Order State Machine
4. Trading Engine
5. Balance Projection / Wallet
6. Dynamic Coin / Currency
7. Custody / Amanat
8. Delivery
9. Customer Rules and Limits
10. Customer / Operator / Admin APIs
11. Audit / Idempotency / Outbox
12. Backend RC1 Final Gate

هر مرحله با PR، تست و مستند مرتبط تکمیل شده است. این Library جایگزین مستندات جزئی نیست؛ نقشه مرکزی آن‌هاست.

---

## 19. مستندات مرجع

- `docs/00_PROJECT_MEMORY.md`
- `docs/PROJECT_STATE.md`
- `docs/ARCHITECTURE_BLUEPRINT.md`
- مستندات شماره‌گذاری‌شده `01` تا `20`
- `docs/adr/`
- `docs/test-reports/`
- مستندات مراحل Backend

---

## 20. موارد خارج از اعلام تکمیل Production

Backend RC1 به معنی Production Launch نیست. موارد زیر جداگانه نیازمند انجام یا تأیید هستند:

- تست امن با API واقعی Kimia
- تأیید Mapping حساب‌ها و Payloadهای Write
- SMS.ir و Jibit Production
- Outbox Dispatcher و Queue Worker عملیاتی
- Backup/Restore drill
- Observability و Alerting Production
- Rate limiting و Load/Stress Test
- Penetration test مستقل
- Frontend نهایی و Contract synchronization
- Deployment و Runbook محیط Production

تا زمان عبور این موارد، وضعیت درست پروژه **Backend RC1** است، نه Production Complete.
