# GoldPlatform V2-01 — Tenancy Source Recovery & Bounded Foundation

**Stage:** V2-01 — Canonical Runtime Integration & Customer↔Kimia Binding Verification  
**Working branch:** `v2/v2-01-canonical-runtime-integration`

## Result first

ADR-024 / ADR-026 evidence has been recovered from preserved Project Memory and historical GitHub branch `work/product-kimia-next`.

The historical branch remains **HISTORICAL ONLY** as a branch-level integration source because it is materially diverged from canonical (`6` commits ahead and `577` behind). No broad merge or cherry-pick is allowed.

The accepted ADR-026 bounded Tenant foundation and ADR-024 identity immutability guards have been reconstructed narrowly on the current V2 branch and are now **TESTED — NOT MERGED** on exact-head CI.

A separate read-only `users` tenancy/account-binding preflight has also been implemented and tested. It reports current schema/data readiness but never creates `tenant_id`, assigns a Tenant, repairs duplicate account bindings, or infers Customer→Account links.

## Recovered Ground Truth

### ADR-024 — Platform User to Kimia Account Binding

Accepted rules include:

- one GoldPlatform login/account -> no more than one local Account -> no more than one Kimia AccountId;
- one Kimia AccountId -> no more than one GoldPlatform login/account;
- established Kimia AccountId is unique and immutable;
- mobile is unique inside a Tenant in the target tenancy model;
- national code may be reused across independent accounts;
- `account_code` is display/search data and never replaces AccountId.

Canonical and historical `RegistrationService` still retain `Create Kimia Account / Link Account / Assign Default Group` as TODO and do not populate `users.account_id`. No implemented approved linking workflow has been recovered. No linking may be inferred from mobile, name, national code, account code, first record, sample IDs, or zero/default IDs.

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

Historical tenancy foundation explicitly deferred `users.tenant_id`, user backfill, authenticated Tenant activation, tenant-scoped mobile uniqueness, and all existing business-table ownership changes to later separately reviewed checkpoints.

## Historical implementation compare

Historical branch: `work/product-kimia-next`  
Compare to canonical `recovery/rc2-product-rebuild`: **DIVERGED — 6 ahead / 577 behind**.

Classification of the entire branch: **HISTORICAL ONLY**.

No historical `users.tenant_id` migration/backfill implementation was recovered from that bounded checkpoint; the documentation states it was intentionally deferred.

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

Runtime properties:

- Host normalization handles case/port/trailing-dot.
- Resolver accepts only active + verified TenantDomain with active Tenant.
- Unknown/unverified/inactive domain fails closed; no Khalifeh Coin fallback.
- TenantContext cannot switch inside one execution scope.
- `tenant.resolve` is registered but remains unattached to current production Customer/Admin/Operator/Kimia routes.

Status: **TESTED — NOT MERGED**.

## ADR-024 identity immutability slice

Recovered narrowly:

- `Account.kimia_id` cannot be changed after creation/synchronization;
- `ExternalAccount.provider` / `external_id` cannot be changed after synchronization;
- `User.account_id` may be populated from null once, but an established non-null binding cannot be changed or removed.

Focused test:

- `backend/tests/Feature/KimiaIdentityImmutabilityTest.php`

Status: **TESTED — NOT MERGED**.

The historical unique `users.account_id` migration is not applied.

## Read-only users tenancy/binding preflight

Implemented:

- `backend/app/Services/Tenancy/UserTenancyBindingPreflightService.php`
- `backend/app/Console/Commands/InspectUserTenancyBindingReadiness.php`
- `backend/tests/Feature/UserTenancyBindingPreflightTest.php`

The preflight reports:

- whether `users` exists;
- whether `users.tenant_id` exists;
- total / linked / unlinked users;
- duplicate non-null `users.account_id` bindings;
- users missing explicit Tenant assignment;
- whether unique-account-binding preflight passes;
- whether authenticated tenancy activation is structurally ready.

Safety properties:

- read-only DB access;
- no insert/update/delete/backfill;
- no duplicate repair;
- no Tenant inference;
- no Account/Kimia binding inference.

Current canonical schema evidence:

- `users.tenant_id` does not exist;
- `users.mobile` is still globally unique;
- `users.account_id` is nullable and not unique;
- therefore authenticated Tenant activation is not yet structurally ready.

Status: **TESTED — NOT MERGED**.

## CI evidence

### Prior Tenant test-harness failures

