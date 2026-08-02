# GoldPlatform — Project Memory, Ground Truth & Architecture Contract

> **Document:** `docs/00_PROJECT_MEMORY.md`
>
> **Status:** Accepted / Living Ground Truth
>
> **Purpose:** این فایل حافظه مرجع پروژه است. اگر در Session جدید، معماری، منطق مالی، کیمیا، Wallet، Amanat یا معنی Codeها فراموش شد، این فایل باید **قبل از هر تحلیل، پیشنهاد یا تغییر کد** خوانده شود.
>
> **Golden Rule:** **NO GUESSING — NO REINVENTING — NO SILENT CHANGES**
>
> هر چیزی که در این سند، مستندات واقعی کیمیا، Swagger/OpenAPI یا خروجی واقعی API مشخص نشده است، نباید حدس زده شود.

---

# 1. Project Identity

GoldPlatform یک **Gold Trading Platform / Order Management System (OMS)** و سامانه مدیریت دارایی مشتری است.

این پروژه فقط فروشگاه اینترنتی نیست.

اهداف:

- Physical Gold Sales
- Digital / Paper Gold Trading
- Monetary Balance
- Gold Balance
- Coin Balance
- Currency Balance
- Customer Custody / Amanat
- Credit Trading
- Deferred Settlement
- Delivery
- Inventory
- Ledger
- Settlement
- Kimia ERP / Accounting Integration
- Customer Groups
- Permissions
- OMS
- Multi Branch
- API Services
- Marketplace

---

# 2. Architecture Principle

## Complex Backend — Simple Frontend

Backend می‌تواند پیچیده باشد؛ Frontend نباید پیچیدگی حسابداری و کیمیا را به کاربر منتقل کند.

کاربر نباید مجبور باشد این اصطلاحات را بفهمد:

- Voucher
- AccountId
- AccountType
- GroupId
- Action
- Transaction Code
- Debit
- Credit

کاربر باید مفاهیم ساده ببیند:

- پول من
- طلای من
- سکه‌های من
- ارزهای من
- امانات من
- آماده تحویل
- خرید
- فروش
- تبدیل به طلا
- تبدیل به پول
- تحویل

---

# 3. Source of Truth

در بخش مالی و اتصال به کیمیا، **واقعیت کیمیا و API واقعی بر حدس، الگوی عمومی بازار و پیشنهاد AI اولویت دارد.**

ترتیب اعتبار:

1. خروجی واقعی API کیمیا
2. Swagger / OpenAPI واقعی کیمیا
3. مستندات پروژه و ADRهای تأییدشده
4. مثال‌های واقعی ارائه‌شده توسط کارفرما
5. کد موجود پروژه
6. در نهایت دانش عمومی — فقط برای توضیح، نه برای ساخت Business Rule

اگر این منابع با هم تناقض داشتند:

**STOP → اعلام تناقض → درخواست تصمیم → سپس implementation**

---

# 4. Four Financial Balance Types

کیمیا برای مشتری چهار نوع اصلی مانده مالی دارد:

1. Money
2. Gold
3. Coin
4. Currency

این چهار مورد **Financial Balance** هستند.

تمام مانده‌های مالی می‌توانند:

- مثبت
- صفر
- منفی

باشند.

---

# 5. Money Balance

مانده پولی مشتری.

نمونه:

- Rial
- Toman

مثال:

```text
Money = -100,000,000
```

منفی بودن مانده ممکن است در شرایط اعتباری/بدهکاری رخ دهد.

---

# 6. Gold Balance

مانده طلای ۱۸ عیار / Paper Gold / طلای معامله‌ای.

مثال:

```text
Gold = 2.000 g
Fineness = 750
```

طلای معامله‌ای می‌تواند بدون تحویل فیزیکی خرید و فروش شود.

مانده طلا نیز می‌تواند منفی باشد.

---

# 7. Coin Balance

مانده سکه.

این بخش **Dynamic** است.

نباید GoldPlatform فقط چند نوع سکه ثابت را Hard-Code کند.

ممکن است هر فروشگاه در کیمیا سکه‌های متفاوتی تعریف کند.

نمونه‌ها:

- تمام امامی
- تمام بهار آزادی
- نیم
- ربع
- گرمی
- پهلوی
- سایر سکه‌ها

هر محصول سکه می‌تواند `ProductId` مخصوص خود را در کیمیا داشته باشد.

مثال‌های واقعی فعلی:

```text
سکه تمام = ProductId 10006
سکه نیم   = ProductId 10007
```

> این اعداد نمونه‌های فعلی هستند و نباید به‌عنوان لیست ثابت معماری Hard-Code شوند.

---

# 8. Currency Balance

مانده ارز.

نمونه‌ها:

- USD
- EUR
- AED
- GBP
- هر ارز دیگری که در کیمیا تعریف شود

این بخش نیز باید Dynamic باشد.

---

# 9. Financial Assets vs Physical Assets

## Financial Assets

- Money
- Gold
- Coin
- Currency

ویژگی‌ها:

- Balance
- Buy
- Sell
- Credit Trading
- Negative Balance
- Settlement

## Physical Custody Assets

- Parsian
- Bullion
- Standard Melted Gold
- Jewelry
- Ring
- Earrings
- Half Set
- Set
- سایر مصنوعات

ویژگی‌ها:

- Purchase
- Custody
- Ready For Pickup
- Delivery

این دو مدل نباید در یک Entity یا Balance ساده ادغام شوند.

---

# 10. Amanat / Custody

محصولات فیزیکی ساخته‌شده که جزو چهار مانده مالی کیمیا نیستند، باید در **Amanat / Custody** نگهداری شوند.

نام مفهومی:

- Amanat
- Customer Custody
- Custody Assets

Amanat یک Financial Balance نیست.

مثلاً:

```text
Gold Balance = 1.000 g
```

به معنی مالکیت یک محصول فیزیکی مشخص نیست.

اما اگر مشتری با موجودی خود یک پلاک پارسیان بخرد:

```text
Gold Balance
    ↓
Purchase Physical Product
    ↓
Create Amanat Record
    ↓
Ready For Pickup
    ↓
Delivered
```

پس از خرید:

- Financial Balance کاهش می‌یابد.
- Amanat افزایش می‌یابد.
- کالا تا زمان تحویل نزد فروشنده نگهداری می‌شود.

---

# 11. Physical Product Categories

## Parsian

وزن‌های فعلی:

```text
30 سوت تا 2 گرم
```

مثال واقعی:

```text
500 سوت
Product Code = 500
```

## 24K Bullion

وزن‌های فعلی:

```text
100 سوت تا 10 گرم
```

## Standard Melted Gold

وزن‌های استاندارد فعلی:

```text
2g
3g
5g
10g
15g
20g
```

## Jewelry

در آینده:

- انگشتر
- گوشواره
- نیم‌ست
- سرویس
- جواهرات
- سایر مصنوعات

---

# 12. Physical Purchase

اگر مشتری فقط پارسیان بخواهد، مستقیماً محصول را خریداری می‌کند.

نمونه:

