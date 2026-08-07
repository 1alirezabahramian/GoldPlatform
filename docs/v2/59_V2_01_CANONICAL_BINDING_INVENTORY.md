# GoldPlatform V2-01 — Canonical Customer↔Kimia Binding Inventory

**Stage:** V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification  
**Canonical base:** `recovery/rc2-product-rebuild`  
**Canonical base exact SHA:** `d9ee5fee69969fa02ac25c96d8e1653143ba413b`  
**Working branch:** `v2/v2-01-canonical-runtime-integration`

## Result first

The canonical source proves a structural chain:

`Authenticated User -> users.account_id -> accounts.id -> accounts.kimia_id`

and a separate synchronized Kimia projection:

`external_accounts.provider = kimia -> external_accounts.external_id`

However, the current canonical registration path does **not** populate or approve `users.account_id`. `RegistrationService` explicitly retains a TODO for `Create Kimia Account / Link Account / Assign Default Group`. Therefore the schema relation is not evidence of an approved runtime binding workflow.

The current customer financial endpoints correctly remain fail-closed until this gap is resolved.

## Canonical inventory

| Capability / structure | Canonical evidence | Classification | Notes |
|---|---|---|---|
| `Account` model | `backend/app/Models/Account.php` with `kimia_id`, `account_code`, `group_id`, `account_type`, sync fields | REUSE AS-IS | `kimia_id` is the explicit local Kimia identifier field. No fallback is allowed. |
| `accounts.kimia_id` DB constraint | `2026_07_19_140812_create_accounts_table.php` | REUSE AS-IS | Non-null and unique in canonical migration. |
| `User -> Account` relation | `backend/app/Models/User.php`: `account_id` fillable + `belongsTo(Account::class)` | REUSE AS-IS | Structural binding exists. Runtime approval/population is not implemented in RegistrationService. |
| `users.account_id` DB constraint | `2026_07_19_141317_add_account_id_to_users_table.php` | REUSE AFTER FIX | Nullable FK only; canonical migration does not enforce unique non-null user-to-account binding. Reconciliation must report duplicate bindings. |
| `ExternalAccount` Kimia projection | `backend/app/Models/ExternalAccount.php`: `provider`, `external_id`, sync metadata | REUSE AS-IS | Read/reconciliation evidence only in V2-01. |
| Kimia HTTP read boundary | `backend/app/Clients/KimiaReadClient.php` | REUSE AS-IS | GET-only canonical client; explicit error propagation. |
| Account read repository | merged recovery PR #150 | REUSE AS-IS | Canonical `Type=3` retail-account query. |
| Balance read repository | merged recovery PR #150 | REUSE AS-IS | Reads `/api/voucher/balance/{AccountId}`. |
| Dynamic Coin/Currency reads | `ProductReadRepository` from merged recovery PR #150 | REUSE AS-IS | No hard-coded asset identifiers. |
| Customer dashboard financial read | `CustomerDashboardController` | REUSE AS-IS | Fail-closed HTTP 503 until verified Kimia resolution exists. |
| Customer assets financial read | `CustomerAssetReadController` | REUSE AS-IS | Money/Gold/Coin/Currency all fail closed; no internal Wallet/Ledger values exposed. |
| Authenticated customer account resolver | No dedicated canonical resolver evidenced | NOT IMPLEMENTED | Must not infer from mobile/name/national code/account code/sample IDs. |
| Registration -> Kimia binding | `RegistrationService` contains explicit TODO and never assigns `account_id` | NOT IMPLEMENTED | No auto-link or guessed POST / mapping is allowed. |
| Runtime verified binding semantics | Schema exists; approved population/verification source absent | BLOCKED BY GROUND TRUTH | Owner-approved cardinality exists historically, but runtime implementation is missing. |
| Tenant/Company/Connector scoping | Historical Project Memory describes interim single-Tenant schema and future tenant-scoped identity, but current canonical runtime proof is incomplete | BLOCKED BY GROUND TRUTH | No silent Tenant/Connector redesign in this stage. |
| Read-only reconciliation bridge | reconstructed from PR #196 concept | IMPLEMENTED — NOT TESTED | Exact-head CI required. |
| Kimia Write | deny-by-default foundation exists | BLOCKED BY GROUND TRUTH | No production write activation or payload mapping. |

## Owner-approved historical binding rule

Project Memory records the accepted cardinality:

`one GoldPlatform login -> zero or one local Account -> zero or one Kimia AccountId`

and:

`one Kimia AccountId -> no more than one GoldPlatform login`

