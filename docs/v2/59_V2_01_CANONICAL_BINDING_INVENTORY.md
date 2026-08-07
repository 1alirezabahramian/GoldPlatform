# GoldPlatform V2-01 — Canonical Customer↔Kimia Binding Inventory

**Stage:** V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification  
**Canonical base:** `recovery/rc2-product-rebuild`  
**Canonical base exact SHA:** `d9ee5fee69969fa02ac25c96d8e1653143ba413b`  
**Working branch:** `v2/v2-01-canonical-runtime-integration`

## Result first

Canonical code structurally provides:

`Authenticated User -> users.account_id -> accounts.id -> accounts.kimia_id`

but canonical Registration does not populate or approve `users.account_id`. Therefore the structural FK is not yet a complete runtime binding workflow.

Source recovery has now re-established the accepted binding and Tenant rules from Accepted Project Memory and exact historical ADR-024/ADR-026 on `work/product-kimia-next`. The historical branch itself remains **HISTORICAL ONLY** because it is 6 commits ahead / 577 behind canonical.

## Ground Truth recovered

### Binding — ADR-024

- one platform login/account -> zero or one local Account -> zero or one Kimia AccountId;
- one Kimia AccountId -> no more than one platform login/account;
- established Kimia AccountId is immutable;
- second account inside same Tenant uses distinct mobile + distinct Kimia AccountId;
- national code may repeat;
- account_code is not the financial identity.

### Tenancy — ADR-026

- shared database/shared schema with mandatory tenant ownership;
- mobile unique inside Tenant;
- one active Kimia connector/book per Tenant in first release;
- Platform Super Admin separate from Tenant Admin/Operator;
- verified domain resolution plus authenticated user/Tenant cross-check;
- no all-table migration and no unique-index replacement without duplicate preflight.

## Capability inventory

| Capability | Evidence | Classification |
|---|---|---|
| `Account.kimia_id` | canonical model/migration | REUSE AS-IS |
| `User.account_id` structural FK | canonical model/migration | REUSE AS-IS |
| Approved Registration→Account binding workflow | Registration TODO / no assignment | NOT IMPLEMENTED |
| Binding cardinality/immutability rule | Accepted ADR-024 | REUSE AS-IS |
| Historical unique `users.account_id` migration | `work/product-kimia-next` | HISTORICAL ONLY / REUSE AFTER FIX candidate |
| Historical binding immutability guards | `work/product-kimia-next` + ADR-024 | HISTORICAL ONLY / REUSE AFTER FIX candidate |
| Kimia Read foundation | merged PR #150 | REUSE AS-IS |
| Customer financial fail-closed state | canonical Customer controllers | REUSE AS-IS |
| Read-only reconciliation | current V2-01 implementation | TESTED — NOT MERGED |
| ADR-026 architecture decisions | Accepted ADR-026 | REUSE AS-IS |
| Historical bounded Tenant foundation | `work/product-kimia-next` | REUSE AFTER FIX |
| Reconstructed Tenant root/domain/context/resolver | current V2 branch | IMPLEMENTED — NOT TESTED |
| `users.tenant_id` + authenticated tenant cross-check | not in current slice | NOT IMPLEMENTED |
| Tenant→Kimia connector/book runtime config | deferred by ADR-026 | NOT IMPLEMENTED |
| Authenticated Customer→Kimia financial resolver | prerequisites incomplete | NOT IMPLEMENTED |
| Kimia Write | deny-by-default | BLOCKED BY GROUND TRUTH |

## Historical unique-binding migration

Recovered migration `2026_08_03_120100_enforce_unique_user_account_binding.php` checks duplicate non-null `users.account_id` values first and aborts if any exist before adding a unique index.

It is not applied/reconstructed in this slice. Current runtime duplicate/orphan evidence is required before identity-constraint recovery.

## Bounded Tenant recovery

Recovered according to ADR-026's explicitly approved first checkpoint:

- Tenant + TenantDomain models;
- `tenants` and `tenant_domains` additive migrations;
- normalized verified-domain resolver;
- request-scoped TenantContext that cannot switch within one scope;
- fail-closed middleware alias;
- isolation regression tests.

The middleware is **not attached to production Customer/Admin/Operator/Kimia routes** by this slice.

No existing business table receives `tenant_id` here.

## Registration/Auth drift

Canonical RegistrationService and active UserObserver both create Wallet/default accounts on User creation.

Classification: **DUPLICATE CANDIDATE**.

This remains recorded but is not silently repaired inside the Tenant/reconciliation slice.

## Exact CI evidence

Exact head `245875155793e20258f460018efb3ad4a94c3207` before the new Tenant foundation recovery:

- Backend RC1 Validation #444 — EXECUTED — PASS
- Operational Readiness #54 — EXECUTED — PASS

Read-only reconciliation: **TESTED — NOT MERGED**.

New Tenant recovery commits require their own exact-head CI before classification can move from IMPLEMENTED — NOT TESTED.

## API / OpenAPI / Frontend

No Customer API/OpenAPI/Frontend behavior change. Financial endpoints remain fail-closed and no unavailable balance is converted to zero.

## Next exact work

1. Validate the reconstructed bounded Tenant foundation on exact-head CI.
2. Keep the historical unique-binding migration and immutability guards preserved until current duplicate/orphan preflight can be established.
3. Recover/compare the exact approved source of `users.account_id` population; do not infer it.
4. Add authenticated Tenant cross-check only in a separately bounded table-group checkpoint after `users.tenant_id` migration/backfill is safely designed and validated.
5. Only then implement the fail-closed Customer→Kimia resolver.
