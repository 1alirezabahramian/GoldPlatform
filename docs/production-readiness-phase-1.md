# GoldPlatform — Production Readiness Phase 1

- Started: 2026-08-04
- Base branch: `feature/goldplatform-developer-mcp`
- Working branch: `work/production-readiness-phase-1`
- Status: In progress

## Goal

تبدیل Backend RC1 به نسخه قابل ارزیابی در محیط نزدیک Production، بدون فعال‌سازی عملیات Write کیمیا و بدون حدس‌زدن قراردادهای مالی یا API.

## Workstreams

1. **Secure Kimia read-only verification**
   - Read-only mode must be enabled by default.
   - Canonical Kimia client must block POST, PUT and DELETE before network dispatch.
   - Real credentials must only exist in an approved secure environment or secret store.
   - Verification output must not expose credentials or sensitive payloads.

2. **Real-response contract comparison**
   - Compare real Account, Account Group, Voucher, Coin and Currency response shapes against the confirmed Swagger and current DTO/mappers.
   - Store only redacted evidence.
   - Stop on any contradiction; do not silently normalize unknown fields.

3. **Production-like Docker rehearsal**
   - Validate PHP, MySQL, Redis, queue and migrations using production-like environment values.
   - Keep destructive migration commands out of connected production databases.

4. **Secure environment configuration**
   - `APP_DEBUG=false`.
   - secrets outside Git.
   - explicit `KIMIA_READ_ONLY=true`.
   - production-safe logging and session/cookie settings.

5. **Monitoring baseline**
   - application health, queue health, failed jobs, Kimia connectivity status and storage/database health.

6. **Backup and restore drill**
   - documented database backup procedure;
   - restore into an isolated database;
   - integrity and migration-status verification;
   - no untested claim of recoverability.

## First implemented safety control

`KIMIA_READ_ONLY` defaults to `true`. The canonical Kimia client blocks all non-GET requests before an HTTP request is sent. A unit test proves that POST, PUT and DELETE do not leave the process while read-only mode is active.

## Acceptance criteria

Phase 1 is complete only when:

- the Backend RC1 regression suite remains green;
- Kimia read-only guard tests pass;
- a secure real read-only verification has been executed and redacted evidence recorded;
- production-like Docker rehearsal passes;
- environment security checklist passes;
- monitoring baseline is operational;
- backup and isolated restore drill pass;
- all results and remaining risks are recorded in `docs/PROJECT_STATE.md` and `docs/test-reports/`.

## Current boundary

The current change starts Phase 1 and implements the first safety control. It does not claim that real Kimia connectivity, production-like deployment, monitoring or backup/restore have already passed.
