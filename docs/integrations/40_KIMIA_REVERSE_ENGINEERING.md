# 40 - Kimia Reverse Engineering
Version: 0.1
Status: In Progress

---

# هدف

مستندسازی کامل ساختار داخلی نرم‌افزار کیمیا برای اتصال پایدار
سامانه جامع معاملات و مدیریت دارایی طلا (Gold Trading & Asset Management Platform).

هدف نهایی:

- حذف وابستگی به روش‌های دستی
- ارتباط کامل با Kimia
- امکان ثبت خودکار تمام عملیات سایت در کیمیا
- کشف ساختار دیتابیس و API های داخلی

---

# اطلاعات کلی

Software:

Kimia

Executable:

Kimia.exe

Service:

KimiService.exe

Framework:

.NET

وجود فایل‌های زیر نشان می‌دهد برنامه بر پایه .NET توسعه یافته است.

- microsoft.runtime.dll
- microsoft.runtime.dotll

---

# ساختار پوشه

C:\Kimia

```
backups/
libs/
logs/
plugins/
resources/
runtimes/

environment.config
Kimia.exe
Kimia.exe.config
Kimia.exe.info

KimiService.exe
KimiService.exe.config

register.bat
```

---

# فایل‌های مهم

## environment.config

دارای دو مقدار رمز شده:

ApiSecurityToken

DataSource

نمونه:

```json
{
  "ApiSecurityToken": "...",
  "DataSource": "..."
}
```

احتمال زیاد:

DataSource

رشته اتصال دیتابیس رمز شده است.

---

## Kimia.exe.config

باید بررسی شود.

احتمال وجود:

- تنظیمات دیتابیس
- Service Endpoint
- Logging
- Plugin Loading

---

## KimiService.exe

احتمالاً سرویس ویندوز.

وظایف احتمالی:

- همگام سازی
- ارتباط API
- مدیریت قفل سخت افزاری
- بروزرسانی

---

# سیستم Backup

پسوند:

```
.kbp
```

مشاهده شد:

تغییر نام به zip امکان‌پذیر است.

داخل فایل:

```
config.json
data/
```

اما پوشه data دارای رمز می‌باشد.

نکات:

- هنگام Restore داخل Kimia پسورد درخواست نمی‌شود.
- بنابراین برنامه رمز را داخلی تولید یا استخراج می‌کند.

---

# فرضیه Backup

احتمالاً روند:

Backup

↓

Zip

↓

AES Encryption

↓

Restore توسط کلید موجود در برنامه

---

# قفل سخت افزاری

کاربر اعلام کرده:

Kimia از Dongle استفاده می‌کند.

نتیجه:

برخی قابلیت‌ها بدون دانگل فعال نمی‌شوند.

---

# ابر آروان

نسخه اصلی Kimia روی سرور ابرآروان نصب شده است.

مزیت:

امکان بررسی مستقیم

- دیتابیس
- فایل‌ها
- سرویس‌ها

در آینده وجود دارد.

---

# اهداف Reverse Engineering

## مرحله 1

بررسی فایل‌های Config

Status:

Pending

---

## مرحله 2

شناسایی دیتابیس

Status:

Pending

---

## مرحله 3

شناسایی Connection String

Status:

Pending

---

## مرحله 4

بررسی DLL ها

Status:

Pending

---

## مرحله 5

شناسایی Plugin ها

Status:

Pending

---

## مرحله 6

بررسی API داخلی

Status:

Pending

---

## مرحله 7

شناسایی ساختار جداول

Status:

Pending

---

## مرحله 8

مستندسازی Business Logic

Status:

Pending

---

# موارد کشف شده

✔ وجود environment.config

✔ وجود Service

✔ ساختار Backup

✔ استفاده از فایل config

✔ احتمال استفاده از SQL Server

---

# موارد نامشخص

- الگوریتم رمز Backup

- محل ذخیره دیتابیس

- ساختار جداول

- API داخلی

- ارتباط Service با برنامه

- Plugin Architecture

---

# برنامه ادامه کار

پس از دسترسی به سرور:

1. بررسی سرویس‌ها

2. بررسی SQL Server

3. استخراج Connection String

4. بررسی DLL ها

5. تحلیل Database Schema

6. طراحی Adapter برای اتصال Gold Platform به Kimia

---

# ارتباط با Gold Platform

هدف نهایی ایجاد لایه‌ای با نام:

Kimia Adapter

که وظیفه آن:

- ارسال اسناد مالی
- دریافت موجودی
- دریافت مشتریان
- دریافت حساب‌ها
- ثبت تراکنش‌ها
- همگام سازی اطلاعات

بدون وابستگی Frontend به ساختار داخلی Kimia خواهد بود.