```text
Customer
    ↓
Select Parsian
    ↓
Select Payment Source
    ↓
Money Balance OR Gold Balance
    ↓
Purchase
    ↓
Create Amanat
    ↓
Ready For Pickup
    ↓
Pickup
    ↓
Delivered
```

پارسیان/شمش/مصنوعات نباید بدون Business Rule مشخص به Gold Balance تبدیل شوند.

---

# 13. Payment Sources for Physical Products

خرید فیزیکی می‌تواند با:

- Money Balance
- Gold Balance

انجام شود.

مثال:

```text
Gold Balance
    ↓
Buy Parsian 500 Soot
    ↓
Amanat Parsian 500 Soot
```

---

# 14. Credit Trading

برخی مشتریان ویژه می‌توانند بدون موجودی کافی معامله کنند.

نمونه:

- Buy without sufficient balance
- Sell without sufficient balance
- Deferred Settlement
- Negative Balance

این قابلیت عمومی نیست.

باید بر اساس:

```text
Customer Group
+
Permission
+
Trading Rules
```

فعال شود.

---

# 15. Customer Groups

حداقل دو گروه مفهومی:

## Normal

برای معامله نیاز به موجودی کافی دارد.

## VIP / Credit

ممکن است اجازه داشته باشد:

- خرید بدون موجودی
- فروش بدون موجودی
- بدهکار شدن
- تسویه بعدی

مدل دقیق Permission در آینده تکمیل می‌شود.

---

# 16. Kimia Operational/Form Codes — Owner Confirmed

از مثال‌های واقعی پروژه:

| Code | Meaning |
|---:|---|
| 1 | دریافت |
| 2 | پرداخت |
| 3 | خرید |
| 4 | فروش |
| 7 | حواله |
| 8 | پولی کردن سکه و ارز |

**این جدول Ground Truth روند عملیاتی/فرم Kimia است، نه قرارداد عددی API.**

Swagger رسمی برای فیلد `Action` در API از کدهای دیگری مانند `32=خرید` و
`64=فروش` استفاده می‌کند. این دو نمایش نباید با یکدیگر ادغام شوند.

---

# 17. Critical Code Distinction

یکی از مهم‌ترین قوانین پروژه:

### Operational/Form Code 4

یعنی:

```text
فروش
```

و **به معنی پولی کردن نیست.**

اما در معامله طلای کاغذی، `Product / Money Code = 4` مربوط به «پولی» است.

پس این دو هرگز نباید با هم اشتباه شوند:

```text
Operational/Form Code 4 = فروش

Money Product Code 4 = پولی در معامله طلای کاغذی
```

همچنین:

```text
Operational/Form Code 8 = پولی کردن سکه و ارز
```

---

# 18. Paper Gold Trading

طلای معامله‌ای در کیمیا با خرید/فروش ثبت می‌شود.

Action:

```text
Customer Buy  = Kimia business side Sell
Customer Sell = Kimia business side Buy
```

Owner-confirmed operational/form code:

```text
Kimia Buy  = 3
Kimia Sell = 4
```

Confirmed API transport code:

```text
API Buy  = 32
API Sell = 64
```

Money Product:

```text
4
```

---

# 19. Paper Gold Buy — Real Example

مشتری:

```text
360,000,000 ریال
```

به قیمت:

```text
180,000,000 ریال / g
```

خرید:

```text
2.000 g
Fineness = 750
```

مفهوم ثبت:

```text
Kimia business side = Sell
Operational/form code = 4
Swagger API Action = 64 (runtime-confirmed from AccountId 350)
Money Product = 4
Weight = 2
Fineness = 750
GoldPrice = 180,000,000
SumMoney = 360,000,000
```

اثر مالی:

```text
Money Balance -= 360,000,000
Gold Balance  += 2.000 g
```

> جهت Buy/Sell و مقدار Action با Swagger و پاسخ واقعی خواندنی Kimia تأیید شده است.
> payload کامل و جریان نوشتن سند هنوز جداگانه مسدود است.

---

# 20. Paper Gold Sell — Real Example

مشتری:

```text
1.5 g
Fineness = 750
```

قیمت:

```text
177,500,000 ریال / g
```

مبلغ:

```text
1.5 × 177,500,000
= 266,250,000 ریال
```

ثبت مفهومی:

```text
Kimia business side = Buy
Operational/form code = 3
Swagger API Action = 32 (runtime-confirmed from AccountId 350)
Money Product = 4
Weight = 1.5
Fineness = 750
GoldPrice = 177,500,000
SumMoney = 266,250,000
```

اثر:

```text
Gold Balance  -= 1.500 g
Money Balance += 266,250,000
```

---

# 21. Coin / Currency Paper Conversion

برای سکه و ارز، کد مربوط به پولی کردن:

```text
Conversion Code = 8
```

است.

Buy/Sell همچنان:

```text
Customer Buy  = Kimia business side Sell
Customer Sell = Kimia business side Buy
```

است.

---

# 22. Customer Sells Coin — Real Example

مشتری:

```text
2 × Full Coin
ProductId = 10006
```

ثبت مفهومی:

```text
Operational/form code = 3
Swagger API Action = 32 (trade code confirmed; coin payload not runtime-verified)
Conversion = 8
ProductId = 10006
Quantity = 2
UnitPrice = current coin price
SumMoney = total
```

اثر:

```text
Coin Balance  -= 2
Money Balance += Total
```

---

# 23. Customer Buys Coin — Real Example

ثبت مفهومی:

```text
Operational/form code = 4
Swagger API Action = 64 (trade code confirmed; coin payload not runtime-verified)
Conversion = 8
ProductId = 10006
Quantity = ...
UnitPrice = ...
SumMoney = ...
```

اثر:

```text
Money Balance -= Total
Coin Balance  += Quantity
```

---

# 24. Coin and Currency Structural Note

در کیمیا مانده سکه و ارز ممکن است از یک ساختار حساب/دسته استفاده کنند و محصولات متعدد داخل آن باشند.

بنابراین نباید فرض کنیم:

```text
هر Product = یک WalletAccount مستقل
```

ساختار واقعی باید از:

```text
/api/voucher/balance/{id}
/api/voucher/balances
/api/voucher/transactions/{id}
```

و خروجی واقعی API استخراج شود.

---

# 25. Receiving / Paying

کدهای عملیاتی/فرم برای دریافت/پرداخت:

```text
Receive = Operational/form code 1
Pay     = Operational/form code 2
```

Swagger برای endpointهای API مرتبط از `2=Receive` و `4=Pay` استفاده می‌کند؛
کد API باید بر اساس endpoint و خروجی واقعی انتخاب شود.

---

# 26. Physical Transaction Examples

## Receive Full Coin

```text
Operational/form code = 1
Product = Full Coin
ProductId = 10006
Quantity = 2
```

## Pay Half Coin

```text
Operational/form code = 2
Product = Half Coin
ProductId = 10007
Quantity = 1
```

## Receive Bank Transfer

```text
Operational/form code = 1
Type = 7
Bank = Mellat
Account/Product Id = 105
Money Amount = ...
```

## Pay Transfer

