# GoldPlatform — Project State

- Updated: 2026-08-04
- Base branch: `feature/goldplatform-developer-mcp`
- Current delivery PR: #68
- Current release state: **Backend RC1 + Stage 13 implementation validated**

## Backend stages

1. Kimia Read Foundation — implemented and covered by canonical-client tests.
2. Ledger Foundation — implemented with exact decimal guards.
3. Order State Machine — implemented and tested.
4. Trading Engine — atomic and idempotent flow tested.
5. Balance Projection / Wallet — Ledger-derived balances tested.
6. Dynamic Coin / Currency Trading — Kimia catalog guards tested.
7. Custody / Amanat — independent physical-asset lifecycle implemented and tested.
8. Delivery — auditable lifecycle, ownership and double-delivery protection implemented and tested.
9. Customer Rules and Limits — data-driven policies per customer group implemented and tested.
10. Customer, Operator and Admin APIs — authenticated, role-separated contracts implemented and tested.
11. Audit, Idempotency and Outbox — request correlation, replay protection, append-only audit persistence and transactional outbox implemented and tested.
12. Backend RC1 Final Gate — complete validation matrix, health checks and Git-history secret scan passed.
13. Kimia Production Readiness — read-only safety guard, validator, retry policy, timeout settings, protected real-validation workflow and compatibility evidence path implemented and CI-validated.

## Current validation

GitHub Actions run `30878059781` passed:

- Composer validation
- Migration fresh on MySQL 8.4
- Unit Tests
- Feature Tests
- Financial and Ledger Tests
- Order lifecycle
- Trade idempotency and Settlement
- Custody and Delivery
- Permission isolation
- Kimia mock tests
- Kimia read-only integration contract
- Full regression suite
- Laravel environment, route and migration health
- MySQL and Redis service health
- Docker Compose configuration validation
- Gitleaks secret scan over complete tracked Git history

Detailed Stage 13 evidence: `docs/test-reports/2026-08-04-stage-13-kimia-production-readiness.md`.

## Stage 13 controls

- `KIMIA_READ_ONLY=true` is the safe default.
- The canonical Kimia client blocks POST, PUT and DELETE before network dispatch while read-only mode is enabled.
- Read requests use configurable timeout, retry count and retry delay.
- The read validator covers confirmed Account, Account Group, Coin, Currency, Barcode and optional Account Balance / Voucher probes.
- Validation output is redacted and contains status, row count, latency and error classification only.
- A protected GitHub Actions workflow exists for real Kimia read-only validation using environment secrets.

## Stage 13 external gate

The implementation and normal CI gate are complete. The following evidence still requires an approved secure environment containing real Kimia credentials:

- execution of `.github/workflows/kimia-readonly-production-validation.yml`;
- capture of the redacted compatibility artifact;
- comparison of real response structures with current DTOs, mappers and confirmed Swagger;
- explicit resolution of any contradiction before changing financial or Kimia behavior.

No Production Kimia credential was placed in Git or CI test configuration. Kimia write remains disabled.

## Project Library

The consolidated project history, capabilities, architecture rules and confirmed boundaries are recorded in:

- `docs/LIBRARY.md`
- `docs/00_PROJECT_MEMORY.md`
- `docs/adr/`
- `docs/test-reports/`

## Confirmed boundaries

- Kimia write operations remain disabled until real API payloads, actions and account mappings are confirmed.
- Local production-like Docker containers on the operator computer have not yet been revalidated after syncing this branch.
- Outbox persistence is complete; an asynchronous publisher/dispatcher remains a later infrastructure step.
- SMS.ir and Jibit Production integrations remain environment-dependent.
- Production backup/restore, observability, alerting, rate limiting, load testing and independent penetration testing remain outside Stage 13.

## Next stage

Stage 14: Kimia Write Preparation — contract design, payload evidence matrix, account/action mapping, idempotency, retry and compensation design. Write activation is not part of Stage 14.

Backend RC1 remains complete, but the product must not yet be described as Production Complete.
