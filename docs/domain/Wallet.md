# Wallet Domain

## هدف

Wallet هسته مالی سامانه جامع معاملات و مدیریت دارایی طلا است.

هر مشتری دقیقاً یک Wallet دارد که شامل چندین حساب (Wallet Account) است.

Wallet صرفاً یک کیف پول نیست؛ بلکه یک موتور حسابداری (Accounting Engine) برای نگهداری تمام دارایی‌ها، بدهی‌ها، اعتبارات و امانات مشتری است.

---

# اهداف طراحی

- Single Source of Truth
- Double Entry Accounting
- Audit کامل
- قابلیت اتصال به Kimia
- قابلیت توسعه برای معاملات آینده

---

# ساختار

User

↓

Wallet

↓

Wallet Accounts

↓

Ledger

↓

Transactions

---

# Wallet

هر کاربر فقط یک Wallet دارد.

Wallet فقط Container است و موجودی داخل آن نگهداری نمی‌شود.

---

# Wallet Accounts

هر Wallet می‌تواند شامل Accountهای زیر باشد.

## Cash

موجودی ریالی

---

## Gold 18K

موجودی طلای ۱۸ عیار

---

## Gold 24K

در صورت نیاز

---

## Custody

امانات مشتری

---

## Credit

اعتبار اعطا شده

---

## Settlement

حساب تسویه

---

## Frozen

موجودی مسدود شده

---

## Pending

تراکنش‌های در انتظار

---

# Wallet Account Fields

- id
- wallet_id
- code
- title
- account_type
- asset_type
- currency
- available_balance
- blocked_balance
- pending_balance
- total_balance
- status

---

# انواع دارایی

- Cash
- Gold18
- Gold24
- Coin
- Bullion
- Custody
- Credit

---

# قوانین

هر Account فقط یک نوع دارایی نگهداری می‌کند.

هیچ Account چند ارزی وجود ندارد.

---

# Ledger

هیچ موجودی داخل Ledger ذخیره نمی‌شود.

Ledger فقط عملیات را ثبت می‌کند.

مثال

+5

-2

+3

---

# موجودی

Balance = Sum(Ledger)

---

# Transaction Rules

هر Transaction باید حداقل دو Entry داشته باشد.

Debit

Credit

---

# نمونه خرید

Cash

↓

Gold

---

# نمونه فروش

Gold

↓

Cash

---

# نمونه انتقال

User A

↓

User B

---

# نمونه امانات

Custody

↓

Settlement

---

# ارتباط با Kimia

Wallet اطلاعات مالی داخلی سیستم است.

Kimia فقط اسناد مالی و انبارداری را دریافت می‌کند.

Ledger مستقل از Kimia طراحی می‌شود.

---

# API

GET Wallet

GET Accounts

GET Balance

GET Transactions

POST Transfer

POST Deposit

POST Withdraw

---

# توسعه آینده

- Multi Currency
- Multi Gold Standard
- Margin Trading
- Futures
- Options
- Bank Accounts
- Crypto Assets