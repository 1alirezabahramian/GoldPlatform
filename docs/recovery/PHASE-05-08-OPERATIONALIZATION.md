# GoldPlatform — Phase 05 to 08 Operationalization

## Status

In Progress

## Canonical baseline

- Base branch: `recovery/rc2-product-rebuild`
- Base SHA: `84cd5d03b427c9d6e3cb58ffa2aaf96f7ce89c4c`
- Frontend recovery phases 1–4: merged and validated

## Non-negotiable authority contract

Kimia remains the final source of truth for Money, Gold, Coin and Currency. GoldPlatform remains the source of truth for physical Custody. Internal Ledger, Journal, Event Store, Idempotency Registry and Balance Projection remain audit/workflow/reconciliation facilities and are not final customer balance authorities.

## Phase 05 — Business workflow closure

Required closure gates:

- Order and Quote lifecycle verified against existing code and accepted contracts.
- Idempotency verified for customer and operator mutations.
- Settlement cannot complete from internal ledger evidence alone.
- Reconciliation records Intent, Result and Kimia evidence without creating a competing balance.
- No Kimia Write is enabled without accepted real payload ground truth.

Current state: existing recovery protections are present; a fresh capability-by-capability closure audit and targeted tests remain required.

## Phase 06 — Security and tenant isolation

Required closure gates:

- Tenant/company/branch isolation tests.
- Permission matrix tests for customer, operator and admin.
- IDOR tests for orders, custody and delivery references.
- Rate-limit and authentication boundary tests.
- White-label configuration isolation without credential leakage.

Current state: role and authentication middleware exist; complete isolation proof is not yet claimed.

## Phase 07 — Runtime integration

Required closure gates:

- Live authentication flow between Frontend and Backend.
- Queue, cache and notification configuration validation.
- Read-only Kimia connectivity health and controlled unavailable states.
- Monitoring, structured logging and request correlation.
- No automatic sensitive write execution through scheduler, queue, event or outbox.

Current state: executable Frontends and Backend contracts exist; environment-specific integration remains.

## Phase 08 — Production and release closure

Required closure gates:

- Production Compose or deployment manifest validation.
- Backup and restore execution evidence.
- Migration and rollback procedure validation.
- Full Backend regression, both Frontend builds and browser E2E on one exact SHA.
- Secret scan and dependency review.
- Release checklist, rollback plan and final Project State alignment.

Current state: combined Frontend/Backend validation is green; deployment, backup/restore and environment closure remain.

## First implemented slice

This change adds an Operational Readiness contract gate that verifies:

- Required Backend, Frontend, E2E, Docker and documentation artifacts exist.
- Customer, Operator and Admin authenticated route boundaries remain present.
- Frontends retain typecheck and production-build scripts.
- Direct Kimia infrastructure references are absent from Frontend code.
- Basic secret patterns are absent from operational surfaces.
- Docker Compose remains syntactically valid.

This gate is additive and does not enable financial rules, Kimia Write, balance mutation, tenant architecture changes or migration changes.