```text
Operational/form code = 2
Type = 7
Party = Mr. Alavi
AccountId = 600
Weight = 200 g
```

## Pay Cash

```text
Operational/form code = 2
Cash
Amount = ... Rial
```

## Pay Melted Gold

```text
Operational/form code = 2
Product = Melted Gold
Weight = ...
Fineness = ...
Laboratory Receipt Number = ...
Profit = 2%
```

## Receive Miscellaneous

```text
Operational/form code = 1
Type = 2
Weight = 50 g
```

## Receive Parsian

```text
Operational/form code = 1
Product = Parsian
Weight = 500 Soot
Product Code = 500
UnitPrice = ...
Wage = ...
Quantity = ...
Total = ...
```

---

# 27. Important Physical-vs-Financial Rule

کیمیا فقط چهار مانده مالی اصلی دارد:

```text
Money
Gold
Coin
Currency
```

اما:

```text
Parsian
Bullion
Standard Melt
Jewelry
```

مانده مالی دائمی در این چهار دسته نیستند.

برای نگهداری تا زمان تحویل، Amanat لازم است.

---

# 28. Amanat Account in Kimia

برای برخی محصولات فیزیکی مانند پارسیان، شمش و ساخته‌ها ممکن است نیاز باشد حساب مشتری در ساختار امانات با `AccountId` مستقل ایجاد شود.

این موضوع **هنوز نباید حدس زده شود.**

قبل از implementation باید رفتار واقعی کیمیا و API مربوط به:

```text
AccountType = 10 (Amanat)
```

بررسی و با نمونه واقعی تأیید شود.

---

# 29. Delivery State Model

چرخه مفهومی:

```text
Purchased
    ↓
Custody
    ↓
Ready For Pickup
    ↓
Delivered
```

State Machine نهایی هنوز باید طراحی شود.

---

# 30. Kimia Account Types — Confirmed

| AccountType | Meaning |
|---:|---|
| 1 | بنکداری |
| 3 | تکفروشی |
| 5 | سرمایه و برداشت |
| 6 | بانک |
| 8 | حساب داخلی |
| 9 | ذوب |
| 10 | امانات |
| 11 | هزینه |
| 12 | کارمندان |

Enum شناخته‌شده پروژه:

```php
enum AccountType:int
{
    case Wholesale = 1;
    case Retail = 3;
    case Capital = 5;
    case Bank = 6;
    case Internal = 8;
    case Melt = 9;
    case Amanat = 10;
    case Expense = 11;
    case Employee = 12;
}
```

---

# 31. Account Groups API

Endpoint:

```http
GET /api/account/groups
```

Query:

```text
accountType
```

تعریف واقعی AccountTypeها در Swagger کیمیا آمده است.

Groupها باید از کیمیا خوانده شوند.

**نباید Groupها را از روی حدس یا لیست ثابت ایجاد کرد.**

---

# 32. Kimia Account APIs

شناخته‌شده:

```http
GET  /api/account
GET  /api/account/{id}
GET  /api/account/groups
POST /api/account
PUT  /api/account
```

پارامتر مهم:

```text
accountType
```

---

# 33. Kimia Voucher APIs

شناخته‌شده:

```http
GET    /api/voucher/balance/{id}
GET    /api/voucher/balances
GET    /api/voucher/transactions/{id}

POST   /api/voucher/adjustment
POST   /api/voucher/exchangecurrency
POST   /api/voucher/exchangegold
POST   /api/voucher/exchangemoney
POST   /api/voucher/tradebarcode
POST   /api/voucher/tradecash
POST   /api/voucher/tradecurrency
POST   /api/voucher/transfergold
POST   /api/voucher/transfermoney

DELETE /api/voucher/deleterecord
```

Request Bodyهای دقیق این POSTها باید از Swagger/Response واقعی استخراج شوند.

---

# 34. Kimia Balance API

```http
GET /api/voucher/balance/{id}
```

پارامتر:

```text
id = AccountId
includePeaks = optional
```

نمونه واقعی:

```json
[
  {
    "AccountId": 123,
    "AccountName": "حساب نقدی",
    "GroupId": 1,
    "Weight": 13.67,
    "Money": -1065900000,
    "CurrencyId": 11,
    "CurrencySymbol": "ریال",
    "GoldPeaks": [
      {
        "Date": "2026-07-26T16:36:47.249Z",
        "Days": 0,
        "Value": 0
      }
    ],
    "MoneyPeaks": [
      {
        "Date": "2026-07-26T16:36:47.249Z",
        "Days": 0,
        "Value": 0
      }
    ]
  }
]
```

---

# 35. Kimia All Balances API

```http
GET /api/voucher/balances
```

Query:

```text
groups = array<int>
```

Response شامل:

```text
AccountId
AccountName
GroupId
Weight
Money
CurrencyId
CurrencySymbol
GoldPeaks
MoneyPeaks
```

---

# 36. Kimia Transactions API

```http
GET /api/voucher/transactions/{id}
```

Parameters:

```text
id
fromDate
toDate
pageNumber
pageSize
descending
```

نکته مهم:

```text
pageNumber starts at 0
descending must be serialized in the query as the literal true or false
```

Initial live evidence captured on 2026-08-02 for `AccountId=350`:

```text
HTTP 400
{"descending":["The value '1' is not valid."]}
```

Laravel/Guzzle serializes a PHP boolean query value as `1` or `0`, while the
Kimia endpoint accepts the standard boolean literals `true` or `false`. The
canonical `VoucherRepository` therefore converts the typed boolean to those
literal query strings at the Kimia boundary. This is a transport-format rule;
it does not change any financial or trade Action mapping.

After serializing `descending` correctly, the same read-only request returned the decisive
trade evidence:

```text
RecordId 75796: Action 32, ActionName خرید, ProductId 4, ProductName پولی,
                Weight 0.2, SumMoney 36200000
RecordId 74007: Action 64, ActionName فروش, ProductId 4, ProductName پولی,
                Weight -1, SumMoney -184219914
```

This confirms the API contract independently from the operational/form codes:

```text
Customer Buy  -> Kimia Sell -> API Action 64
Customer Sell -> Kimia Buy  -> API Action 32
```

Canonical transport mapping:

```text
App\Enums\KimiaApiTradeAction
```

This evidence resolves only the numeric trade Action discrepancy. It does not authorize
or enable a live Kimia voucher write.

نمونه Response:

```json
{
  "FromDate": "...",
  "ToDate": "...",
  "TotalCount": 1000,
  "TotalPages": 3,
  "PageSize": 50,
  "PageNumber": 0,
  "Items": []
}
```

---

# 37. Transaction Item Fields

فیلدهای مشاهده‌شده:

```text
AccountId
AccountCode
AccountName
VoucherId
VoucherNumber
RecordId
Date
Action
ActionName
ProductId
ProductName
Description
Weight
Fineness
UnitPrice
Cent
GoldUnit
GoldUnitName
GoldPrice
Quantity
Weight750
SumMoney
CurrencyId
CurrencySymbol
Comment
CumulativeWeight750
CumulativeSumMoney
RelatedRecord
```

