# GoldPlatform V2-01 — Canonical Customer↔Kimia Binding Inventory

**Stage:** V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification  
**Canonical base:** `recovery/rc2-product-rebuild`  
**Canonical base exact SHA:** `d9ee5fee69969fa02ac25c96d8e1653143ba413b`  
**Working branch:** `v2/v2-01-canonical-runtime-integration`

## Result first

The canonical source proves the structural chain:

`Authenticated User -> users.account_id -> accounts.id -> accounts.kimia_id`

and a separate synchronized Kimia projection:

`external_accounts.provider = kimia -> external_accounts.external_id`

The current canonical registration path does **not** populate or approve `users.account_id`; `RegistrationService` explicitly retains a TODO for `Create Kimia Account / Link Account / Assign Default Group`. Therefore the schema relation is not an approved runtime binding workflow.

The owner-approved binding cardinality is recoverable from Accepted Project Memory, but the historical unique-binding and immutability implementation was only prepared and is not evidenced as canonical runtime code. Accepted multi-tenant direction also exists, while canonical runtime remains without an implemented Tenant root. Customer financial reads therefore remain correctly fail-closed.

## Canonical inventory

| Capability / structure | Canonical evidence | Classification | Notes |
|---|---|---|---|
| `Account` model | `backend/app/Models/Account.php` | REUSE AS-IS | Explicit `kimia_id`; no fallback allowed. |
| `accounts.kimia_id` DB constraint | `2026_07_19_140812_create_accounts_table.php` | REUSE AS-IS | Non-null and unique. |
| `User -> Account` relation | `backend/app/Models/User.php` | REUSE AS-IS | Structural relation only; runtime approval/population is absent. |
| `users.account_id` DB constraint | `2026_07_19_141317_add_account_id_to_users_table.php` | REUSE AFTER FIX | Nullable FK only; no unique non-null enforcement. |
| `ExternalAccount` Kimia projection | `backend/app/Models/ExternalAccount.php` | REUSE AS-IS | Read/reconciliation projection only. |
| Kimia HTTP read boundary | `backend/app/Clients/KimiaReadClient.php` | REUSE AS-IS | Canonical GET-only client with explicit error propagation. |
| Account read repository | merged PR #150 | REUSE AS-IS | Confirmed `Type=3` retail-account read. |
| Balance read repository | merged PR #150 | REUSE AS-IS | `/api/voucher/balance/{AccountId}`. |
| Dynamic Coin/Currency reads | merged PR #150 | REUSE AS-IS | No fixed Coin/Currency identifier list. |
| Customer dashboard/assets fail-closed reads | canonical Customer controllers | REUSE AS-IS | HTTP 503 until verified Kimia resolution exists. |
| Authenticated customer account resolver | no dedicated canonical implementation evidenced | NOT IMPLEMENTED | No inference by mobile/name/national code/account code/sample IDs. |
| Registration -> Kimia binding | canonical `RegistrationService` TODO; no assignment to `account_id` | NOT IMPLEMENTED | No auto-link or guessed Kimia create/link behavior. |
| Owner-approved binding cardinality | Accepted Project Memory / ADR-024 reference | REUSE AFTER FIX | Rule is accepted; canonical enforcement is incomplete. |
| Unique `users.account_id` historical migration | Accepted Project Memory says prepared, not runtime-applied | HISTORICAL ONLY | Candidate for controlled recovery only after tenant/runtime preconditions. |
| User/Account/ExternalAccount immutability guards | Accepted Project Memory says prepared | HISTORICAL ONLY | Not treated as canonical implementation. |
| Tenant architecture rule | Accepted Project Memory / ADR-026 direction | REUSE AFTER FIX | White-label/multi-tenant direction is accepted. |
| Canonical Tenant runtime root | no active canonical Tenant implementation evidenced in reviewed runtime | NOT IMPLEMENTED | Do not cement single-tenant assumptions with a binding migration. |
| Tenant -> Kimia connector/book runtime mapping | accepted direction exists; canonical runtime mapping not evidenced | BLOCKED BY GROUND TRUTH | Required before tenant-aware financial resolver activation. |
| Read-only reconciliation bridge | reconstructed from PR #196 concept | TESTED — NOT MERGED | Exact-head `058680093fea90a30235acc1171c744a3c472ca1`: Backend RC1 #437 PASS; Operational Readiness #47 PASS. |
| Kimia Write | deny-by-default foundation | BLOCKED BY GROUND TRUTH | No production write activation or payload mapping. |

## Accepted binding rule vs implementation state

Accepted Project Memory records:

`one GoldPlatform login -> zero or one local Account -> zero or one Kimia AccountId`

`one Kimia AccountId -> no more than one GoldPlatform login`

and records an established Kimia AccountId binding as immutable. This is valid business/architecture Ground Truth. It does **not** mean the current canonical runtime enforces the rule: the current `users.account_id` migration is a nullable FK without the historical prepared unique index, and the current registration path does not create or approve a link.

