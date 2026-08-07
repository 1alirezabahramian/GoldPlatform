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

The canonical source also contains an isolated Kimia read path and intentionally fail-closed Customer financial endpoints. What is **not** yet proven is that every authenticated customer's runtime `users.account_id` binding is approved/verified and scoped correctly for the active Tenant/Company/Connector context, if such context is required. Therefore no customer balance resolver or auto-link is introduced in this slice.

## Canonical inventory

| Capability / structure | Canonical evidence | Classification | Notes |
|---|---|---|---|
| `Account` model | `backend/app/Models/Account.php` with `kimia_id`, `account_code`, `group_id`, `account_type`, sync fields | REUSE AS-IS | `kimia_id` is the explicit local Kimia identifier field. No fallback is allowed. |
| `User -> Account` relation | `backend/app/Models/User.php`: `account_id` fillable + `belongsTo(Account::class)` | REUSE AS-IS | Structural binding exists. Runtime approval/verification of populated data is not yet proven. |
| `ExternalAccount` Kimia projection | `backend/app/Models/ExternalAccount.php`: `provider`, `external_id`, sync metadata | REUSE AS-IS | Used only as read/reconciliation evidence in this stage. |
| Kimia HTTP read boundary | `backend/app/Clients/KimiaReadClient.php` | REUSE AS-IS | GET-only canonical client; explicit error propagation. |
| Account read repository | `backend/app/Repositories/Kimia/Read/AccountReadRepository.php` from merged recovery PR #150 | REUSE AS-IS | Canonical `Type=3` retail-account query. |
| Balance read repository | `backend/app/Repositories/Kimia/Read/BalanceReadRepository.php` from merged recovery PR #150 | REUSE AS-IS | Reads `/api/voucher/balance/{AccountId}`. |
| Dynamic Coin/Currency reads | `ProductReadRepository` from merged recovery PR #150 | REUSE AS-IS | No hard-coded asset identifiers. |
| Customer dashboard financial read | `CustomerDashboardController` | REUSE AS-IS | Currently fail-closed with HTTP 503 until verified Kimia resolution exists. |
| Customer assets financial read | `CustomerAssetReadController` | REUSE AS-IS | Money/Gold/Coin/Currency all fail closed; no internal Wallet/Ledger values exposed. |
| Authenticated customer account resolver | No dedicated canonical resolver evidenced in reviewed canonical files/history | NOT IMPLEMENTED | Must not be invented from mobile/name/national code/account code/sample IDs. |
| Runtime verified binding semantics | Schema relation exists, but approved runtime population/verification source has not yet been evidenced | BLOCKED BY GROUND TRUTH | Requires runtime evidence or an accepted binding rule; no mutation is allowed here. |
| Tenant/Company/Connector scoping for binding | No verified requirement/mapping established by the reviewed canonical evidence | BLOCKED BY GROUND TRUTH | Do not silently add or redesign scope. Continue inventory before any schema/API change. |
| Read-only reconciliation bridge | PR #196 concept existed on materially diverged branch | REUSE AFTER FIX | Reconstructed narrowly on V2-01; broad merge/cherry-pick remains forbidden. |
| Kimia Write | Deny-by-default foundation exists; production operation registry remains ungrounded | BLOCKED BY GROUND TRUTH | No write activation or payload/action mapping in V2-01. |

## Historical compare

### PR #196

PR #196 is evidence, not an integration source. Its useful behavior was:

- SELECT-only inspection of `accounts`, `external_accounts`, and `users.account_id`;
- conflict classification;
- no auto-fix;
- before/after table snapshots proving zero mutation.

Classification: **REUSE AFTER FIX**.

The V2-01 reconstruction deliberately fixes one unsafe edge in the historical implementation: a missing/null `accounts.kimia_id` must remain unavailable and must never be cast into a fake `0` Kimia AccountId. Such rows are reported as `account_missing_kimia_id`.

### Historical Customer asset implementation

Older Customer asset work temporarily read internal Wallet/Ledger projections. Canonical recovery subsequently established Kimia as the final customer financial balance authority and the current Customer controllers fail closed. The older Ledger-derived customer balance behavior is therefore **SUPERSEDED** and must not be restored.

## Current exact resolution answer

For source structure only, the current chain is:

`request->user()`  
`-> User.account_id`  
`-> Account.id`  
`-> Account.kimia_id`  
`-> BalanceReadRepository::forAccount(kimia AccountId)`

However, V2-01 cannot yet call this a **verified runtime binding** merely because the columns exist. The missing evidence is: who/what populated `users.account_id`, under which approved rule, and whether Tenant/Company/Connector scope is required for uniqueness. Until that is proven, Customer financial endpoints correctly remain fail-closed.

## Reconciliation slice added in V2-01

Files:

- `backend/app/Services/Kimia/CustomerAccountReconciliationService.php`
- `backend/app/Console/Commands/KimiaInspectAccountReconciliation.php`
- `backend/tests/Feature/KimiaInspectAccountReconciliationTest.php`

Behavior:

- read-only only;
- reports matched/local-only/external-only/duplicate/orphan states;
- reports missing Kimia identifiers without converting them to `0`;
- never creates, links, repairs, updates, deletes, or backfills bindings;
- tests snapshot `accounts`, `external_accounts`, and `users` before/after inspection.

Current capability status before exact-head CI: **IMPLEMENTED — NOT TESTED**.

## Migration / DB conclusion for this slice

No migration is added. Existing schema is sufficient for read-only reconciliation. A new binding/scoping migration would be speculative until runtime binding and Tenant/Company/Connector Ground Truth is proven.

## API / OpenAPI conclusion for this slice

No API or OpenAPI change. Existing customer balance endpoints remain fail-closed. Reconciliation is an internal inspection capability only.

## Permission / Audit / Idempotency

- No new customer/admin permission is introduced because this slice exposes no HTTP endpoint.
- No mutation means write idempotency is not applicable.
- The reconciliation output is inspection evidence, not an authoritative balance source and not a repair mechanism.

## Next evidence required

1. Exact-head CI for the reconstructed read-only reconciliation slice.
2. Runtime read-only reconciliation output from an approved environment when available.
3. Evidence of how `users.account_id` is populated/approved in real customer registration or migration flow.
4. Evidence whether Tenant/Company/Connector/Book context participates in customer-to-Kimia identity.
5. Only after those are proven: implement a fail-closed authenticated resolver and connect Customer financial reads to canonical Kimia repositories.
