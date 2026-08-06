# GoldPlatform — Project State

- Updated: 2026-08-06
- Canonical recovery branch: `recovery/rc2-product-rebuild`
- Canonical Head SHA: `9942c9cc7f0b9908e7d950d4ffdadeb23047e12e`
- Latest canonical merge: PR `#175` — Direct Settlement Completion Guard
- Recovery status: **Release Candidate — Final Audit and Closure In Progress**
- Open product/recovery PRs observed at audit start: **0**

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
17. Repository-level Operational Readiness contract.
18. Direct settlement completion guard requiring verified Kimia result evidence and post-write readback.

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

## Kimia safety

- Kimia Read and Kimia Write remain separate paths.
- Kimia Write remains disabled until real payloads, action codes, transaction codes, account mappings, retry behavior and post-write readback are confirmed from ground truth.
- Controllers and routes may not call Kimia infrastructure directly.
- Application services may not use raw HTTP or Kimia Client directly outside the accepted integration boundary.
- No sample AccountId, ProductId or transaction identifier may be treated as a permanent hard-coded rule.
- Money and weight calculations must use exact Decimal or String Decimal; float is prohibited.
- Direct customer financial settlement completion is blocked unless verified Kimia result evidence and post-write balance readback exist.

## Current CI evidence

The declared canonical branch is identical to commit `9942c9cc7f0b9908e7d950d4ffdadeb23047e12e`.

The GitHub connector currently returns no pull-request-triggered workflow runs and no combined status entries directly attached to this merge commit. This does not prove failure, but it means post-merge CI on the exact canonical merge SHA is **NOT CONFIRMED** from the available connector evidence.

Production Ready is therefore **NOT CLAIMED**.

## Remaining verified gaps

1. Confirm all required release workflows on one exact final canonical SHA.
2. Complete business workflow closure audit and targeted tests.
3. Prove tenant/company/branch isolation and IDOR protection.
4. Validate live Frontend authentication and environment integration.
5. Validate monitoring and production logging behavior.
6. Execute and retain backup/restore evidence.
7. Validate production deployment, migration and rollback procedures.
8. Run final OpenAPI contract validation.
9. Publish final Security, Test, Recovery and Release reports.
10. Create a final Recovery Closure checkpoint only after all mandatory gates are green on the same SHA.

## Current next step

Run CI for this documentation-alignment PR. After it is green, merge with exact Head SHA protection. Then select the smallest verified security/isolation or release-evidence gap; do not introduce financial rules, Kimia Write behavior or tenant architecture by assumption.
