# Customer Panel & API Contract — Phase Closure

## وضعیت

این سند برای بستن رسمی زنجیره CP-01 تا CP-18 ایجاد شده است.

وضعیت نهایی این فاز فقط پس از تحقق هم‌زمان موارد زیر «بسته‌شده» اعلام می‌شود:

- Merge شدن Final Regression روی Branch اصلی توسعه
- سبزشدن هر شش Gate استاندارد CI روی SHA مستقل Final Regression
- اجرای Health Check نهایی
- همگام‌سازی مستندات وضعیت پروژه و CHANGELOG

## خروجی‌های تثبیت‌شده

- Customer API نسخه‌دار
- قراردادهای Dashboard، Assets، Orders، Custody، Delivery، Profile، Activity و Bootstrap
- Error Contract استاندارد و امن
- Pagination با پیش‌فرض `per_page=25`
- فیلتر وضعیت مبتنی بر Enum واقعی Backend
- مرتب‌سازی محدود `newest|oldest`
- فیلتر تاریخ `from` و `to` با فرمت استاندارد
- Header رهگیری `X-Request-ID`
- سیاست `private, no-store` برای پاسخ‌های حساس مشتری
- OpenAPI 3.1 همگام با Backend
- Contract و Regression Tests

## مرزهای حفظ‌شده

- بدون Migration مالی جدید
- بدون تغییر Wallet، Ledger، Settlement یا State Machine
- بدون Kimia Write
- بدون ساخت Rule مالی، فرمول قیمت یا کارمزد جدید
- بدون افشای AccountId، ProductId، Voucher و اصطلاحات داخلی Kimia به Frontend

## چک‌لیست نهایی

- [ ] Final Regression CI: 6/6 PASS
- [ ] Final Regression PR merged
- [ ] Phase Closure CI: 6/6 PASS
- [ ] `docs/project_state.md` updated
- [ ] `docs/CHANGELOG.md` updated
- [ ] Project Memory updated only with confirmed facts
- [ ] Remaining risks and next phase recorded

## ریسک‌های باقی‌مانده خارج از محدوده این فاز

- عملیات واقعی Kimia Write و Payload نهایی آن
- قواعد مالی، تسویه، محدودیت اعتباری و ضدنوسان‌گیری
- پیاده‌سازی رابط بصری نهایی Frontend
- پنل Admin و Operator

این موارد با تکمیل Customer API Contract به‌طور خودکار تأیید یا پیاده‌سازی‌شده محسوب نمی‌شوند.
