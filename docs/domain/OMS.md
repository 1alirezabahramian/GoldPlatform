# Order Management System (OMS)

# هدف

OMS مغز سامانه جامع معاملات و مدیریت دارایی طلا است.

تمام سفارش‌های مشتری ابتدا وارد OMS می‌شوند و OMS تصمیم می‌گیرد سفارش چگونه پردازش شود.

هیچ ماژولی اجازه ندارد مستقیماً Trading یا Settlement را اجرا کند.

تمام عملیات باید از OMS عبور کند.

---

# مسئولیت‌ها

- ایجاد سفارش
- اعتبارسنجی سفارش
- کنترل وضعیت سفارش
- کنترل موجودی
- کنترل اعتبار
- کنترل قوانین کسب‌وکار
- ارسال به Trading
- ارسال به Settlement
- ثبت Audit
- هماهنگی با Kimia

---

# انواع سفارش

## Buy Order

خرید طلا

---

## Sell Order

فروش طلا

---

## Custody Sell

فروش از امانات

---

## Delivery Order

درخواست تحویل فیزیکی

---

## Deposit Order

شارژ کیف پول

---

## Withdrawal Order

برداشت وجه

---

## Internal Transfer

انتقال داخلی دارایی

---

# چرخه سفارش

Draft

↓

Submitted

↓

Validating

↓

Waiting Payment

↓

Paid

↓

Waiting Inventory

↓

Reserved

↓

Executing

↓

Executed

↓

Settlement

↓

Completed

یا

Rejected

یا

Cancelled

یا

Expired

---

# قوانین اعتبارسنجی

قبل از اجرای سفارش بررسی می‌شود:

- وضعیت کاربر
- احراز هویت
- سقف معاملات
- موجودی Wallet
- اعتبار مشتری
- موجودی انبار
- محدودیت‌های قانونی
- قیمت لحظه‌ای
- زمان اعتبار قیمت

---

# ارتباط با Wallet

OMS فقط موجودی را بررسی می‌کند.

جابجایی دارایی توسط Wallet انجام می‌شود.

---

# ارتباط با Trading

پس از تأیید سفارش

OMS

↓

Trading

---

# ارتباط با Settlement

پس از پایان معامله

Trading

↓

OMS

↓

Settlement

---

# ارتباط با Kimia

ثبت در Kimia فقط زمانی انجام می‌شود که سفارش Completed شود.

اگر سفارش لغو یا رد شود، هیچ سندی در Kimia ثبت نمی‌شود.

---

# مدیریت خطا

در صورت بروز خطا:

- Rollback عملیات
- آزادسازی موجودی رزرو شده
- ثبت Audit
- ثبت Log
- اطلاع‌رسانی به کاربر
- امکان Retry

---

# Audit

برای هر تغییر وضعیت ثبت می‌شود:

- User
- Action
- Previous Status
- New Status
- IP
- Device
- Timestamp

---

# Notification

در مراحل زیر پیام ارسال می‌شود:

- ثبت سفارش
- پرداخت موفق
- پرداخت ناموفق
- اجرای معامله
- تکمیل معامله
- آماده تحویل
- انتقال به امانات
- لغو سفارش

---

# API

POST /orders

GET /orders

GET /orders/{id}

POST /orders/validate

POST /orders/pay

POST /orders/execute

POST /orders/cancel

POST /orders/retry

---

# توسعه آینده

- Matching Engine
- Order Queue
- Priority Orders
- Market Orders
- Limit Orders
- Stop Orders
- Scheduled Orders
- Algorithmic Trading
- Multi Branch OMS