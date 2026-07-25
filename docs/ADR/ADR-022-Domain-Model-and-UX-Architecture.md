# ADR-021
# GoldPlatform Domain Model & UX Architecture

Status: Accepted

Date: 2026-07-23

---

# Context

در بررسی معماری پروژه مشخص شد که GoldPlatform صرفاً یک فروشگاه آنلاین طلا نیست.

هدف پروژه ایجاد یک سامانه جامع مدیریت دارایی و معاملات طلا است که بتواند علاوه بر فروش فیزیکی، معاملات اعتباری، کیف پول‌های چندگانه، امانات، ارتباط کامل با نرم‌افزار حسابداری کیمیا و توسعه‌های آینده را مدیریت کند.

بنابراین معماری سیستم باید از ابتدا بر اساس مدل واقعی کسب‌وکار طراحی شود، نه صرفاً فروشگاه اینترنتی.

---

# Project Definition

GoldPlatform is a comprehensive Gold Trading Platform / Order Management System (OMS).

The platform supports:

- Physical Gold Sales
- Digital Gold Trading
- Monetary Wallet
- Gold Wallet
- Coin Wallet
- Currency Wallet
- Custody (Safe Deposit)
- Credit Trading
- Deferred Settlement
- Kimia ERP Integration
- Customer Groups
- Permission Based Trading
- Delivery Management

---

# Customer Asset Types

بر اساس نرم‌افزار کیمیا چهار نوع مانده اصلی وجود دارد.

## A. Monetary Balance

مانده پولی مشتری

- Rial
- Toman

---

## B. Gold Balance

مانده طلای ۱۸ عیار

این همان طلای معامله‌ای یا Paper Gold است.

امکان خرید و فروش مکرر بدون تحویل فیزیکی وجود دارد.

---

## C. Coin Balance

مانده انواع سکه

این بخش کاملاً Dynamic است.

هر فروشگاه می‌تواند انواع دلخواه سکه تعریف کند.

نمونه‌ها:

- امامی
- بهار آزادی
- نیم
- ربع
- گرمی
- پهلوی
- سایر سکه‌ها

بنابراین سیستم نباید به چند نوع سکه محدود شود.

---

## D. Currency Balance

مانده ارز

نمونه‌ها:

- USD
- EUR
- AED
- GBP

و هر ارز دیگری که در کیمیا تعریف شود.

---

# Custody Assets

برخی کالاها در چهار مانده فوق قرار نمی‌گیرند.

نمونه‌ها:

- پلاک پارسیان
- شمش ۲۴ عیار
- آبشده استاندارد
- طلای ساخته
- انگشتر
- گوشواره
- نیم‌ست
- سرویس

این کالاها قابلیت معامله اعتباری ندارند.

تنها خرید فیزیکی انجام می‌شود.

پس از خرید، کالا تا زمان تحویل در بخش Custody نگهداری می‌شود.

---

# Custody Architecture

برای نگهداری کالاهای فیزیکی باید حساب جدیدی مستقل از چهار مانده اصلی ایجاد شود.

نام پیشنهادی:

Custody Assets

یا

Customer Custody

این بخش موجودی تحویلی مشتری را نگهداری می‌کند.

---

# Gold Conversion

در بازار طلا اصطلاحات زیر رایج هستند.

طلایی کردن

یعنی:

تبدیل مانده پولی به طلای اعتباری

پولی کردن

یعنی:

فروش طلای اعتباری و تبدیل آن به مانده پولی

در کیمیا این عملیات با Transaction Code های مخصوص ثبت می‌شود.

اما این کدها نباید در رابط کاربری نمایش داده شوند.

---

# Credit Trading

برخی مشتریان ویژه اجازه معامله بدون موجودی خواهند داشت.

نمونه‌ها:

- خرید بدون موجودی
- فروش بدون موجودی
- تسویه بعدی

این قابلیت بر اساس Customer Group فعال می‌شود.

---

# Product Categories

محصولات قابل معامله:

- Gold
- Coin
- Currency

محصولات قابل نگهداری امانی:

- Parsian Plate
- Bullion
- Standard Melted Gold
- Jewelry

---

# Existing Prototype

نمونه اولیه HTML طراحی‌شده دارای ویژگی‌های زیر است:

- اتصال مستقیم به API قیمت
- پرداخت از صندوق پولی
- پرداخت از صندوق طلایی
- سبد خرید
- صدور فاکتور
- دسته‌بندی محصولات
- آماده برای اتصال Backend

این Prototype به عنوان Reference UI نگهداری خواهد شد و مستقیماً وارد پروژه Laravel نخواهد شد.

ابتدا بر اساس Design System پروژه بازطراحی می‌شود.

---

# UX Decision

یکی از مهم‌ترین تصمیمات پروژه:

پیچیدگی سیستم نباید به کاربر منتقل شود.

Backend می‌تواند بسیار پیچیده باشد.

اما Frontend باید بسیار ساده باشد.

---

# Two Layer Architecture

## Business Layer

این لایه مطابق ساختار واقعی کیمیا طراحی می‌شود.

نمونه‌ها:

- Wallet
- Ledger
- Balance
- Settlement
- Custody
- Accounts
- Transactions

---

## Experience Layer

کاربر فقط مفاهیم ساده را مشاهده می‌کند.

نمونه‌ها:

- دارایی من
- پول نقد
- طلای من
- سکه‌ها
- ارزها
- آماده تحویل
- خرید
- فروش
- تبدیل به طلا
- تبدیل به پول

کاربر هرگز اصطلاحات تخصصی حسابداری را مشاهده نخواهد کرد.

---

# UX Principle

اصل شماره ۱ طراحی GoldPlatform

Complex Backend

Simple Frontend

تمام پیچیدگی در Backend مدیریت می‌شود.

رابط کاربری باید برای افراد غیرمتخصص نیز کاملاً قابل فهم باشد.

---

# Long-Term Vision

GoldPlatform باید به گونه‌ای طراحی شود که در آینده بتواند:

- Trading Platform
- OMS
- Custody Platform
- ERP Integration
- Multi Branch
- API Services
- Marketplace

را بدون تغییر اساسی در معماری پشتیبانی کند.

---

# Decision

این معماری از این پس مبنای توسعه تمامی ماژول‌های پروژه خواهد بود.

تمام طراحی‌های Backend و Frontend باید با این سند سازگار باشند.