# GoldPlatform — Project State

- Updated: 2026-08-06
- Canonical recovery branch: `recovery/rc2-product-rebuild`
- Canonical Head SHA: `fe0aad5c4920a650da2d9ba0755ab7883e5bf4a2`
- Latest canonical merge: PR `#181` — Operator Permission Gates
- Recovery status: **Release Candidate — Final Audit and Closure In Progress**

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
19. Customer order and delivery list owner-isolation regression proof.
20. Admin observability response redaction.
21. Operator queue response redaction.
22. Operator delivery action response redaction.
23. Explicit per-action operator permission gates with additive rollout compatibility.

## Recent canonical security and closure merges

- PR `#176` — Final audit documentation alignment.
- PR `#177` — Customer order/delivery list IDOR isolation proof.
- PR `#178` — Admin observability payload redaction.
- PR `#179` — Operator queue response redaction.
- PR `#180` — Operator delivery action response redaction.
- PR `#181` — Explicit operator permission gates.

## Latest validated CI evidence

PR `#181` was validated on exact Head SHA:

- Head SHA: `016d6f878e69e425badce36d92799949b87fd180`
- Operational Readiness #11: **EXECUTED — PASS**
- Backend RC1 Validation #302: **EXECUTED — PASS**
- Migration fresh: **PASS**
- Unit tests: **PASS**
- Feature tests: **PASS**
- Permission tests: **PASS**
- Full regression: **PASS**

Canonical merge SHA after PR `#181`:

- `fe0aad5c4920a650da2d9ba0755ab7883e5bf4a2`

The available connector confirms the merge commit and the pre-merge exact-Head CI evidence. A separate push-triggered post-merge workflow result attached directly to this merge SHA is not claimed unless independently observed.

Production Ready is therefore **NOT CLAIMED**.

## Kimia safety

- Kimia Read and Kimia Write remain separate paths.
- Kimia Write remains disabled until real payloads, action codes, transaction codes, account mappings, retry behavior and post-write readback are confirmed from ground truth.
- Controllers and routes may not call Kimia infrastructure directly.
- Application services may not use raw HTTP or Kimia Client directly outside the accepted integration boundary.
- No sample AccountId, ProductId or transaction identifier may be treated as a permanent hard-coded rule.
- Money and weight calculations must use exact Decimal or String Decimal; float is prohibited.
- Direct customer financial settlement completion is blocked unless verified Kimia result evidence and post-write balance readback exist.

## Remaining verified gaps

1. Prove tenant/company/branch isolation after accepted architecture ground truth exists.
2. Validate live Frontend authentication and environment integration.
3. Validate monitoring and production logging behavior.
4. Execute and retain backup/restore evidence.
5. Validate production deployment, migration and rollback procedures.
6. Run final OpenAPI contract validation.
7. Publish final Security, Test, Recovery and Release reports.
8. Create a final Recovery Closure checkpoint only after all mandatory gates are green on the same SHA.

## Current next step

Validate and merge this documentation-alignment change on its exact Head SHA. Then continue with the smallest verified release-evidence or security gap that does not require guessing financial rules, Kimia Write behavior, tenant architecture, company scope or branch scope.
