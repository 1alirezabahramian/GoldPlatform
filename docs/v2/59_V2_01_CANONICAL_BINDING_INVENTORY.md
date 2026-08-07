# GoldPlatform V2-01 — Canonical Customer↔Kimia Binding Inventory

**Stage:** V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification  
**Canonical base:** `recovery/rc2-product-rebuild`  
**Canonical base exact SHA:** `d9ee5fee69969fa02ac25c96d8e1653143ba413b`  
**Working branch:** `v2/v2-01-canonical-runtime-integration`

## Result first

Canonical GitHub proves this structural chain:

`Authenticated User -> users.account_id -> accounts.id -> accounts.kimia_id`

and a separate synchronized Kimia projection:

`external_accounts.provider = kimia -> external_accounts.external_id`

Canonical `RegistrationService` does **not** populate or approve `users.account_id`; it retains an explicit TODO for `Create Kimia Account / Link Account / Assign Default Group`. Therefore the schema relation is not evidence of an approved runtime binding workflow.

A prior V2-01 documentation pass also referred to historical `ADR-024` / `ADR-026`, unique-binding enforcement, immutability guards, and detailed Tenant rules as accepted Ground Truth. During the current evidence pass those exact claims could not be re-established from current GitHub search, the available `00_PROJECT_MEMORY.md`, the Domain Workshop, or the Kimia integration audit. Under NO GUESSING they are therefore downgraded to **UNVERIFIED HISTORICAL CLAIM / BLOCKED BY SOURCE RECOVERY** and must not drive schema or resolver implementation until their source is recovered.

White-label remains a confirmed product requirement from current project instructions. That does not, by itself, define a specific Tenant database model, host-routing rule, uniqueness scope, or Kimia connector/book mapping.

Customer financial reads therefore remain correctly fail-closed.

## Canonical inventory

| Capability / structure | Canonical evidence | Classification | Notes |
|---|---|---|---|
| `Account` model | `backend/app/Models/Account.php` | REUSE AS-IS | Explicit `kimia_id`; no fallback allowed. |
| `accounts.kimia_id` DB constraint | `2026_07_19_140812_create_accounts_table.php` | REUSE AS-IS | Non-null and unique. |
| `User -> Account` relation | `backend/app/Models/User.php` | REUSE AS-IS | Structural relation only; runtime approval/population is absent. |
| `users.account_id` DB constraint | `2026_07_19_141317_add_account_id_to_users_table.php` | REUSE AS-IS | Nullable FK; no unique constraint evidenced. Do not invent one before source recovery and data preflight. |
| `ExternalAccount` Kimia projection | `backend/app/Models/ExternalAccount.php` | REUSE AS-IS | Read/reconciliation projection only. |
| Kimia HTTP read boundary | `backend/app/Clients/KimiaReadClient.php` | REUSE AS-IS | Canonical GET-only client with explicit error propagation. |
| Account/Balance/Product read repositories | merged PR #150 | REUSE AS-IS | Existing canonical Kimia Read foundation. |
| Customer dashboard/assets fail-closed reads | canonical Customer controllers | REUSE AS-IS | HTTP 503 until verified Kimia resolution exists. |
| Authenticated customer account resolver | no dedicated canonical implementation evidenced | NOT IMPLEMENTED | No inference by mobile/name/national code/account code/sample IDs. |
| Registration -> Kimia binding | canonical `RegistrationService` TODO; no assignment to `account_id` | NOT IMPLEMENTED | No auto-link or guessed Kimia create/link behavior. |
| Exact binding cardinality beyond current schema | prior V2 notes referenced ADR-024, but source not re-established | BLOCKED BY GROUND TRUTH | Do not convert historical claim into migration/guard. |
| Binding immutability guard | prior V2 notes referenced prepared historical guards, but source not re-established | BLOCKED BY GROUND TRUTH | No canonical guard evidenced. |
| White-label product requirement | current project instructions | REUSE AS-IS | Product requirement only; does not define Tenant schema. |
| Canonical Tenant runtime root | no active canonical Tenant implementation evidenced | NOT IMPLEMENTED | Specific model/scoping remains unresolved. |
| Tenant/Company/Connector/Book identity rules | no exact current source recovered | BLOCKED BY GROUND TRUTH | Do not guess routing or uniqueness scope. |
| Read-only reconciliation bridge | reconstructed from PR #196 concept | TESTED — NOT MERGED | Exact-head CI evidence recorded below. |
| Kimia Write | deny-by-default foundation | BLOCKED BY GROUND TRUTH | No production write activation or payload mapping. |