---

# 38. Transaction Field Interpretation

برای طلا:

```text
Weight
Fineness
GoldPrice
Weight750
SumMoney
```

برای سکه/ارز:

```text
ProductId
Quantity
UnitPrice
SumMoney
```

برای نوع عملیات:

```text
Action
ActionName
```

باید بررسی شوند.

---

# 39. Balance Fields

فیلدهای مهم:

```text
Weight
Money
CurrencyId
CurrencySymbol
```

مثال واقعی:

```text
Weight = 13.67
Money = -1,065,900,000
```

پس:

**Balance همیشه مثبت نیست.**

---

# 40. Current GoldPlatform Account Model

فایل:

```text
backend/app/Models/Account.php
```

فیلدهای Fillable فعلی:

```text
kimia_id
account_code
group_id
account_type
name
shop_name
national_code
mobile
tel
economic_code
address
postal_code
birthday
comment
is_visible
synced_at
```

Relations:

```php
group(): BelongsTo
user(): HasOne
```

SoftDeletes فعال است.

---

# 41. Accounts Migration

فایل:

```text
backend/database/migrations/2026_07_19_140812_create_accounts_table.php
```

ساختار اصلی:

```text
id
kimia_id UNIQUE
account_code nullable
group_id nullable → account_groups
account_type nullable
name
shop_name
national_code
mobile
tel
economic_code
address
postal_code
birthday
comment
is_visible
synced_at
timestamps
softDeletes
```

---

# 42. Current Account Sync Commands

فایل‌ها:

```text
backend/app/Console/Commands/SyncKimiaAccountsCommand.php
backend/app/Console/Commands/KimiaSyncGroups.php
```

Commands:

```bash
php artisan kimia:sync-accounts
php artisan kimia:sync-accounts --type=3
php artisan kimia:sync-groups
```

`kimia:sync-accounts` از مسیر فعال زیر استفاده می‌کند:

```text
App\Services\KimiaService::get('/api/account', ['Type' => ...])
```

گزینه `--type` تکرارپذیر است و در صورت حذف‌شدن گزینه، تمام موارد تعریف‌شده در
`AccountType` همگام می‌شوند. داده‌ها بر اساس:

```text
AccountId
AccountCode
Type
Name
```

در جدول `external_accounts` با provider برابر `kimia` ثبت یا به‌روزرسانی می‌شوند.

`kimia:sync-groups` از:

```text
AccountRepository::groups($type)
```

برای AccountTypeهای:

```text
1, 3, 5, 6, 8, 9, 10, 11, 12
```

داده می‌گیرد.

`kimia:sync-accounts` برای endpoint حساب‌ها پارامتر `Type` را می‌فرستد.

`kimia:sync-groups` برای endpoint گروه‌ها پارامتر `accountType` را می‌فرستد.

> این تفاوت نام پارامترها در Swagger رسمی کیمیا ثبت شده است و نباید یکسان‌سازی یا حدس زده شود.
>
> این Commandها باید با API واقعی دوباره تست شوند؛ وجود Command به معنی درست بودن Mapping نهایی نیست.

---

# 43. Current Account Sync Problem

آخرین تست Tinker:

```php
\App\Models\Account::count();
```

نتیجه:

```text
0
```

و:

```php
\App\Models\Account::select(
    'kimia_id',
    'account_code',
    'account_type',
    'name'
)->orderBy('account_type')->get()->toArray();
```

نتیجه:

```text
[]
```

بنابراین فعلاً:

```text
Kimia → GoldPlatform Account Sync
```

موفق نشده یا هنوز اجرا نشده است.

**این نتیجه به تنهایی ثابت نمی‌کند API کیمیا خراب است.**

باید مسیر کامل بررسی شود:

```text
Kimia API
    ↓
Account API
    ↓
Raw Response
    ↓
AccountRepository
    ↓
Sync Command
    ↓
Account Model
    ↓
Database
```

---

# 44. Current Kimia Repository

فایل:

```text
backend/app/Repositories/Kimia/AccountRepository.php
```

مسئولیت مفهومی:

- دریافت Accounts
- دریافت Account
- دریافت Groups
- Create Account
- Update Account
- دریافت داده‌های مرتبط با Account

قبل از تغییر Repository باید Response واقعی endpoint مربوطه بررسی شود.

---

# 45. Current Kimia Service

فایل:

```text
backend/app/Services/KimiaService.php
```

وظیفه مفهومی:

- HTTP Client
- Base URL
- Basic Authentication
- Timeout
- Retry
- GET
- POST
- PUT
- DELETE
- Logging

تست اتصال فعلی:

```php
public function test(): Response
{
    return $this->client()->get('/swagger/v1/swagger.json');
}
```

---

# 46. Wallet Architecture — Current Project

ساختار فعلی:

```text
Wallet
   └── WalletAccount
           └── WalletTransaction
```

اما Wallet فعلی نباید بدون تصمیم معماری نهایی به عنوان جایگزین مستقیم Kimia در نظر گرفته شود.

---

# 47. WalletAccount

Migration:

```text
wallet_accounts
```

فیلدها:

```text
id
wallet_id
code
title
balance
blocked_balance
is_active
created_at
updated_at
```

Unique:

```text
(wallet_id, code)
```

---

# 48. Current Wallet Account Enum

Enum فعلی:

```php
enum WalletAccountType: string
{
    case CASH = 'cash';
    case GOLD = 'gold';
    case COIN = 'coin';
    case CURRENCY = 'currency';
}
```

**هشدار معماری:**

این Enum به تنهایی حقیقت نهایی ساختار Kimia نیست.

به‌خصوص:

```text
Coin
Currency
```

ممکن است چند Product مختلف داشته باشند.

ساختار نهایی باید با API کیمیا تطبیق داده شود.

---

# 49. Wallet Balance

مفاهیم فعلی:

```text
balance
blocked_balance
available_balance
```

رابطه:

```text
available_balance =
balance - blocked_balance
```

---

# 50. WalletService

فایل:

```text
backend/app/Services/Wallet/WalletService.php
```

عملیات فعلی:

```text
deposit()
withdraw()
```

با:

```text
DB::transaction()
lockForUpdate()
```

انجام می‌شوند.

محاسبات اعشاری:

```text
bcadd
bcsub
bccomp
```

---

# 51. WalletTransaction

مدل:

```text
WalletTransaction
```

فیلدهای فعلی:

```text
wallet_account_id
wallet_type
type
amount
balance_after
reference
description
```

Typeهای فعلی:

```text
deposit
withdraw
buy
sell
refund
adjustment
block
unblock
```

Migration اولیه از `wallet_id` به `wallet_account_id` تغییر کرده است:

```text
2026_07_22_155644_update_wallet_transactions_for_wallet_accounts.php
```

ساختار فعلی باید بر اساس:

```text
wallet_account_id
```

در نظر گرفته شود.

---

# 52. Wallet ≠ Kimia

نباید بدون تصمیم معماری فرض کنیم Wallet داخلی GoldPlatform جایگزین Kimia است.

رابطه باید صریحاً طراحی شود:

