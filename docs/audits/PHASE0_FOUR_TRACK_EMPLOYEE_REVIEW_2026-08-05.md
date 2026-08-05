# Phase 0 — Four-Track Post-RC2 Employee Review

Date: 2026-08-05

## ثابت مبنا

- آخرین Baseline سالم و اثبات‌شده: Stage 22 / RC2
- RC2 merge commit: `cada4441184e59d09f5ddac567d7b9b8d19b34ae`
- هیچ Branch بعد از RC2 مستقیم Merge نمی‌شود.
- هر مسیر مانند خروجی یک کارمند مستقل ارزیابی می‌شود.

## Track A — Customer Platform

### آخرین Closure

- Final Regression PR: #126
- Final Regression head: `1f767b39de28381de79a71b287f5e4a14cf94fc1`
- Final Regression merge: `a0c6baea327945371251e39e1e8fd89273e4ec2e`
- Phase Closure PR: #132
- Closure head: `c6d1efcf0e99c78ad240586a08e3ee0fcf66278f`
- Closure merge: `5da4da919b0fbd277e3cb1f3cf92c27b93b3868c`

### وضعیت اولیه

`KEEP WITH CONTRACT REVIEW`

Customer تنها مسیر بعد از RC2 است که Final Regression مستقل و Closure ادغام‌شده دارد. بااین‌حال شماره‌های تکراری CP-17/CP-18 و Drift معنایی CP-08 باید بررسی شوند. Closure به معنی تأیید همه جزئیات معنایی نیست.

## Track B — Business Engine / Kimia

### مسیرهای اصلی

- Stage 00 PR #88 — merged
- Stage 01 PR #89 — merged
- Stage 02 PR #92 — merged to `main`
- Stage 02 head: `69e018c3ad9fdc88968def0ffacf0a069c218fdc`
- Stage 02 merge: `31d55fac545201c7b436e940e48e9dcd89bd553d`
- Stage 03 PR #109 — open/draft
- Stage 03 head: `f77ba03ca27169d500a02c424cff8fa011e53119`
- Stage 03: 53 commits, 51 changed files

### وضعیت اولیه

`STAGE 00-02: KEEP WITH ARCHITECTURE REVIEW`

`STAGE 03: DONOR ONLY — FULL SLICE REVIEW REQUIRED`

Stage 03 مستقل قابل انتقال نیست. Financial projection، journal و idempotency فقط زیرساخت عملیاتی‌اند و نباید منبع مانده نهایی Money/Gold/Coin/Currency شوند.

## Track C — Admin / Operator / Frontend

این Track دو زنجیره رقیب ساخته است.

### AP chain

- AP-01 تا AP-20
- AP-20 PR #137
- AP-20 head: `d70b5558d652ea08c33da8fcfecb644c2138dea5`
- وضعیت: open/draft
- Dependency install، Nuxt build و typecheck در گزارش خود PR اجرا نشده‌اند.

### OP chain

- OP-01 merged
- OP-02 تا OP-05 stacked/draft
- OP-05 PR #144
- OP-05 head: `37935f54d357341aba87808146e77d56d5df2d8a`
- وضعیت: open/draft
- Backend و Frontend tests نوشته شده‌اند ولی اجرا نشده‌اند.

### وضعیت اولیه

`AP + OP: DONOR ONLY — TWO COMPETING CHAINS`

هیچ AP یا OP مستقیم Merge نمی‌شود. Permission catalog، route contract، dashboard/queue duplication و frontend directory conflict باید قبل از هر انتقال حل شوند.

## Track D — Infrastructure / Release

### Stage 23

- PR #98
- head: `8b684a5de3e1cf9b089dfeee83f69c00c4f3131e`
- base: RC2 merge commit
- status: open

### Stage 24

- PR #101
- head: `edf03bd03d8af1ae7e4f87075b8770c2759a4c94`
- status: open

### وضعیت اولیه

`DONOR — CI AND OPERATIONAL REVIEW REQUIRED`

این مسیر از نظر دامنه کم‌خطرتر است، اما Worker/Scheduler/Restart/Observability باید روی Base نهایی و SHA دقیق دوباره اجرا شوند.

## تصمیم اجرایی فعلی

1. PR #149 و بازسازی تک‌مسیره Customer در حالت Draft/Frozen باقی می‌ماند.
2. ابتدا CI و Diff نهایی هر چهار Track بررسی می‌شود.
3. برای هر Track یک Verdict نهایی صادر می‌شود: KEEP / KEEP WITH FIX / REBUILD / DONOR ONLY / REJECT.
4. فقط پس از پایان این چهار Verdict، Branch محصولی Canonical از RC2 ساخته یا ادامه داده می‌شود.
5. هیچ خروجی تاریخی حذف نمی‌شود.

## گام بعدی

- Customer: بررسی CI Final Regression و Semantic Contractهای CP-01 تا CP-18
- Business: Dependency graph کامل Stage 03 و مقایسه با Stage 02 merge
- Admin/Operator: Permission/Route/Frontend conflict matrix برای AP و OP
- Infrastructure: CI و file-level review Stage 23 و 24
