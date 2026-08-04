# OP-02 — Session Bootstrap & Permission-aware Navigation

## هدف

اتصال Bootstrap پنل Admin و Operator به نشست واقعی کاربر و Permissionهای مؤثر Spatie، بدون انتقال Authorization به Frontend و بدون تغییر منطق مالی.

## تغییرات

- `AdminBootstrapController` و `OperatorBootstrapController` از کاربر احراز‌شده استفاده می‌کنند.
- `BackofficeSessionBootstrap` نقش‌ها و Permissionهای واقعی کاربر را استخراج می‌کند.
- Navigation فقط برای Permissionهای مؤثر همان نشست برگردانده می‌شود.
- موبایل کاربر Mask می‌شود و کد ملی، ایمیل، شناسه Kimia و اطلاعات مالی منتشر نمی‌شوند.
- OpenAPI Backoffice به نسخه `0.2.0-op-02` به‌روزرسانی شد.

## قرارداد پاسخ

```json
{
  "data": {
    "panel": "admin",
    "session": {
      "authenticated": true,
      "user": {
        "display_name": "مدیر سامانه",
        "mobile_masked": "0912***4567",
        "is_active": true,
        "last_login_at": null
      },
      "roles": ["admin"],
      "permissions": ["audit-logs.view"]
    },
    "navigation": [
      {
        "code": "audit_logs",
        "path": "/admin/audit-logs",
        "permission": "audit-logs.view"
      }
    ],
    "capabilities": ["audit-logs.view"]
  },
  "meta": {
    "request_id": "uuid",
    "generated_at": "2026-08-05T00:00:00Z",
    "api_version": "v1"
  },
  "message": null
}
```

## مرز امنیتی

- Navigation سمت Frontend فقط برای تجربه کاربری است و جایگزین Middleware و Policy سمت Backend نیست.
- Permission جدیدی در این مرحله Seed یا اختراع نشده است.
- مقدار کامل موبایل، کد ملی، Credential، Token و اطلاعات Kimia در پاسخ قرار نمی‌گیرد.
- هیچ Wallet، Ledger، Settlement یا Kimia Write تغییر نکرده است.

## تست‌ها

- `BackofficeSessionBootstrapContractTest`
  - نشست واقعی Admin و Permissionها
  - Mask موبایل
  - فیلتر Navigation براساس Permission
  - جلوگیری از دسترسی ناشناس
- `AdminOperatorFoundationTest`
  - حفظ جداسازی Routeها
  - عدم افشای اصطلاحات داخلی Kimia و اطلاعات حساس

## محدودیت فعلی

آیتم‌های Navigation فقط برای Permissionهایی ظاهر می‌شوند که واقعاً به Role یا User تخصیص یافته‌اند. مدیریت تخصیص Role و Permission خارج از محدوده OP-02 است.
