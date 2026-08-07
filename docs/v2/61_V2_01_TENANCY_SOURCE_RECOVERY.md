# GoldPlatform V2-01 — Tenancy Source Recovery & Bounded Foundation

**Stage:** V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification  
**Working branch:** `v2/v2-01-canonical-runtime-integration`

## Result first

The previously referenced ADR-024 / ADR-026 evidence has now been recovered from two independent preserved sources:

1. File Library Project Memory snapshot dated 2026-08-03, marked Accepted / Living Ground Truth.
2. Historical GitHub branch `work/product-kimia-next`, which contains the exact ADRs and the prepared bounded implementation.

The historical branch is **HISTORICAL ONLY** as an integration source because it is materially diverged from canonical (`6` commits ahead and `577` behind). No broad merge or cherry-pick is allowed.

The accepted ADR-026 bounded checkpoint has therefore been reconstructed file-by-file on the current V2 branch after compatibility inspection.

## Recovered Ground Truth

### ADR-024 — Platform User to Kimia Account Binding

Accepted rules include:

- one GoldPlatform login/account -> no more than one local Account -> no more than one Kimia AccountId;
- one Kimia AccountId -> no more than one GoldPlatform login/account;
- established Kimia AccountId is unique and immutable;
- mobile is unique inside a Tenant in the target tenancy model;
- national code may be reused across independent accounts;
- account_code is display/search data and never replaces AccountId;
- a second account for the same real customer inside one Tenant uses a second mobile and second Kimia AccountId.

Canonical runtime still does not implement the approved workflow that populates `users.account_id`.

### ADR-026 — Multi-tenancy Isolation Strategy

Accepted architecture:

- shared database / shared schema;
- mandatory tenant ownership for tenant-owned data;
- Khalifeh Coin is the first explicit pilot tenant, never an implicit fallback;
- public tenant resolution from verified active domain/subdomain;
- authenticated requests must later cross-check resolved Tenant against authenticated user's Tenant;
- one active Kimia connector/book per Tenant in first release, while keeping model boundaries extensible;
- Platform Super Admin is separate from Tenant Admin/Operator;
- table-group migrations only; no all-table migration;
- no unique-index replacement without duplicate preflight.

The ADR explicitly authorizes the first bounded checkpoint as Tenant root + verified domain + explicit context/resolver + isolation tests, without attaching tenancy to production business routes or migrating all business tables.

## Historical implementation compare

Historical branch: `work/product-kimia-next`  
Compare to canonical `recovery/rc2-product-rebuild`: **DIVERGED — 6 ahead / 577 behind**.

Classification of the entire branch: **HISTORICAL ONLY**.

Reusable bounded files were inspected individually before reconstruction.

## Reconstructed bounded foundation

Files recovered on the current V2 branch:

- `backend/app/Models/Tenant.php`
- `backend/app/Models/TenantDomain.php`
- `backend/app/Tenancy/TenantHost.php`
- `backend/app/Tenancy/TenantContext.php`
- `backend/app/Tenancy/TenantResolver.php`
- `backend/app/Http/Middleware/ResolveTenantFromDomain.php`
- `backend/database/migrations/2026_08_03_130000_create_tenants_table.php`
- `backend/database/migrations/2026_08_03_130100_create_tenant_domains_table.php`
- `backend/tests/Feature/TenantDomainResolutionTest.php`

Canonical integration adjustments:

- `AppServiceProvider` keeps all current rate-limit/observability behavior and adds only scoped `TenantContext` registration.
- `bootstrap/app.php` keeps current RequestContext, SecurityHeaders, Idempotency, Role and Permission middleware and adds only the inactive `tenant.resolve` alias.

## Runtime behavior

The resolver:

- normalizes host case/port/trailing-dot;
- resolves only active + verified TenantDomain;
- requires active Tenant;
- returns null on unknown/unverified/inactive domain;
- never falls back to Khalifeh Coin;
- TenantContext cannot switch to another Tenant inside one execution scope.

The middleware alias exists but is **not attached to current production Customer/Admin/Operator/Kimia routes by this slice**.

## Explicitly not included

- no `users.tenant_id` migration or backfill;
- no Product/Order/Wallet/Ledger/Custody tenant migration;
- no tenant-specific Kimia credential storage;
- no connector/book model or credential movement;
- no authenticated user/Tenant cross-check activation yet;
- no Customer financial resolver activation;
- no unique `users.account_id` migration application;
- no Kimia Write.

## Binding enforcement historical source

Historical migration `2026_08_03_120100_enforce_unique_user_account_binding.php` performs a read-only duplicate preflight before adding the nullable unique `users.account_id` index. ADR-024 also records prepared immutability guards.

These are preserved as **HISTORICAL ONLY / REUSE AFTER FIX candidates**. They are not reconstructed in this bounded Tenant slice because applying identity constraints requires current runtime duplicate/orphan evidence and a separately controlled identity checkpoint.

## Current classifications

- ADR-024 business/architecture rule: **REUSE AS-IS**.
- ADR-026 architecture decisions: **REUSE AS-IS**.
- Historical `work/product-kimia-next` branch: **HISTORICAL ONLY**.
- Bounded Tenant root/domain/context/resolver reconstruction: **IMPLEMENTED — NOT TESTED** until exact-head CI.
- Historical unique-binding migration/immutability guards: **HISTORICAL ONLY**, candidate **REUSE AFTER FIX**.
- Registration -> approved `users.account_id` workflow: **NOT IMPLEMENTED**.
- Authenticated Customer -> Kimia resolver: **NOT IMPLEMENTED**.
- Tenant -> Kimia connector/book runtime implementation: **NOT IMPLEMENTED**; exact credential/config model remains deferred by ADR-026.
- Kimia Write: **BLOCKED BY GROUND TRUTH**.

## Validation gate

Before this recovery slice, exact head `245875155793e20258f460018efb3ad4a94c3207` passed:

- Backend RC1 Validation #444 — EXECUTED — PASS
- Operational Readiness #54 — EXECUTED — PASS

The new Tenant foundation must receive its own exact-head CI before its status can become TESTED — NOT MERGED.
