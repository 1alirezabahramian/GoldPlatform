# GoldPlatform — Project State

- Updated: 2026-08-06
- Canonical recovery branch: `recovery/rc2-product-rebuild`
- Canonical SHA before this documentation alignment: `57d72651964bad162abb83e2a8b6753ac32fb168`
- Recovery status: **Backend recovery hardening in progress — canonical branch healthy**
- Open product/recovery PRs at this checkpoint: **0**

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

The canonical recovery branch now contains:

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

## Integrated recovery PRs

Validated recovery PRs merged into the canonical branch include `#149` through `#167`, excluding historical audit-only or superseded PRs that were intentionally closed without merge.

The latest integrated PR is `#167` — `Recovery: guard HTTP financial model mutations`.

## Validation status

Latest validated PR head before merge:

- PR: `#167`
- Head SHA: `9e867baa455563bdb73154e8f56b1858a0bc6906`
- Workflow: `Backend RC1 Validation` run `#261`
- Result: **EXECUTED — PASS**

Passed gates:

- Migration fresh
- Unit tests
- Feature tests
- Financial and Ledger tests
- Order lifecycle tests
- Trade idempotency and settlement tests
- Custody and delivery tests
- Permission tests
- Kimia mock tests
- Kimia read-only integration contract
- Full regression suite
- Laravel health check
- Docker Compose validation
- Secret scan

Canonical merge commit after that validated head: `57d72651964bad162abb83e2a8b6753ac32fb168`.

Production Ready is **NOT CLAIMED** because frontend, tenant isolation, deployment, backup/restore and final closure validation remain incomplete.

## Kimia safety

- Kimia Read and Kimia Write remain separate paths.
- Kimia Write remains disabled until real payloads, action codes, transaction codes, account mappings, retry behavior and post-write readback are confirmed from ground truth.
- Controllers and routes may not call Kimia infrastructure directly.
- Application services may not use raw HTTP or Kimia Client directly outside the accepted integration boundary.
- No sample AccountId, ProductId or transaction identifier may be treated as a permanent hard-coded rule.
- Money and weight calculations must use exact Decimal or String Decimal; float is prohibited.

## Remaining verified gaps

1. Recover and validate the Customer frontend against the canonical API contract.
2. Reconstruct Admin and Operator capabilities only from inspected historical evidence and current canonical needs.
3. Verify tenant/company/branch isolation, permission coverage and IDOR protection.
4. Validate frontend build, typecheck and E2E flows.
5. Validate OpenAPI against the final customer/admin/operator contracts.
6. Validate production compose, deployment, monitoring and backup/restore procedures.
7. Locate or establish the canonical CHANGELOG path; no root `CHANGELOG.md` was found at this checkpoint.
8. Produce final Recovery Closure only after all remaining product and release gates pass on exact SHAs.

## Current next step

Move from backend architecture hardening to a controlled capability-gap audit for Customer frontend and Admin/Operator recovery. Do not start a new stage unless a genuine missing capability is proven. Preserve historical branches as evidence and reconstruct only small, isolated, testable slices.