```text
GoldPlatform Wallet
        ↓
Kimia Account
        ↓
Kimia Balance
        ↓
Kimia Voucher
```

اینکه کدام سیستم Source of Truth نهایی باشد، باید بر اساس معماری پذیرفته‌شده تعیین شود.

---

# 53. Financial Transaction vs Custody Transaction

این تفکیک حیاتی است.

## Financial Transaction

دارایی:

- Money
- Gold
- Coin
- Currency

اثر:

```text
Balance increases/decreases
```

## Custody Transaction

دارایی:

- Parsian
- Bullion
- Melt
- Jewelry

اثر:

```text
Custody quantity/state changes
```

تا زمان تحویل.

نباید این دو را در یک Balance مدل کرد.

---

# 54. GoldPlatform UX Mapping

Backend مفاهیم:

```text
Account
AccountGroup
Wallet
WalletAccount
Balance
Voucher
Ledger
Settlement
Custody
Inventory
Delivery
```

Frontend مفاهیم:

```text
دارایی من
پول
طلا
سکه
ارز
امانات من
خرید
فروش
تبدیل به طلا
تبدیل به پول
تحویل
```

---

# 55. UI Must Not Expose Kimia Codes

هرگز در UI:

```text
Action 4
Action 8
AccountType 10
ProductId 10006
GroupId
VoucherId
```

به عنوان اصطلاح اصلی نمایش داده نشود.

Backend می‌تواند آن‌ها را نگه دارد.

Frontend باید مفهوم انسانی نمایش دهد.

---

# 56. Existing Prototype

Prototype HTML قبلی به عنوان **Reference UI** نگهداری می‌شود.

ویژگی‌های شناخته‌شده:

- اتصال مستقیم به API قیمت
- پرداخت از صندوق پولی
- پرداخت از صندوق طلایی
- سبد خرید
- صدور فاکتور
- دسته‌بندی محصولات
- آماده اتصال Backend

Prototype مستقیماً وارد Laravel نمی‌شود.

ابتدا باید بر اساس Design System پروژه بازطراحی شود.

---

# 57. Critical Domain Flow — Financial Trading

## Gold

```text
Money
   ↓
Paper Gold Buy
   ↓
Gold Balance ↑
Money Balance ↓
```

یا:

```text
Gold Balance
   ↓
Paper Gold Sell
   ↓
Gold Balance ↓
Money Balance ↑
```

## Coin

```text
Money
   ↓
Coin Buy
   ↓
Coin Balance ↑
Money Balance ↓
```

یا:

```text
Coin Balance
   ↓
Coin Sell
   ↓
Coin Balance ↓
Money Balance ↑
```

## Currency

همین مفهوم برای Currency با Product/Asset واقعی کیمیا.

---

# 58. Critical Domain Flow — Physical Purchase

```text
Money Balance OR Gold Balance
            ↓
     Physical Purchase
            ↓
      Create Amanat
            ↓
     Ready For Pickup
            ↓
          Delivery
```

---

# 59. Physical Purchase Example

مشتری:

```text
Parsian 500 Soot
```

خرید:

```text
Money Balance OR Gold Balance
```

بعد:

```text
Amanat:
Product = Parsian
Weight = 500 Soot
Status = Ready For Pickup
```

تا زمان تحویل:

```text
Amanat remains active
```

بعد:

```text
Delivered
```

---

# 60. Required Future Modules

معماری باید برای این ماژول‌ها آماده باشد:

1. Trading Engine
2. Settlement
3. Ledger
4. Amanat / Custody
5. Inventory
6. Delivery
7. Kimia Synchronization
8. Customer Groups
9. Permissions
10. OMS
11. Multi Branch
12. API Services
13. Marketplace

---

# 61. Required Kimia Investigation Before Trading Engine

قبل از پیاده‌سازی کامل Trading Engine باید موارد زیر با API واقعی مشخص شوند:

- Account discovery
- Account groups
- Account types
- Customer Account mapping
- AccountId strategy
- GroupId strategy
- Balance retrieval
- Gold balance
- Money balance
- Coin balance
- Currency balance
- Product discovery
- ProductId mapping
- Paper Gold Buy
- Paper Gold Sell
- Coin Buy
- Coin Sell
- Currency Buy
- Currency Sell
- Amanat Account
- Physical Purchase
- Physical Delivery
- Credit Trading
- Settlement
- Voucher creation
- Voucher response
- Error handling
- Idempotency
- Delete/rollback behavior

---

# 62. Exact Kimia Data Needed Per Financial Operation

قبل از implementation هر عملیات باید این موارد مشخص باشند:

1. AccountId
2. GroupId
3. AccountType
4. ProductId
5. Action
6. نوع مانده
7. نوع معامله
8. Weight
9. Fineness
10. UnitPrice
11. GoldPrice
12. SumMoney
13. Quantity
14. CurrencyId
15. Endpoint
16. Request Body
17. Response
18. Error Response
19. اثر روی Balance
20. اثر روی Transaction History

---

# 63. No Guessing — Mandatory Workflow

قبل از هر تغییر مهم:

### Step 1
این فایل خوانده شود.

### Step 2
فایل‌های فعلی پروژه بررسی شوند.

### Step 3
Swagger/OpenAPI کیمیا بررسی شود.

### Step 4
اگر لازم است API واقعی Call شود.

### Step 5
Raw Response بررسی شود.

### Step 6
Business Rule استخراج شود.

### Step 7
اگر تناقض وجود دارد، توقف و اعلام تناقض.

### Step 8
Implementation.

### Step 9
Test.

### Step 10
Git Diff.

### Step 11
Commit.

### Step 12
ثبت نتیجه در Documentation.

---

# 64. Never Silently Change These Rules

بدون تأیید کارفرما تغییر نده:

- چهار Financial Balance
- Money
- Gold
- Coin
- Currency
- Amanat
- Credit Trading
- Customer Groups
- Permission Rules
- Action Codes
- Product Codes
- Conversion Codes
- AccountType
- Account Groups
- Gold Conversion
- Coin Conversion
- Currency Conversion
- Settlement
- Source of Truth
- Kimia Integration Boundary

---

# 65. Owner-Confirmed Operational/Form Code Table

```text
1 = دریافت
2 = پرداخت
3 = خرید
4 = فروش
7 = حواله
8 = پولی کردن سکه و ارز
```

این جدول کدهای روند عملیاتی است. برای payload API، Swagger کدهای bit-flag از جمله
`32=خرید` و `64=فروش` را تعریف می‌کند.

---

# 66. Known Gold Paper Mapping

```text
Customer Buy:
Kimia business side = Sell
Operational/form code = 4
Swagger API Action = 64 (runtime-confirmed from AccountId 350)

Customer Sell:
Kimia business side = Buy
Operational/form code = 3
Swagger API Action = 32 (runtime-confirmed from AccountId 350)

Money Product:
Code = 4

Fineness:
750
```

اثر مفهومی:

```text
Gold Buy:
Money ↓
Gold ↑

Gold Sell:
Gold ↓
Money ↑
```

---