## Source correction — historical ADR/Tenant claims

Current evidence recovery attempted all of the following:

- GitHub PR search for Tenant/Kimia connector/book/binding terms;
- GitHub commit search for `ADR-024`;
- GitHub code search for `tenant_id`;
- GitHub branch search for Tenant-related branches;
- available `00_PROJECT_MEMORY.md`;
- `41_GOLDPLATFORM_DOMAIN_WORKSHOP_2026-07-28.md`;
- `08_KIMIA_INTEGRATION_AUDIT.md`;
- continuation/handoff text available to this mission.

The exact ADR-024/ADR-026 documents or an equivalent authoritative source for the previously stated cardinality/Tenant rules were not recovered in this pass. First-search absence is not proof of non-existence, so the correct state is **BLOCKED BY SOURCE RECOVERY**, not `NOT IMPLEMENTED` based solely on absence and not accepted Ground Truth based solely on prior assistant text.

The Domain Workshop does independently support an important identity principle for products: mutable Kimia shortcut/search code must not be the stable identity; immutable Kimia ID is the stable integration identifier. That product identity rule must not be silently generalized into an unproven Customer↔Account cardinality rule.

## Registration/Auth drift

Canonical `RegistrationService` creates a Wallet and default WalletAccounts after `User::create()`.

Canonical `UserObserver`, registered by `AppServiceProvider`, also creates a Wallet and the same default accounts on user creation.

Classification: **DUPLICATE CANDIDATE**.

This drift is recorded but not repaired inside the reconciliation slice because changing Registration/Auth behavior requires a controlled Auth comparison and is not necessary to prove read-only reconciliation.

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

But a logged-in Customer cannot yet be declared to resolve to the **correct verified** Kimia AccountId because canonical code does not prove who/what populates and approves `users.account_id`. Any additional Tenant/Company/Connector/Book scope needed by the White-label architecture is also not yet grounded to an exact runtime model.

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
- Head `058680093fea90a30235acc1171c744a3c472ca1`
  - Operational Readiness #47 — EXECUTED — PASS
  - Backend RC1 Validation #437 — EXECUTED — PASS
- Head `1b7380abc688b4fea295176ffd759e3396164b71`
  - Operational Readiness #50 — EXECUTED — PASS
  - Backend RC1 Validation #440 — EXECUTED — PASS

Current reconciliation status: **TESTED — NOT MERGED**.

## Migration / DB conclusion

No migration is introduced in this slice.

A unique `users.account_id` constraint, immutability guard, Tenant-scoped uniqueness, or connector/book foreign-key model must not be created from unrecovered historical claims. Any future migration requires exact Ground Truth, read-only duplicate/orphan preflight, preservation/rollback analysis, tests, and exact-head CI.

## API / OpenAPI / Frontend conclusion

No API/OpenAPI/Frontend behavior change in this slice. Existing Customer financial endpoints remain fail-closed and no unavailable balance is converted to zero.

## Permission / Audit / Idempotency

- HTTP permission change: NOT APPLICABLE.
- Write idempotency: NOT APPLICABLE.
- Reconciliation output is audit/inspection evidence only, never balance authority or a repair mechanism.

## Next exact work

1. Keep the read-only reconciliation capability stable and green.
2. Continue source recovery specifically for the previously referenced ADR-024/ADR-026 or equivalent accepted decisions; do not substitute prior assistant prose for source evidence.
3. Continue Canonical/Historical search for the actual workflow that creates or approves `users.account_id`.
4. Determine the exact White-label Tenant/Company/Connector/Book runtime model only from recovered authoritative evidence or an explicit owner decision if evidence truly cannot be recovered.
5. Only then implement a fail-closed authenticated resolver and connect Customer financial reads through canonical Kimia read repositories.
