# GoldPlatform V2-01 — Binding / Tenant Activation Traceability Gate

**Stage:** V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification

## Decision

Customer Money/Gold/Coin/Currency reads remain fail-closed until the authenticated Customer can be resolved through an approved binding and the required Tenant context to one exact Kimia AccountId.

## Recovered authoritative sources

Source recovery re-established:

- Accepted Project Memory snapshot dated 2026-08-03;
- `ADR-024 — Platform User to Kimia Account Binding`;
- `ADR-026 — Multi-tenancy Isolation Strategy`;
- historical bounded implementation on `work/product-kimia-next`.

The historical branch is materially diverged from canonical and remains **HISTORICAL ONLY** at branch level. Individual components may be recovered only after current-code comparison.

## Traceability matrix

| Requirement | Ground Truth | Current implementation | Classification / gate |
|---|---|---|---|
| Kimia final authority for Money/Gold/Coin/Currency | project architecture | canonical Kimia reads + Customer fail-closed controllers | REUSE AS-IS |
| one login/account -> zero/one Account -> zero/one Kimia AccountId | ADR-024 | structural nullable `users.account_id`; Registration does not link | Rule REUSE AS-IS / workflow NOT IMPLEMENTED |
| one Kimia AccountId not shared by multiple logins | ADR-024 | `accounts.kimia_id` unique; `users.account_id` not unique | historical unique migration preserved; current enforcement incomplete |
| established AccountId immutable | ADR-024 | historical guards recovered, not canonical | HISTORICAL ONLY / REUSE AFTER FIX candidate |
| shared DB/shared schema tenancy | ADR-026 | bounded root/domain foundation reconstructed in V2 branch | IMPLEMENTED — NOT TESTED |
| verified domain Tenant resolution | ADR-026 | TenantResolver + inactive middleware alias reconstructed | IMPLEMENTED — NOT TESTED |
| authenticated user/Tenant cross-check | ADR-026 | requires `users.tenant_id` table-group migration/backfill | NOT IMPLEMENTED |
| one active Kimia connector/book per Tenant first release | ADR-026 | connector/config implementation explicitly deferred | NOT IMPLEMENTED |
| no fallback Tenant | ADR-026 | resolver returns null for unknown/unverified/inactive domains | IMPLEMENTED — NOT TESTED |
| reconciliation reports conflicts without repair | V2-01 | service/command/tests | TESTED — NOT MERGED |
| Customer financial presentation reads Kimia only | architecture | Customer endpoints remain 503 pending resolver | REUSE AS-IS |
| Kimia Write | deny-by-default | not activated | BLOCKED BY GROUND TRUTH |

## Future Customer resolver gate

A Customer financial resolver may return a Kimia AccountId only when:

1. authenticated User is valid;
2. verified Tenant context required by ADR-026 is active;
3. authenticated User belongs to that Tenant once `users.tenant_id` is safely introduced;
4. `users.account_id` was populated by an approved non-inferred workflow;
5. referenced Account exists;
6. Account exposes exact `kimia_id`;
7. duplicate/orphan/ambiguous binding state is absent;
8. Tenant-specific connector/book context is resolved when the connector checkpoint is implemented;
9. no mobile/name/national-code/account-code/sample/first/zero fallback is used.

Any failed/unproven check keeps the financial path unavailable.

## Migration gate

### Tenant root/domain migrations

ADR-026 explicitly approved this first bounded checkpoint. The reconstructed migrations create only:

- `tenants`;
- `tenant_domains`.

They do not alter existing business tables and do not insert a hidden default Tenant.

### Binding unique-index migration

Historical `users.account_id` unique migration includes duplicate preflight. It is **not** reconstructed/applied in this slice.

Before recovery/application:

- current duplicate/orphan state must be inspected read-only;
- runtime/shop migration state must be known;
- rollback/data-preservation behavior must be validated;
- exact-head tests/CI must pass.

### `users.tenant_id`

Not part of this slice. It requires a separate bounded table-group migration/backfill checkpoint and authenticated user/Tenant isolation tests.

## Current safety boundary

The `tenant.resolve` middleware alias is registered but not attached to current production business routes. No Customer/Admin/Operator/Kimia route behavior changes in this slice.

No connector credential movement, Kimia Write, balance mutation, business-table tenant backfill, or Customer resolver activation occurs.

## Validation

Read-only reconciliation and prior documentation state at exact head `245875155793e20258f460018efb3ad4a94c3207`:

- Backend RC1 Validation #444 — EXECUTED — PASS
- Operational Readiness #54 — EXECUTED — PASS

The reconstructed Tenant foundation is **IMPLEMENTED — NOT TESTED** pending exact-head CI on the latest commit.