It also records `Kimia AccountId` as an immutable financial binding once established. This is valid business/architecture evidence, but GitHub canonical still wins for implementation state: the unique user binding migration/immutable guards described as prepared in that historical checkpoint are not evidenced in the reviewed canonical migration/model path.

Therefore V2-01 preserves the accepted rule but does not pretend the current runtime enforces it.

## Registration/Auth drift discovered

Canonical `RegistrationService` currently creates a Wallet and two default WalletAccounts after `User::create()`.

Canonical `UserObserver` is also registered in `AppServiceProvider` and creates a Wallet plus the same two default accounts on user creation.

Classification: **DUPLICATE CANDIDATE** / canonical drift.

This is relevant to binding trace because Registration is not currently a trustworthy source of Customer↔Kimia linkage. It is not repaired inside the reconciliation slice because changing Registration/Auth behavior requires a separate controlled comparison with the accepted Auth contract and prior recovery history.

## Historical compare

### PR #196

PR #196 remains evidence, not an integration source. Reused behavior:

- SELECT-only inspection of `accounts`, `external_accounts`, and `users.account_id`;
- conflict classification;
- no auto-fix;
- before/after table snapshots proving zero mutation.

Classification: **REUSE AFTER FIX**.

The V2-01 reconstruction keeps a defensive `account_missing_kimia_id` state but canonical schema declares `accounts.kimia_id` non-null. Tests therefore use only schema-valid Account rows and do not fabricate a null Kimia identifier.

### Historical Customer asset implementation

Older Customer asset work temporarily read internal Wallet/Ledger projections. Canonical recovery superseded that behavior. Current Customer financial endpoints fail closed and must remain Kimia-authoritative.

Classification of Ledger-derived customer final balance: **SUPERSEDED**.

## Exact answer to the V2-01 core question

### What is structurally available?

`request->user()`  
`-> User.account_id`  
`-> Account.id`  
`-> Account.kimia_id`  
`-> BalanceReadRepository::forAccount(kimia AccountId)`

### What is still missing?

There is no canonical implementation proving **who/what sets and approves `users.account_id`**. Registration explicitly does not do it. The DB FK is nullable and does not by itself guarantee one-user-per-account cardinality.

Therefore a logged-in Customer cannot yet be declared to resolve to the correct Kimia AccountId merely because these columns exist.

Current classification of the end-to-end authenticated resolver: **BLOCKED BY GROUND TRUTH / NOT IMPLEMENTED**.

## Reconciliation slice

Files:

- `backend/app/Services/Kimia/CustomerAccountReconciliationService.php`
- `backend/app/Console/Commands/KimiaInspectAccountReconciliation.php`
- `backend/tests/Feature/KimiaInspectAccountReconciliationTest.php`

Behavior:

- strictly read-only;
- compares explicit local Account/Kimia identity to synchronized external Kimia projection;
- reports matched/local-only/external-only/duplicate/orphan states;
- never creates, links, repairs, updates, deletes, or backfills bindings;
- tests snapshot `accounts`, `external_accounts`, and `users` before/after inspection.

## CI history for this slice

- Head `d61f491f77e349d88299ac60dd4057807777bc6f`
  - Operational Readiness #45 — EXECUTED — PASS
  - Backend RC1 Validation #435 — EXECUTED — FAIL
- Static schema comparison identified a test fixture incompatible with canonical non-null `accounts.kimia_id`.
- The fixture was corrected in commit `eb84cb61111c6249dc5563f5cccaf18bc63d32fd` without changing business behavior.
- Exact-head CI for the corrected commit is required before PASS is claimed.

## Migration / DB conclusion

No new migration is added in this slice.

A unique non-null `users.account_id` constraint and Tenant-scoped identity changes existed as prepared historical direction, but current canonical state must be compared and validated before any migration is introduced. V2-01 will not create a migration merely to force an assumed model.

## API / OpenAPI conclusion

No API/OpenAPI change in this slice. Existing customer balance endpoints remain fail-closed. Reconciliation is internal inspection only.

## Permission / Audit / Idempotency

- No new HTTP permission is introduced.
- No mutation means write idempotency is NOT APPLICABLE.
- Reconciliation output is evidence, not balance authority and not a repair mechanism.

## Next exact work

1. Wait for/inspect exact-head CI on the corrected reconciliation test.
2. Trace prior accepted identity migrations/guards against current canonical to classify lost/recovered implementation accurately.
3. Inspect Tenant/Company/Connector implementation currently present on canonical; distinguish active runtime scope from historical prepared design.
4. Do not implement Customer balance resolver until the source of approved `users.account_id` population is proven or explicitly defined.
5. Once binding Ground Truth is complete, implement fail-closed resolver and connect Customer financial reads only through canonical Kimia read repositories.