# 67. Known Coin/Currency Mapping

```text
Customer Buy:
Kimia business side = Sell
Operational/form code = 4
Swagger API Action = 64 (trade code confirmed; coin/currency payload not runtime-verified)

Customer Sell:
Kimia business side = Buy
Operational/form code = 3
Swagger API Action = 32 (trade code confirmed; coin/currency payload not runtime-verified)

Conversion:
Code = 8
```

اثر مفهومی:

```text
Buy:
Money ↓
Coin/Currency ↑

Sell:
Coin/Currency ↓
Money ↑
```

---

# 68. Known Physical Receive/Pay Mapping

```text
Receive = Operational/form code 1
Pay     = Operational/form code 2

Swagger API receive = 2
Swagger API pay     = 4
```

API transport values remain endpoint-specific and require runtime confirmation before write.

---

# 69. Important Terminology

در UX، «طلایی کردن» یعنی:

```text
تبدیل مانده پولی به طلای معامله‌ای / Paper Gold
```

«پولی کردن» در زمینه طلا/سکه/ارز یعنی تبدیل دارایی معامله‌ای به مانده پولی، با Mapping واقعی کیمیا.

اما:

```text
Operational/form code 4 ≠ پولی کردن
```

Operational/form code 4 فقط:

```text
فروش
```

است.

---

# 70. Domain Boundary

## GoldPlatform

مسئول:

- UX
- Trading Workflow
- Order
- Cart
- Customer Experience
- Custody Workflow
- Delivery Workflow
- Permission
- Customer Groups
- Application Logic
- Integration Orchestration

## Kimia

مرجع اطلاعات/ثبت مالی کیمیا در محدوده‌ای که API و معماری نهایی پروژه تعیین می‌کند.

GoldPlatform نباید رفتار کیمیا را بازتعریف یا جعل کند.

---

# 71. Adapter Boundary

Business Logic نباید در Controllerها مستقیماً HTTP Call کیمیا انجام دهد.

ساختار مفهومی:

```text
Controller
    ↓
Application Service
    ↓
Domain Service
    ↓
Kimia Integration / Adapter
    ↓
Kimia API
```

هدف:

- تست‌پذیری
- جلوگیری از Coupling
- جلوگیری از پخش شدن Codeهای کیمیا
- امکان تغییر API
- ثبت Mappingها در یک محل

---

# 72. Kimia Adapter Rules

Adapter باید در آینده مسئول Mapping این موارد باشد:

```text
GoldPlatform Domain
        ↓
Kimia Request
```

و:

```text
Kimia Response
        ↓
GoldPlatform Domain
```

نباید Codeهای خام کیمیا در سراسر پروژه پخش شوند.

---

# 73. Dynamic Product Rule

این موارد نباید Hard-Code شوند مگر صرفاً برای تست:

```text
Coin ProductId
Currency ProductId
Parsian ProductCode
Gold Product
```

محصول واقعی باید از داده/تنظیمات معتبر استخراج شود.

نمونه:

```text
10006 = Full Coin
10007 = Half Coin
```

فقط به عنوان نمونه فعلی.

---

# 74. Dynamic Group Rule

Account Groupها باید از:

```http
GET /api/account/groups?accountType=...
```

یا endpoint واقعی متناظر دریافت شوند.

نباید گروه‌ها را از روی حدس ساخت.

---

# 75. Important Error: Empty Accounts

اگر:

```php
Account::count() === 0
```

اولین فرض نباید:

```text
API broken
```

باشد.

باید این زنجیره بررسی شود:

```text
1. KimiaService connection
2. Base URL
3. Authentication
4. Account endpoint
5. Raw HTTP status
6. Raw response body
7. JSON structure
8. Repository mapping
9. Command execution
10. Model fillable
11. Database migration
12. updateOrCreate keys
13. database connection
```

---

# 76. What Must Never Happen Again

AI/Developer نباید:

- از روی اسم Endpoint حدس بزند.
- از روی تجربه نرم‌افزارهای طلا Business Rule بسازد.
- Codeهای کیمیا را بدون مثال واقعی معنی کند.
- Action Code را با Product Code اشتباه بگیرد.
- Coin/Currency را بدون API به چند Wallet ثابت تبدیل کند.
- Amanat را با Gold Balance قاطی کند.
- Operational/form code 4 را «پولی کردن» بنامد.
- API Request Body را حدس بزند.
- Response را بدون دیدن Raw JSON فرض کند.
- معماری پذیرفته‌شده را بی‌دلیل عوض کند.
- تصمیم قبلی را silently تغییر دهد.
- به جای توقف در ابهام، implementation حدسی انجام دهد.

---

# 77. Current Repository Reference

Repository عمومی پروژه:

`1alirezabahramian/GoldPlatform`

ساختار فعلی Repository شامل:

```text
backend/
docker/
docs/
swagger.json
README.md
docker-compose.yml
docs_list.txt
```

Repository در GitHub موجود است؛ شاخه پایه `main` و شاخه فعال این مرحله
`audit/kimia-foundation` است.

---

# 78. Known Project Files

## Enums

```text
backend/app/Enums/AccountType.php
backend/app/Enums/WalletAccountType.php
```

## Kimia

```text
backend/app/Services/KimiaService.php
backend/app/Repositories/Kimia/AccountRepository.php
backend/app/Repositories/Kimia/VoucherRepository.php
backend/app/Console/Commands/SyncKimiaAccountsCommand.php
backend/app/Console/Commands/KimiaSyncGroups.php
backend/app/Console/Commands/KimiaInspectTransactions.php
```

## Models

```text
backend/app/Models/Account.php
backend/app/Models/AccountGroup.php
backend/app/Models/Wallet.php
backend/app/Models/WalletAccount.php
backend/app/Models/WalletTransaction.php
```

## Wallet Services

```text
backend/app/Services/Wallet/WalletService.php
backend/app/Services/Auth/RegistrationService.php
```

## Accounts Migration

```text
backend/database/migrations/2026_07_19_140812_create_accounts_table.php
```

## Wallet Transaction Migration

```text
backend/database/migrations/2026_07_22_155644_update_wallet_transactions_for_wallet_accounts.php
```

---

# 79. Current Development State

Known state:

- Laravel 12
- Docker-based backend
- Backend under development
- Frontend not yet finalized
- Kimia Integration is current priority
- Account sync has not yet populated local `accounts`

Current database check:

```text
Account::count() = 0
```

---

# 80. Tomorrow's First Mission

فردا نباید مستقیم Trading Engine را حدس بزنیم.

اول باید **Kimia Integration را با داده واقعی تثبیت کنیم.**

ترتیب پیشنهادی:

```text
1. Verify Kimia connection
        ↓
2. Call /api/account
        ↓
3. Save raw response
        ↓
4. Inspect actual JSON
        ↓
5. Compare AccountRepository mapping
        ↓
6. Fix sync only if required
        ↓
7. Run kimia:sync-accounts
        ↓
8. Verify Account::count()
        ↓
9. Discover Account Groups
        ↓
10. Discover customer account mapping
        ↓
11. Call balance API for real customer
        ↓
12. Map Money / Gold / Coin / Currency
        ↓
13. Read real transactions
        ↓
14. Verify Action/Product mappings
        ↓
15. Only then implement financial trading
        ↓
16. Then Amanat
        ↓
17. Then Delivery
        ↓
18. Then Credit Trading / Settlement
```

