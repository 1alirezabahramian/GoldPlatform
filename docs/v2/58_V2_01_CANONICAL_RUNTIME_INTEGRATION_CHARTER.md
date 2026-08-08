# GoldPlatform V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification

**Status:** IN PROGRESS — RECONCILIATION TESTED — TENANT FOUNDATION RECOVERED / CI PENDING  
**Base branch:** `recovery/rc2-product-rebuild`  
**Base exact SHA:** `d9ee5fee69969fa02ac25c96d8e1653143ba413b`  
**Working branch:** `v2/v2-01-canonical-runtime-integration`

## Outcome

Establish a canonical, read-only, fail-closed path from an authenticated GoldPlatform customer to the exact Kimia account used for Money/Gold/Coin/Currency reads, without creating a competing balance source.

## Source-of-truth boundaries

1. Kimia is final authority for Money/Gold/Coin/Currency.
2. GoldPlatform is final authority for physical Custody/Amanat.
3. Ledger/Projection/Reservation remain audit/workflow/reconciliation aids only.
4. Kimia Write remains deny-by-default.
5. No binding may be inferred from mobile/name/national code/account code/sample/first/zero ID.

## Recovered accepted Ground Truth

Source recovery located the Accepted Project Memory snapshot and exact historical ADR files on `work/product-kimia-next`.

### ADR-024

- one GoldPlatform login/account -> zero or one local Account -> zero or one Kimia AccountId;
- one Kimia AccountId -> no more than one platform login/account;
- established AccountId binding is unique and immutable;
- mobile uniqueness target is inside Tenant;
- national code may repeat across independent accounts;
- account_code never replaces AccountId.

### ADR-026

- shared database/shared schema with mandatory tenant ownership;
- verified active domain/subdomain is the public Tenant source;
- authenticated requests must later cross-check resolved Tenant with user's Tenant;
- one active Kimia connector/book per Tenant in first release;
- Platform Super Admin separate from Tenant Admin/Operator;
- no all-table migration; table-group migration with preflight only.

The historical branch is 6 commits ahead and 577 behind canonical, so it is **HISTORICAL ONLY** as a branch-level integration source. No broad merge/cherry-pick is permitted.

## Canonical findings

- `accounts.kimia_id`: non-null + unique — **REUSE AS-IS**.
- `users.account_id`: nullable FK, no unique index — **REUSE AS-IS** as current schema, enforcement incomplete.
- Registration does not assign/approve `users.account_id` and retains Kimia-link TODO — **NOT IMPLEMENTED**.
- RegistrationService + active UserObserver both create Wallet/default accounts — **DUPLICATE CANDIDATE**.
- Customer financial controllers correctly return `KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED` until verified resolution — **REUSE AS-IS**.
- Canonical Kimia Read repositories from merged PR #150 — **REUSE AS-IS**.

## Implemented V2-01 slices

### Read-only reconciliation

- `CustomerAccountReconciliationService`
- `kimia:inspect-account-reconciliation`
- zero-mutation/duplicate-binding tests

Status: **TESTED — NOT MERGED**.

Exact head `245875155793e20258f460018efb3ad4a94c3207`:

- Backend RC1 Validation #444 — EXECUTED — PASS
- Operational Readiness #54 — EXECUTED — PASS

### Bounded Tenant foundation

Recovered file-by-file from the accepted ADR-026 historical checkpoint:

- Tenant / TenantDomain models;
- TenantHost / TenantContext / TenantResolver;
- fail-closed ResolveTenantFromDomain middleware;
- additive `tenants` / `tenant_domains` migrations;
- middleware alias only, not attached to production routes;
- Tenant domain isolation tests.

Status: **IMPLEMENTED — NOT TESTED** until exact-head CI for the new recovery commits.

Explicitly excluded:

- no `users.tenant_id` migration/backfill;
- no tenant migration of business tables;
- no connector credential movement;
- no Customer resolver activation;
- no binding unique-index migration application;
- no Kimia Write.

## Target resolution chain

`Authenticated Customer -> verified Tenant context (when authenticated tenancy is activated) -> approved users.account_id binding -> exact Account.kimia_id -> canonical Kimia Read -> reconciliation metadata -> customer-safe financial presentation`

Every link must be evidenced. Missing/ambiguous links fail closed.

## Remaining V2-01 blockers

1. Approved runtime workflow that populates `users.account_id` is still **NOT IMPLEMENTED**.
2. `users.tenant_id` and authenticated user/Tenant cross-check are not yet active.
3. Historical unique-binding/immutability implementation is preserved but requires current duplicate/orphan preflight before controlled recovery.
4. Tenant->Kimia connector/book credential/config implementation was explicitly deferred by ADR-026.
5. Customer financial resolver remains **NOT IMPLEMENTED** until the above prerequisites are proven.

## Exit criteria

V2-01 closes only after applicable resolver/binding/Tenant prerequisites are implemented, exact-head tests/CI pass, Customer Money/Gold/Coin/Currency read only from Kimia, conflicts fail closed, and documentation/traceability are synchronized.
