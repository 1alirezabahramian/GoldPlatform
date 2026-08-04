# GoldPlatform — Project State

- Updated: 2026-08-04
- Base branch: `feature/goldplatform-developer-mcp`
- Current delivery PR: #67
- Current release state: **Backend RC1**

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

## Current validation

GitHub Actions run `30876475856` passed:

- Composer validation
- Migration fresh on MySQL 8.4
- Unit: 33 tests / 165 assertions
- Feature: 35 tests / 156 assertions
- Financial and Ledger: 16 tests / 49 assertions
- Order lifecycle: 10 tests / 29 assertions
- Trade idempotency and Settlement: 3 tests / 16 assertions
- Custody and Delivery: 3 tests / 10 assertions
- Permission: 6 tests / 25 assertions
- Kimia mock: 15 tests / 30 assertions
- Kimia read-only integration contract: 1 test / 4 assertions
- **Full regression: 68 tests / 321 assertions**
- Laravel environment, route and migration health
- 22 registered application routes
- MySQL and Redis service health
- Docker Compose configuration validation
- Gitleaks secret scan over complete tracked Git history

Detailed evidence: `docs/test-reports/2026-08-04-backend-rc1-final-gate.md`.

## Project Library

The consolidated project history, capabilities, architecture rules and confirmed boundaries from inception through Backend RC1 are recorded in:

- `docs/LIBRARY.md`
- `docs/00_PROJECT_MEMORY.md`
- `docs/adr/`
- `docs/test-reports/`

`docs/LIBRARY.md` is the central navigation and summary document. It does not replace the detailed ADRs or domain documentation.

## Current API boundaries

- Customer APIs expose balances, orders, custody and delivery using simple product language.
- Operator APIs expose operational queues and delivery transitions.
- Admin APIs expose audit records, outbox records and customer trading policies.
- Sanctum authentication and explicit roles protect all panel contracts.
- Supported mutating operations require an `Idempotency-Key`.
- Every API response receives an `X-Request-Id` correlation identifier.

## Confirmed RC1 boundaries

- CI Kimia read-only validation is an HTTP Fake integration contract; Production Kimia connectivity and credentials were not exercised.
- Kimia write operations remain disabled until real API payloads, actions and account mappings are confirmed.
- Local production-like Docker containers on the operator computer have not yet been revalidated after syncing this RC1 branch.
- Outbox persistence is complete; an asynchronous publisher/dispatcher remains a later infrastructure step.
- SMS.ir and Jibit Production integrations remain environment-dependent.
- Production backup/restore, observability, alerting, rate limiting, load testing and independent penetration testing remain outside Backend RC1.

## Next stage

Production-readiness preparation:

1. secure real Kimia read-only verification;
2. production-like deployment rehearsal;
3. outbox worker and operational observability;
4. external provider validation;
5. frontend/API contract synchronization;
6. performance and security hardening.

Backend RC1 is complete, but the product must not yet be described as Production Complete.