---

# 81. Tomorrow's Required Tests

حداقل این سناریوها باید با API واقعی تست شوند:

## Test A — Account

```text
GET /api/account
```

هدف:

- Response واقعی
- AccountId
- AccountCode
- Type
- Name
- Group
- Customer mapping

## Test B — Groups

```text
GET /api/account/groups?accountType=...
```

هدف:

- GroupId
- Group Name
- AccountType

## Test C — Balance

```text
GET /api/voucher/balance/{id}
```

هدف:

- Money
- Weight
- Coin/Currency behavior
- CurrencyId
- CurrencySymbol
- Peaks

## Test D — Transactions

```text
GET /api/voucher/transactions/{id}
```

هدف:

- Action
- ProductId
- Quantity
- Weight
- Fineness
- UnitPrice
- GoldPrice
- SumMoney
- Weight750

## Test E — Existing Real Voucher

یک Transaction واقعی از مشتری پیدا شود و با مثال‌های ثبت‌شده در این فایل تطبیق داده شود.

---

# 82. Architecture Acceptance Criteria

معماری فردا زمانی قابل قبول است که:

### Financial

- چهار Balance اصلی حفظ شده باشد.
- منفی بودن Balance پشتیبانی شود.
- Coin Dynamic باشد.
- Currency Dynamic باشد.
- Paper Gold از Physical Product جدا باشد.

### Amanat

- Physical Product در Amanat باشد.
- Amanat Balance مالی نباشد.
- Ready For Pickup و Delivered قابل تشخیص باشند.

### Kimia

- Account mapping واقعی باشد.
- Group mapping واقعی باشد.
- Balance mapping واقعی باشد.
- Product mapping واقعی باشد.
- Action mapping واقعی باشد.
- Request/Response واقعی باشد.
- API codeها در Adapter متمرکز باشند.

### UX

- پیچیدگی کیمیا در Frontend دیده نشود.
- کاربر فقط مفاهیم انسانی ببیند.

### Safety

- هیچ Business Rule از حدس ساخته نشده باشد.
- هیچ تصمیم قبلی بدون تأیید تغییر نکرده باشد.

---

# 83. Architecture Invariants

این موارد Invariant هستند مگر اینکه کارفرما صریحاً تغییر دهد:

```text
Money
Gold
Coin
Currency
```

چهار Financial Balance.

```text
Parsian
Bullion
Melt
Jewelry
```

Physical / Amanat.

```text
Operational/form code 1 = Receive
Operational/form code 2 = Pay
Operational/form code 3 = Buy
Operational/form code 4 = Sell
Operational/form code 7 = Transfer
Operational/form code 8 = Coin/Currency Monetization
```

این Invariant مربوط به معنای روند عملیاتی است. قرارداد عددی API جدا و endpoint-specific است.

و:

```text
Gold Paper Money Product = 4
```

این دو مفهوم جدا هستند.

---

# 84. Final Ground Truth Diagram

```text
                         GoldPlatform
                              │
              ┌───────────────┴───────────────┐
              │                               │
       Financial Assets                  Physical Assets
              │                               │
      ┌───────┼────────┬───────┐              │
      │       │        │       │           Amanat
    Money    Gold     Coin   Currency        │
      │       │        │       │       ┌──────┼──────┬──────┐
      │       │        │       │       │      │      │      │
      │       │        │       │    Parsian Bullion Melt  Jewelry
      │       │        │       │
      └───────┴────────┴───────┘
                  │
               Trading
                  │
        ┌─────────┼─────────┐
        │         │         │
       Gold      Coin    Currency
        │         │         │
        └─────────┴─────────┘
                  │
                Kimia
                  │
       Account / Balance / Voucher
```

---

# 85. Final Golden Rule

در GoldPlatform:

```text
Kimia Reality
      ↓
Real API / Swagger
      ↓
Verified Mapping
      ↓
Domain Model
      ↓
Backend
      ↓
Simple UX
```

نه:

```text
Guess
  ↓
Code
  ↓
Hope
```

---

# 86. Session Handover Instruction

اگر این فایل به یک AI/Developer جدید داده شد، باید ابتدا تأیید کند که این موارد را فهمیده است:

1. چهار Financial Balance چیست.
2. Amanat چیست.
3. چرا Physical Product با Financial Balance فرق دارد.
4. تفاوت Operational/Form Code 4 و API Action چیست.
5. Product/Money Code 4 چیست.
6. Code 8 چیست.
7. تفاوت کدهای عملیاتی 1/2 و Actionهای API endpoint چیست.
8. Coin/Currency چرا Dynamic هستند.
9. چرا AccountId و GroupId باید از کیمیا استخراج شوند.
10. چرا API Request Body را نباید حدس زد.
11. چرا `Account::count() = 0` به تنهایی نشانه خرابی API نیست.
12. چرا قبل از Trading Engine باید Integration واقعی کیمیا تثبیت شود.

سپس باید فقط بر اساس همین Ground Truth و داده واقعی ادامه دهد.

---

# 87. Stop Conditions

در هر یک از شرایط زیر، implementation متوقف شود:

- Endpoint نامشخص است.
- Request Body نامشخص است.
- Response واقعی نداریم.
- Code معنی مشخصی ندارد.
- دو مستند با هم تناقض دارند.
- Account mapping مشخص نیست.
- Product mapping مشخص نیست.
- Balance semantics مشخص نیست.
- Source of Truth مشخص نیست.
- Business Rule جدیدی لازم است ولی تأیید نشده.

در این شرایط:

```text
STOP
↓
SHOW EXACT UNKNOWN
↓
ASK ONE PRECISE QUESTION
↓
WAIT
```

نه حدس، نه workaround مخفی، نه تغییر معماری.

---

# 88. Document Maintenance

این فایل باید با هر تصمیم مهم به‌روزرسانی شود.

هر تغییر مهم باید شامل:

```text
Date
Decision
Reason
Evidence
Affected Modules
Migration Impact
API Impact
```

باشد.

اگر تصمیم جدیدی جایگزین تصمیم قبلی شد:

```text
Old Rule
New Rule
Reason
Date
```

ثبت شود.

---

# 89. Current Status

```text
Project:
GoldPlatform

Current Priority:
Kimia Integration

Current Critical Issue:
Account Sync = 0 records

Architecture:
Financial Assets + Physical Custody

Financial Assets:
Money / Gold / Coin / Currency

Physical:
Amanat

Trading:
Gold / Coin / Currency

Paper Gold:
Operational/form code 3/4 + Money Product 4
Swagger API Action 32/64 (runtime-confirmed from AccountId 350)

Coin/Currency Conversion:
Operational/form code 3/4 + Code 8
Swagger API Action 32/64 (trade code confirmed; coin/currency payload still unverified)

Physical Receive/Pay:
Operational/form code 1/2
Swagger endpoint-specific Action 2/4 (runtime confirmation pending)

Customer Groups:
Normal / VIP Credit

Core Principle:
Complex Backend — Simple Frontend

Development Rule:
NO GUESSING
```