Classification:

- binding rule: **REUSE AFTER FIX** into canonical runtime;
- historical prepared enforcement: **HISTORICAL ONLY** until reconstructed and revalidated;
- current end-to-end runtime binding: **NOT IMPLEMENTED**.

## Tenant prerequisite

Accepted historical architecture says GoldPlatform is White-label / Multi-tenant and Khalifeh Coin is the first tenant, not a hidden global default. The prepared direction includes tenant-owned records, tenant-scoped mobile identity, verified Host Tenant, authenticated user/Tenant equality, and a tenant-specific Kimia connector/book boundary.

Current canonical runtime does not evidence that Tenant root. Historical PR #131 explicitly described Tenant/Company/tenant-specific Kimia configuration as absent and was closed without merge; it is evidence only. Historical stacked tenant work such as PR #109 is not a canonical integration source.

Therefore V2-01 does not add a `users.account_id` unique migration or activate a Customer financial resolver yet. Doing so before the Tenant identity boundary is canonical could silently harden an interim single-tenant model.

## Registration/Auth drift

Canonical `RegistrationService` creates a Wallet and default WalletAccounts after `User::create()`.

Canonical `UserObserver`, registered by `AppServiceProvider`, also creates a Wallet and the same default accounts on user creation.

Classification: **DUPLICATE CANDIDATE**.

This drift is recorded but not repaired inside the reconciliation slice because changing Registration/Auth behavior needs a controlled Auth comparison and is not necessary to prove read-only reconciliation.

## PR #196 historical compare

PR #196 remains evidence, not an integration source. Reused concepts:

- SELECT-only inspection of `accounts`, `external_accounts`, and `users.account_id`;
- conflict classification;
- no automatic repair;
- before/after snapshots proving zero mutation.

Classification: **REUSE AFTER FIX**.

The V2-01 implementation was reconstructed narrowly on the canonical branch; no broad merge/cherry-pick was performed.

## Exact answer to the V2-01 core question

Structurally the code can express:

`request->user()`  
`-> User.account_id`  
`-> Account.id`  
`-> Account.kimia_id`  
`-> BalanceReadRepository::forAccount(kimia AccountId)`

But a logged-in Customer cannot yet be declared to resolve to the **correct verified** Kimia AccountId because canonical code does not prove who/what populates and approves `users.account_id`, and the Tenant/connector identity context is not implemented.

End-to-end authenticated resolver: **NOT IMPLEMENTED**.  
Resolver activation: **BLOCKED BY GROUND TRUTH**.

## Reconciliation slice and test evidence

Files:

- `backend/app/Services/Kimia/CustomerAccountReconciliationService.php`
- `backend/app/Console/Commands/KimiaInspectAccountReconciliation.php`
- `backend/tests/Feature/KimiaInspectAccountReconciliationTest.php`

Behavior:

- strictly read-only;
- reports matched, local-only, external-only, duplicate and orphan states;
- never creates, links, repairs, updates, deletes or backfills a binding;
- snapshots `accounts`, `external_accounts`, and `users` before/after inspection.

CI history:

- Head `d61f491f77e349d88299ac60dd4057807777bc6f`
  - Operational Readiness #45 — EXECUTED — PASS
  - Backend RC1 Validation #435 — EXECUTED — FAIL
- Failure source was a test fixture incompatible with canonical non-null `accounts.kimia_id`; fixture corrected without business behavior change.
- Exact head `058680093fea90a30235acc1171c744a3c472ca1`
  - Operational Readiness #47 — EXECUTED — PASS
  - Backend RC1 Validation #437 — EXECUTED — PASS

Current reconciliation status: **TESTED — NOT MERGED**.

## Migration / DB conclusion

No migration is introduced in this slice.

The historical nullable-unique `users.account_id` migration and immutability guards are recovery candidates, not safe copy targets. Before any binding migration, V2-01 must reconcile them with the accepted Tenant architecture and current canonical schema/runtime.

## API / OpenAPI / Frontend conclusion

No API/OpenAPI/Frontend change in this slice. Existing Customer financial endpoints remain fail-closed and no unavailable balance is converted to zero.

## Permission / Audit / Idempotency

- HTTP permission change: NOT APPLICABLE.
- Write idempotency: NOT APPLICABLE.
- Reconciliation output is audit/inspection evidence only, never balance authority or a repair mechanism.

## Next exact work

1. Record a compact binding/Tenant traceability activation gate.
2. Preserve the historical unique-binding and immutability code as `HISTORICAL ONLY / REUSE AFTER FIX`; do not copy blindly.
3. Prove the canonical Tenant/Company/Connector root or explicitly reconstruct it under its accepted architecture before a financial resolver is activated.
4. Define/implement the approved source that creates `users.account_id` without inference or Kimia Write guessing.
5. Only then implement the fail-closed authenticated resolver and connect Customer financial reads through canonical Kimia read repositories.
