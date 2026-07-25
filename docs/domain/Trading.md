# Trading Domain

# هدف

Trading Engine مسئول مدیریت کامل معاملات خرید و فروش طلا در سامانه است.

این ماژول صرفاً خرید و فروش انجام نمی‌دهد؛ بلکه کل چرخه معامله را از ثبت سفارش تا تسویه و تحویل مدیریت می‌کند.

---

# اهداف

- ثبت سفارش
- مدیریت سفارش
- اجرای معامله
- کنترل موجودی
- کنترل اعتبار
- رزرو دارایی
- ثبت Ledger
- ارتباط با OMS
- ارتباط با Settlement
- ارتباط با Kimia

---

# انواع معامله

## خرید

کاربر ریال پرداخت می‌کند.

طلای ۱۸ دریافت می‌کند.

---

## فروش

کاربر طلا می‌فروشد.

ریال دریافت می‌کند.

---

## خرید اعتباری

کاربر با اعتبار خرید انجام می‌دهد.

---

## فروش امانات

از موجودی Custody استفاده می‌شود.

---

## تبدیل دارایی

نمونه

۱۸ عیار

↓

شمش

↓

سکه

↓

پارسیان

---

# وضعیت سفارش

Draft

↓

Pending

↓

Waiting Payment

↓

Paid

↓

Reserved

↓

Executed

↓

Settlement

↓

Completed

یا

Cancelled

یا

Rejected

---

# قوانین

قبل از اجرای معامله موارد زیر بررسی می‌شود.

- موجودی ریالی
- موجودی طلایی
- اعتبار
- موجودی انبار
- محدودیت معاملات
- وضعیت کاربر

---

# رزرو موجودی

قبل از اجرای معامله

دارایی رزرو می‌شود.

Available

↓

Blocked

---

# اجرای معامله

پس از تایید

Blocked

↓

Executed

---

# Ledger

تمام معاملات باید در Ledger ثبت شوند.

هیچ تغییری مستقیم روی Balance انجام نمی‌شود.

---

# ارتباط با Wallet

Trading فقط از Wallet استفاده می‌کند.

هیچ موجودی مستقلاً نگهداری نمی‌شود.

---

# ارتباط با OMS

OMS مالک سفارش است.

Trading فقط اجراکننده سفارش است.

---

# ارتباط با Settlement

پس از اجرای معامله

Settlement آغاز می‌شود.

---

# ارتباط با Kimia

بعد از Final شدن معامله

سند فروش یا خرید در Kimia ثبت می‌شود.

---

# API

POST /orders

GET /orders

GET /orders/{id}

POST /orders/cancel

POST /orders/pay

POST /orders/execute

POST /orders/confirm

---

# Future

Market Order

Limit Order

Stop Order

Scheduled Order

Recurring Order

Algorithmic Order