---

# 90. Final Message to Future Developer / AI

این پروژه را با حدس جلو نبر.

اگر چیزی در این فایل مشخص شده است، همان را اجرا کن.

اگر چیزی مشخص نشده است، از API واقعی، Swagger یا کارفرما استخراج کن.

اگر تناقض دیدی، توقف کن.

اگر چیزی را نمی‌دانی، دقیقاً همان بخش ناشناخته را بپرس.

**GoldPlatform باید از واقعیت کسب‌وکار و واقعیت کیمیا ساخته شود، نه از حدس AI.**

---

# 91. Backend Stabilization Audit — 2026-08-02

## Confirmed Kimia Account Query Fix

```text
GET /api/account         → Type
GET /api/account/groups  → accountType
```

Evidence:

- `swagger.json`
- بررسی مسیر واقعی `AccountRepository`
- وضعیت قبلی `Account::count() = 0`

Changes:

- Account sync accepts repeatable, validated `--type` options and synchronizes all defined `AccountType` cases when no option is supplied.
- Account fields documented by Swagger are mapped into the local `external_accounts` model.
- Invalid account rows without `AccountId` are skipped and counted instead of causing a silent partial failure.
- Legacy duplicate Kimia implementations under `app/Clients` and `app/Services/kimia` were removed. The active synchronization commands currently use:

```text
App\Services\KimiaService
App\Repositories\Kimia\AccountRepository
```

The pre-existing `App\Integrations\Kimia` tree was preserved for a separate architecture
review because it belongs to earlier commits on `audit/kimia-foundation` and is not part of
this stabilization deletion scope.

- Kimia configuration now has one canonical source: `config/services.php`, with placeholder environment keys documented in `.env.example`.

Migration impact:

```text
None
```

API impact:

```text
Correct query parameter for GET /api/account
No change to financial write operations
```

## Resolved Trade Action and Remaining Write Stop Conditions

The previous Trading Engine stop conditions were resolved from approved project rules and
owner confirmation on 2026-08-02:

1. Customer Buy in GoldPlatform = business sells to customer in Kimia.
2. Customer Sell in GoldPlatform = business buys from customer in Kimia.
3. Money, Gold, Coin, and Currency balances may be negative only for approved credit groups
   and only within their configured limits. Custody remains a separate physical asset model.

The semantic trade direction and numeric API transport encoding are resolved. A real
read-only response from account `350` confirmed `Action 32=خرید` and `Action 64=فروش`,
matching Swagger while remaining separate from operational/form codes `3/4`.

Canonical implementation contract:

```text
App\Enums\KimiaTradeSide
App\Enums\KimiaApiTradeAction
```

Full decision record:

```text
docs/ADR/ADR-023-kimia-customer-trade-action-mapping.md
```

## Read-Only Transaction Evidence Path

To resolve the transport Action discrepancy without writing any financial document, the
canonical read path is:

```text
App\Repositories\Kimia\VoucherRepository::transactions()
GET /api/voucher/transactions/{accountId}
```

The inspection command is:

```text
php artisan kimia:inspect-transactions {accountId} --page=0 --size=50
```

Owner-confirmed evidence account (2026-08-02):

```text
AccountId = 350
php artisan kimia:inspect-transactions 350 --page=0 --size=50
```

This account identifier is approved only for the read-only transaction inspection above.
The owner-run response on 2026-08-02 captured the required records:

```text
RecordId 75796 -> Action 32 -> خرید -> ProductId 4 (پولی)
RecordId 74007 -> Action 64 -> فروش -> ProductId 4 (پولی)
```

The API mapping is therefore final: customer `buy` maps to Kimia `sell`/`64`, and customer
`sell` maps to Kimia `buy`/`32`. Operational/form codes `3/4` remain a separate contract.
Live voucher writes remain disabled until the complete payload and write workflow are
verified independently.

It displays the raw evidence fields `RecordId`, `Action`, `ActionName`, `ProductId`,
`ProductName`, `Weight`, `Quantity`, and `SumMoney`. The command is read-only and does not
create, edit, or delete a Kimia voucher.

## Authentication/SMS Structural Stabilization

- Active OTP/SMS classes were aligned with their PSR-4 paths.
- Missing `SendOtpRequest`, `SmsProvider`, and `SmsResult` contracts were added.
- Unreferenced duplicate Auth/OTP/SMS implementations were removed.
- No OTP duration, attempt limit, provider behavior, or customer-facing route was intentionally changed in this structural pass.
- Secure OTP-at-rest design remains pending because changing it affects security behavior and database schema.

## Authentication Decisions Still Required

The current repository contains confirmed implementation conflicts that were not silently resolved:

1. Project rules describe OTP-only authentication, while `RegisterRequest` and `RegistrationService` currently require/use a password.
2. `RegistrationService` writes `first_name` and `last_name`, but no current users migration creates those columns.
3. `UserObserverTest` expects automatic creation of nine wallet accounts, while the active observer is empty and registration currently creates two default accounts.
4. The active `verify-otp` and `logout` controller methods are still incomplete.

These items affect security and customer experience and require an approved canonical authentication flow before implementation.

---

# 92. Full Automated Test Verification — 2026-08-02

The owner executed the automated tests inside the project PHP Docker container after pulling
the Kimia trade-action mapping checkpoint published as GitHub commit `98a7c40`.

Canonical full-suite command and result:

```text
docker exec -it goldplatform_php php artisan test

Tests:    23 passed (160 assertions)
Duration: 19.52s
Failures: 0
```

The successful full suite covered:

- PSR-4 application path/class compliance.
- Kimia account and account-group query parameter contracts.
- Customer trade direction and API transport mapping (`buy -> 64`, `sell -> 32`).
- Rejection of operational/form codes `3/4` as API trade Actions.
- Voucher transaction endpoint, boolean query serialization, and pagination validation.
- Account, coin, currency, and group synchronization without duplicates.
- User wallet/default-account observer behavior.
- Basic application response health.

Supporting targeted evidence executed before the canonical full suite:

```text
KimiaApiTradeActionTest                 3 passed (6 assertions)
tests/Unit/Kimia                       11 passed (16 assertions)
SyncKimiaCurrenciesCommandTest          2 passed (13 assertions)
Psr4ComplianceTest                      1 passed (70 assertions)
Unit suite, run 1                      13 passed (87 assertions)
Unit suite, run 2                      13 passed (87 assertions)
```

These are overlapping verification runs and must not be added to the canonical full-suite
totals.

Safety boundary:

- The automated tests did not create, edit, or delete a live Kimia financial voucher.
- Live voucher writing remains disabled.
- The successful suite does not resolve the still-unverified live write payload,
  idempotency, retry, failure-handling, and posting-time rules.

Conclusion:

```text
Automated project test suite: PASS
Tests: 23
Assertions: 160
Failures: 0
```
