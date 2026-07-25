# Settlement Domain

# هدف

Settlement مسئول نهایی‌سازی معامله پس از اجرای آن است.

پس از اینکه Trading معامله را اجرا کرد، مسئولیت سیستم به Settlement منتقل می‌شود.

Settlement تعیین می‌کند مشتری چه چیزی دریافت می‌کند، چه چیزی تحویل می‌دهد و چه چیزی در Kimia ثبت می‌شود.

---

# مسئولیت‌ها

- تسویه مالی
- تسویه طلایی
- تحویل فیزیکی
- انتقال به امانات
- خروج از امانات
- رزرو موجودی انبار
- آزادسازی موجودی
- ثبت سند در Kimia
- پایان چرخه معامله

---

# انواع Settlement

## Cash Settlement

پرداخت یا دریافت وجه

---

## Gold Settlement

انتقال طلا

---

## Custody Settlement

انتقال طلا به صندوق امانات

---

## Physical Delivery

تحویل فیزیکی کالا

---

## Internal Transfer

انتقال داخلی بین حساب‌ها

---

# وضعیت تسویه

Created

↓

Waiting

↓

Processing

↓

Reserved

↓

Delivered

↓

Completed

یا

Cancelled

یا

Failed

---

# گردش عملیات

Order

↓

Trading

↓

Wallet

↓

Settlement

↓

Kimia

↓

Completed

---

# تحویل فیزیکی

اگر مشتری درخواست تحویل کالا داشته باشد

سیستم

- کالا را رزرو می‌کند
- حواله صادر می‌کند
- تحویل را ثبت می‌کند
- موجودی انبار را کاهش می‌دهد

---

# امانات

اگر مشتری کالا را تحویل نگیرد

می‌تواند گزینه

انتقال به صندوق امانات

را انتخاب کند.

در این حالت

Gold Account

↓

Custody Account

---

# فروش از امانات

در زمان فروش

Custody

↓

Settlement

↓

Trading

↓

Cash

---

# کنترل‌ها

قبل از Settlement بررسی می‌شود

- موجودی انبار
- موجودی امانات
- وضعیت پرداخت
- وضعیت سفارش
- وضعیت مشتری
- محدودیت‌های امنیتی

---

# ارتباط با Wallet

Settlement فقط از طریق Wallet دارایی را جابجا می‌کند.

هیچ موجودی مستقلی نگهداری نمی‌شود.

---

# ارتباط با OMS

OMS پایان معامله را اعلام می‌کند.

Settlement نتیجه نهایی را ثبت می‌کند.

---

# ارتباط با Kimia

در پایان عملیات

Settlement سند مناسب را در Kimia ثبت می‌کند.

نمونه‌ها

- سند فروش
- سند خرید
- حواله انبار
- رسید انبار
- انتقال امانات

---

# API

POST /settlements

GET /settlements

GET /settlements/{id}

POST /settlements/confirm

POST /settlements/cancel

POST /settlements/delivery

POST /settlements/custody

---

# توسعه آینده

- چند انبار
- چند شعبه
- چند خزانه
- حمل بیمه‌شده
- رهگیری مرسوله
- امضای دیجیتال تحویل
- QR تحویل کالا
- زمان‌بندی تحویل