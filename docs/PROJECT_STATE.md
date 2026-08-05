# GoldPlatform — Project State

- Updated: 2026-08-06
- Canonical recovery branch: `recovery/rc2-product-rebuild`
- Canonical SHA before Phase 05–08 operationalization: `84cd5d03b427c9d6e3cb58ffa2aaf96f7ce89c4c`
- Recovery status: **Frontend Recovery Complete — Operationalization In Progress**
- Open product/recovery PRs before this slice: **0**

## Source-of-truth contract

Kimia is the final source of truth for customer financial balances:

- Money
- Gold
- Coin
- Currency

GoldPlatform must not publish an independent or competing final balance for these four asset classes.

Internal Ledger, Journal, Event Store, Idempotency Registry and Balance Projection are permitted only for audit, traceability, intent/result recording, workflow, reconciliation and duplicate/incomplete-operation detection. They are not final customer balance authorities.

Any internal projection or snapshot must be Kimia-derived, timestamped, rebuildable and reconcilable. In a conflict, Kimia is authoritative.

GoldPlatform is the source of truth for physical Custody / Amanat. Custody remains separate from financial balances.

## Canonical recovery content

The canonical recovery branch contains:

1. Business Engine baseline recovery.
2. Customer API contracts and explicit customer read resources.
3. Kimia read integration with dynamic coin/currency reads.
4. Custody and delivery customer boundaries.
5. Internal balance projection marked audit/reconciliation-only.
6. Legacy internal wallet deposit/withdraw disabled.
7. Settlement completion from ledger-only evidence disabled.
8. Balance reservations retained as workflow intent only.
9. Internal wallet balances hidden from serialization and legacy customer overview.
10. Admin financial policy mutation disabled pending accepted ground truth.
11. Sensitive outbox replay and automatic outbox scheduling guarded.
12. Scheduled and queued Kimia/financial execution guarded.
13. HTTP, service, event/observer and direct financial-model mutation boundaries guarded.
14. Customer Frontend Foundation and core read pages.
15. Admin and Operator Frontend Foundation.
16. Strict Frontend typecheck, production builds and Chromium E2E validation.

## Frontend recovery closure

Merged and validated:

- PR `#170` — Customer Frontend Foundation.
- PR `#171` — Customer core read pages.
- PR `#172` — Admin and Operator Frontend Foundation.
- PR `#173` — Combined Frontend Release Validation.

Latest Frontend release validation head:

- Head SHA: `ddfdf572bbacdaa701b8f158e7d6145491555ca7`
- Customer Frontend: **EXECUTED — PASS**
- Admin Operator Frontend: **EXECUTED — PASS**
- Frontend Release Validation: **EXECUTED — PASS**
- Chromium E2E: **EXECUTED — PASS**
- Backend RC1 Regression: **EXECUTED — PASS**

Canonical merge commit after Frontend recovery: `84cd5d03b427c9d6e3cb58ffa2aaf96f7ce89c4c`.

## Kimia safety

- Kimia Read and Kimia Write remain separate paths.
- Kimia Write remains disabled until real payloads, action codes, transaction codes, account mappings, retry behavior and post-write readback are confirmed from ground truth.
- Controllers and routes may not call Kimia infrastructure directly.
- Application services may not use raw HTTP or Kimia Client directly outside the accepted integration boundary.
- No sample AccountId, ProductId or transaction identifier may be treated as a permanent hard-coded rule.
- Money and weight calculations must use exact Decimal or String Decimal; float is prohibited.

## Phase 05–08 operationalization

Phase 05 — Business workflow closure:

- Verify Order, Quote, Idempotency, Settlement and Reconciliation capabilities against accepted contracts.
- Do not enable Kimia Write without accepted real ground truth.

Phase 06 — Security and isolation:

- Prove tenant/company/branch isolation, Permission coverage and IDOR protection.

Phase 07 — Runtime integration:

- Validate live authentication, queue, cache, notification, monitoring and controlled Kimia-read unavailability.

Phase 08 — Production closure:

- Validate deployment manifests, backup/restore, migration procedures, rollback and final release gates.

The first operationalization slice adds a repository-level readiness contract and Docker Compose validation. Full details are recorded in `docs/recovery/PHASE-05-08-OPERATIONALIZATION.md`.

## Remaining verified gaps

1. Business workflow closure audit and targeted tests.
2. Tenant/company/branch isolation and IDOR proof.
3. Live Frontend authentication and environment integration.
4. Monitoring and production logging validation.
5. Backup/restore execution evidence.
6. Production deployment and rollback validation.
7. OpenAPI final contract validation.
8. Final Recovery Closure on one exact green SHA.

Production Ready is **NOT CLAIMED** until these gaps are closed with executed evidence.

## Current next step

Run the Operational Readiness workflow on its exact PR Head SHA. If green, merge the additive gate, then continue with the smallest verified Phase 05 business-workflow closure gap. Do not introduce financial rules, Kimia Write behavior or tenant architecture by assumption.
