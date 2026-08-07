# GoldPlatform V2-01 — Tenancy Source Recovery & Bounded Foundation

**Stage:** V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification  
**Working branch:** `v2/v2-01-canonical-runtime-integration`

## Result first

The previously referenced ADR-024 / ADR-026 evidence has now been recovered from preserved Project Memory and the historical GitHub branch `work/product-kimia-next`.

The historical branch is **HISTORICAL ONLY** as a branch-level integration source because it is materially diverged from canonical (`6` commits ahead and `577` behind). No broad merge or cherry-pick is allowed.

The accepted ADR-026 bounded checkpoint was reconstructed file-by-file on the current V2 branch after compatibility inspection. ADR-024 identity immutability guards were subsequently reconstructed as a separate narrow slice without applying the historical unique-index migration.

## Recovered Ground Truth

### ADR-024 — Platform User to Kimia Account Binding

Accepted rules include:

- one GoldPlatform login/account -> no more than one local Account -> no more than one Kimia AccountId;
- one Kimia AccountId -> no more than one GoldPlatform login/account;
- established Kimia AccountId is unique and immutable;
- mobile is unique inside a Tenant in the target tenancy model;
- national code may be reused across independent accounts;
- account_code is display/search data and never replaces AccountId.

Canonical and historical `RegistrationService` inspected so far still retain `Create Kimia Account / Link Account / Assign Default Group` as TODO and do not populate `users.account_id`. No implemented approved linking workflow has yet been recovered.

### ADR-026 — Multi-tenancy Isolation Strategy

Accepted architecture:

- shared database / shared schema;
- mandatory tenant ownership for tenant-owned data;
- Khalifeh Coin is the first explicit pilot tenant, never an implicit fallback;
- public tenant resolution from verified active domain/subdomain;
- authenticated requests must later cross-check resolved Tenant against authenticated user's Tenant;
- one active Kimia connector/book per Tenant in first release;
- Platform Super Admin is separate from Tenant Admin/Operator;
- table-group migrations only; no all-table migration;
- no unique-index replacement without duplicate preflight.

The ADR explicitly authorizes the first bounded checkpoint as Tenant root + verified domain + explicit context/resolver + isolation tests, without attaching tenancy to production business routes or migrating all business tables.

## Historical implementation compare

Historical branch: `work/product-kimia-next`  
Compare to canonical `recovery/rc2-product-rebuild`: **DIVERGED — 6 ahead / 577 behind**.

Classification of the entire branch: **HISTORICAL ONLY**.

Reusable bounded files were inspected individually before reconstruction.

## Reconstructed bounded Tenant foundation

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

- `AppServiceProvider` preserves current rate-limit/observability behavior and adds only scoped `TenantContext` registration.
- `bootstrap/app.php` preserves current RequestContext, SecurityHeaders, Idempotency, Role and Permission middleware and adds only the inactive `tenant.resolve` alias.

Runtime properties:

- Host normalization handles case/port/trailing-dot.
- Resolver accepts only active + verified TenantDomain with active Tenant.
- Unknown/unverified/inactive domain fails closed; no Khalifeh Coin fallback.
- TenantContext cannot switch inside one execution scope.
- `tenant.resolve` is not attached to current production Customer/Admin/Operator/Kimia routes by this slice.

## Exact CI evidence for Tenant recovery

Head `2d15d25cc9cbac9e0061c0f41bd327ff9547f036`:

- Operational Readiness #69 — **EXECUTED — PASS**
- Backend RC1 Validation #459 — **EXECUTED — FAIL**

The CI log showed:

- migration fresh PASS, including `tenants` and `tenant_domains`;
- Unit suite PASS: 66 tests / 357 assertions;
- Feature suite: 86 PASS and one failure;
- four of five TenantDomainResolution tests PASS;
- only the middleware HTTP test failed because the historical test injected `Host` as a request header while the current Laravel test request host is sourced from server variables.

No resolver, migration, isolation, financial or Tenant-rule failure was evidenced.

Test-only compatibility fix commit: `2128bd42e0829e5d182115150261b62686bef3f9`, replacing `withHeader('Host', ...)` with explicit `HTTP_HOST` server variables. Exact-head CI is required before closing this fix.

## ADR-024 identity immutability slice

Recovered after current consumer comparison:

- `Account.kimia_id` cannot be changed after creation/synchronization;
- `ExternalAccount.provider` / `external_id` cannot be changed after synchronization;
- `User.account_id` may be populated from null once, but an established non-null binding cannot be changed or removed.

The current account sync tests update mutable ExternalAccount fields while preserving `(provider, external_id)`, so the identity guard does not block the evidenced valid sync path.

Focused test added:

- `backend/tests/Feature/KimiaIdentityImmutabilityTest.php`

This test intentionally excludes historical unique-index and national-code constraint tests because those migrations are not activated in this slice.

Current identity-guard status: **IMPLEMENTED — NOT TESTED** on latest exact head until CI completes.

## Explicitly not included

- no `users.tenant_id` migration or backfill;
- no Product/Order/Wallet/Ledger/Custody tenant migration;
- no tenant-specific Kimia credential storage;
- no connector/book credential movement;
- no authenticated user/Tenant cross-check activation yet;
- no Customer financial resolver activation;
- no unique `users.account_id` migration application;
- no Kimia Write.

## Historical unique-binding migration

Historical `2026_08_03_120100_enforce_unique_user_account_binding.php` performs duplicate non-null preflight before adding unique `users.account_id`.

It remains **HISTORICAL ONLY / REUSE AFTER FIX candidate** and is not applied until current runtime duplicate/orphan evidence and tenant-aware migration sequencing are approved.

## Current classifications

- ADR-024 rule: **REUSE AS-IS**.
- ADR-026 decisions: **REUSE AS-IS**.
- Historical `work/product-kimia-next` branch: **HISTORICAL ONLY**.
- Bounded Tenant foundation: **IMPLEMENTED — NOT TESTED** on latest head pending exact-head CI.
- ADR-024 immutability guards: **IMPLEMENTED — NOT TESTED** pending exact-head CI.
- Historical unique-binding migration: **HISTORICAL ONLY**, candidate **REUSE AFTER FIX**.
- Registration -> approved `users.account_id` workflow: **NOT IMPLEMENTED**.
- Authenticated Customer -> Kimia resolver: **NOT IMPLEMENTED**.
- Tenant -> Kimia connector/book runtime implementation: **NOT IMPLEMENTED**.
- Kimia Write: **BLOCKED BY GROUND TRUTH**.
