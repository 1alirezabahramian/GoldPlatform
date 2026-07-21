# GoldPlatform SMS Templates

Version: 1.0

---

# Purpose

This document defines all SMS.ir Verify Templates used by GoldPlatform.

Every SMS template must be approved by SMS.ir before being used in production.

No template text should be hardcoded inside the source code.

---

# Provider

SMS.ir

Verify Service

---

# Template 1

Name

LoginOTP

Purpose

User Login

Variables

Code

Message

کد ورود شما به GoldPlatform:

{{Code}}

این کد تا ۲ دقیقه معتبر است.

طلا و سکه خلیفه

Status

Pending

Template ID

-

---

# Template 2

Name

RegisterOTP

Purpose

Registration

Variables

Code

Message

کد ثبت‌نام شما:

{{Code}}

به GoldPlatform خوش آمدید.

Status

Pending

Template ID

-

---

# Template 3

Name

PasswordRecovery

Purpose

Recover Account

Variables

Code

Message

کد بازیابی حساب:

{{Code}}

این کد را در اختیار دیگران قرار ندهید.

Status

Pending

Template ID

-

---

# Template 4

Name

VerifyMobile

Purpose

Verify Mobile Number

Variables

Code

Message

کد تأیید شماره موبایل:

{{Code}}

Status

Pending

Template ID

-

---

# Template 5

Name

OrderCreated

Purpose

Order Created

Variables

Order

Message

سفارش شما با موفقیت ثبت شد.

شماره سفارش:

{{Order}}

Status

Pending

Template ID

-

---

# Template 6

Name

SellCreated

Purpose

Sell Request

Variables

Tracking

Message

درخواست فروش ثبت شد.

کد رهگیری:

{{Tracking}}

Status

Pending

Template ID

-

---

# Template 7

Name

WalletDeposit

Purpose

Wallet Deposit

Variables

Amount

Message

کیف پول شما به مبلغ

{{Amount}}

شارژ شد.

Status

Pending

Template ID

-

---

# Template 8

Name

WalletWithdraw

Purpose

Wallet Withdraw

Variables

Amount

Message

برداشت از کیف پول انجام شد.

مبلغ:

{{Amount}}

Status

Pending

Template ID

-

---

# Template 9

Name

IdentityVerified

Purpose

Identity Verification

Variables

Name

Message

{{Name}}

احراز هویت شما با موفقیت انجام شد.

Status

Pending

Template ID

-

---

# Template 10

Name

GeneralNotification

Purpose

General Notification

Variables

Message

Message

{{Message}}

Status

Pending

Template ID

-

---

# Production Notes

After each template is approved by SMS.ir:

- Update Template ID
- Change Status to Active
- Register the ID inside config/services.php
- Never hardcode Template IDs in controllers
- Access templates only through SmsService