Earlier runs #459 and #465 failed only on the Tenant middleware HTTP test harness while migrations, resolver behavior, and other Tenant tests passed. The test was ultimately rewritten to exercise the Middleware with an actual `Illuminate\Http\Request::create()` host rather than relying on dynamic Router test host injection.

Exact head `4d0208fce93bb19aae015096415fb5584aa530bc`:

- Operational Readiness #76 — **EXECUTED — PASS**
- Backend RC1 Validation #466 — **EXECUTED — PASS**

This promoted the bounded Tenant foundation and ADR-024 immutability guards to **TESTED — NOT MERGED**.

### Users preflight CI

Head `51999217665591f948df5951cdf39c71141fff4a`:

- Operational Readiness #79 — **EXECUTED — PASS**
- Backend RC1 Validation #469 — **EXECUTED — FAIL**

#469 evidence:

- migration fresh PASS;
- Unit: 66 PASS / 359 assertions;
- Feature: 91 PASS / 1 FAIL;
- the two service/zero-mutation preflight tests PASS;
- only the command JSON assertion failed because it matched pretty-printed output as a spacing-sensitive string.

No service, query, schema, tenancy, financial, Kimia, or mutation failure was evidenced.

Test-only fix commit `8b1dcd2b0545088b79de161fcea90d252a68f7c5` changed only the command-output test to parse JSON structurally using `Artisan::call()` / `Artisan::output()` and `json_decode()`.

Exact head `8b1dcd2b0545088b79de161fcea90d252a68f7c5`:

- Operational Readiness #80 — **EXECUTED — PASS**
- Backend RC1 Validation #470 — **EXECUTED — PASS**
- Migration Fresh — PASS
- Unit Tests — PASS
- Feature Tests — PASS
- Financial/Ledger — PASS
- Order Lifecycle — PASS
- Trade Idempotency/Settlement — PASS
- Custody/Delivery — PASS
- Permission — PASS
- Kimia Mock — PASS
- Kimia Read-Only Integration Contract — PASS
- Full Regression — PASS
- Laravel Health — PASS
- Docker Compose Validation — PASS
- Secret Scan — PASS

The users tenancy/binding preflight is therefore **TESTED — NOT MERGED**.

## Next bounded checkpoint constraints

Do not combine these operations into one migration/checkpoint:

1. adding nullable `users.tenant_id`;
2. assigning/backfilling users to a Tenant;
3. replacing global `users.mobile` uniqueness with tenant-scoped uniqueness;
4. enforcing unique `users.account_id`;
5. implementing Registration→Account binding;
6. activating Host Tenant ↔ authenticated User Tenant cross-check.

Each requires its own evidence, preflight, rollback/reversibility and CI closure.

The accepted architecture supports a future nullable `users.tenant_id` schema checkpoint, but **does not provide a ground-truth user→Tenant backfill mapping**. No automatic backfill or default Khalifeh assignment is authorized by current evidence.

## Explicitly not included

- no `users.tenant_id` migration/backfill yet;
- no Product/Order/Wallet/Ledger/Custody tenant migration;
- no tenant-specific Kimia credential storage;
- no connector/book credential movement;
- no authenticated user/Tenant cross-check activation yet;
- no Customer financial resolver activation;
- no unique `users.account_id` migration application;
- no Registration→Account linking implementation;
- no Kimia Write.

## Current classifications

- ADR-024 rule: **REUSE AS-IS**.
- ADR-026 decisions: **REUSE AS-IS**.
- Historical `work/product-kimia-next` branch: **HISTORICAL ONLY**.
- Read-only reconciliation: **TESTED — NOT MERGED**.
- Bounded Tenant foundation: **TESTED — NOT MERGED**.
- ADR-024 immutability guards: **TESTED — NOT MERGED**.
- Users tenancy/binding preflight: **TESTED — NOT MERGED**.
- Historical unique-binding migration: **HISTORICAL ONLY**, candidate **REUSE AFTER FIX**.
- `users.tenant_id` migration/backfill: **NOT IMPLEMENTED**.
- Registration -> approved `users.account_id` workflow: **NOT IMPLEMENTED**.
- Authenticated Host Tenant ↔ User Tenant cross-check: **NOT IMPLEMENTED**.
- Authenticated Customer -> Kimia resolver: **NOT IMPLEMENTED**.
- Tenant -> Kimia connector/book runtime implementation: **NOT IMPLEMENTED**.
- Customer financial fail-closed behavior: **REUSE AS-IS**.
- Kimia Write: **BLOCKED BY GROUND TRUTH**.
