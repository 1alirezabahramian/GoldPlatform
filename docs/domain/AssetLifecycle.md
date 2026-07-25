# Asset Lifecycle

# هدف

این سند چرخه کامل حرکت دارایی در سامانه جامع معاملات و مدیریت دارایی طلا را مشخص می‌کند.

هر دارایی مشتری در طول عمر خود ممکن است بین چند وضعیت مختلف جابه‌جا شود.

هیچ انتقالی خارج از این سند مجاز نیست.

---

# انواع دارایی

- Cash
- Gold 18K
- Gold 24K
- Coin
- Bullion
- Parsian
- Credit
- Custody
- Frozen
- Pending

---

# وضعیت‌های دارایی

Available

↓

Pending

↓

Frozen

↓

Executed

↓

Settlement

↓

Custody

↓

Delivered

↓

Archived

---

# انتقال‌های مجاز

## شارژ کیف پول

Bank

↓

Cash

---

## خرید طلا

Cash

↓

Gold18

---

## فروش طلا

Gold18

↓

Cash

---

## خرید با اعتبار

Credit

↓

Gold18

---

## بازپرداخت اعتبار

Cash

↓

Credit

---

## انتقال به امانات

Gold18

↓

Custody

---

## خروج از امانات

Custody

↓

Gold18

---

## فروش از امانات

Custody

↓

Cash

---

## رزرو سفارش

Available

↓

Frozen

---

## لغو سفارش

Frozen

↓

Available

---

## اجرای معامله

Frozen

↓

Executed

---

## شروع تسویه

Executed

↓

Settlement

---

## تحویل کالا

Settlement

↓

Delivered

---

## بایگانی

Delivered

↓

Archived

---

# انتقال‌های غیرمجاز

Cash

×

Custody

بدون خرید طلا

---

Credit

×

Delivered

---

Pending

×

Archived

---

Frozen

×

Delivered

---

# قوانین

هر انتقال باید:

- Transaction داشته باشد.
- Ledger ثبت کند.
- Audit ثبت کند.
- User مشخص داشته باشد.
- Timestamp داشته باشد.

---

# ارتباط با Wallet

تمام انتقال‌ها از طریق Wallet انجام می‌شود.

---

# ارتباط با Trading

Trading فقط انتقال‌های مجاز این سند را اجرا می‌کند.

---

# ارتباط با Settlement

Settlement آخرین مرحله چرخه دارایی است.

---

# ارتباط با OMS

OMS وضعیت سفارش را کنترل می‌کند.

AssetLifecycle وضعیت دارایی را کنترل می‌کند.

---

# ارتباط با Kimia

فقط انتقال‌های نهایی در Kimia ثبت می‌شوند.

انتقال‌های داخلی Wallet در Kimia ذخیره نمی‌شوند.

---

# آینده

- چند انبار
- چند خزانه
- چند شعبه
- چند نوع فلز
- ارز خارجی
- کریپتو
- NFT طلا
- توکن طلا