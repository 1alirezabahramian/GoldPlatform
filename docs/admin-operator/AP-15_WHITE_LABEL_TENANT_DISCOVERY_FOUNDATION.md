# AP-15 — White-label & Tenant Discovery Foundation

## هدف
ایجاد نمای فقط‌خواندنی و صادقانه از وضعیت White-label و Tenant در مخزن فعلی، بدون ساخت Multi-tenancy فرضی یا تغییر معماری اصلی.

## Endpoint
- `GET /api/v1/admin/white-label/overview`

## Permission
- `white-label.view`

## Ground Truth
در شاخه بررسی‌شده هیچ مدل، جدول یا قرارداد اجرایی تأییدشده‌ای برای موارد زیر پیدا نشد:
- Tenant
- Company
- Branding Profile
- Custom Domain
- Theme Tokens
- Logo Management
- Branch-to-Tenant Relation
- Tenant User Assignment
- Tenant-specific Kimia Configuration

## رفتار API
API فقط نام و Locale فعلی برنامه را از Configuration استاندارد Laravel و وضعیت قابلیت‌ها را به‌صورت صریح نمایش می‌دهد. همه قابلیت‌های ناموجود با `supported=false` گزارش می‌شوند.

## مرزهای ایمنی
- بدون Migration
- بدون Tenant یا Company Model
- بدون Domain Routing
- بدون تغییر Theme، Logo یا Brand
- بدون Tenant Scope روی داده‌های مالی
- بدون Tenant-specific Kimia credentials
- بدون افشای URL، Credential یا Secret

## تصمیم معماری موردنیاز
پیاده‌سازی واقعی White-label نیازمند ADR مستقل برای Tenant isolation، مالکیت داده، دامنه، شعب، کاربران، Kimia و مدل استقرار است. این مرحله هیچ‌یک از این تصمیم‌ها را حدس نمی‌زند.

## وضعیت تست
Feature Test نوشته شده است؛ اجرای CI و PASS نهایی هنوز تأیید نشده است.
