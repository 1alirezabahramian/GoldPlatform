# GoldPlatform Local Agent Runner

این ابزار روی کامپیوتر ویندوزی فروشگاه، تست‌های امن پروژه را اجرا و گزارش زمان‌دار ذخیره می‌کند.

## قابلیت‌های نسخه اول

- بررسی Git و Docker
- روشن‌کردن سرویس‌های Docker Compose
- اجرای `artisan about` و `migrate:status`
- اجرای کامل تست‌های Laravel
- تست اتصال Kimia
- خواندن محدود تراکنش حساب 350 به‌صورت read-only
- ذخیره گزارش در `storage/agent-reports`
- اجرای خودکار در Startup ویندوز و هر چند ساعت

## اجرای دستی

PowerShell 7 را در ریشه پروژه باز کنید:

```powershell
pwsh -NoProfile -ExecutionPolicy Bypass -File .\tools\local-agent\Invoke-GoldPlatformHealthCheck.ps1 -OpenReport
```

همگام‌سازی‌های محلی Kimia را فقط در صورت نیاز اضافه کنید:

```powershell
pwsh -NoProfile -ExecutionPolicy Bypass -File .\tools\local-agent\Invoke-GoldPlatformHealthCheck.ps1 -IncludeKimiaSync -OpenReport
```

## نصب اجرای خودکار

PowerShell 7 را با **Run as administrator** باز کنید:

```powershell
pwsh -NoProfile -ExecutionPolicy Bypass -File .\tools\local-agent\Install-GoldPlatformAgentTask.ps1 -EveryHours 2
```

اجرای فوری Task:

```powershell
Start-ScheduledTask -TaskName 'GoldPlatform Local Health Check'
```

مشاهده آخرین گزارش‌ها:

```powershell
Get-ChildItem .\storage\agent-reports\*.log |
    Sort-Object LastWriteTime -Descending |
    Select-Object -First 5 Name, LastWriteTime, Length
```

## سیاست امنیتی

این نسخه عمداً فرمان‌های زیر را اجرا نمی‌کند:

- `migrate:fresh` یا `db:wipe`
- `docker compose down -v`
- حذف Volume
- `git reset --hard` یا Push اجباری
- ثبت یا تغییر Voucher در Kimia
- نمایش Secretهای `.env`

## محدودیت مهم

این نسخه خودکار تست می‌گیرد، اما هنوز از خانه فرمان مستقیم ChatGPT را دریافت نمی‌کند. مرحله بعد یک کانال کنترل احرازشده و محدود خواهد بود که فقط فرمان‌های Allowlist شده را به Runner منتقل می‌کند. هیچ پورت عمومی روی اینترنت باز نخواهد شد.
