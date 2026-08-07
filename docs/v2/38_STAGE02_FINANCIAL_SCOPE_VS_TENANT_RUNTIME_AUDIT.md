# GoldPlatform V2 — Stage 02 Financial Scope vs Tenant Runtime Audit

- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Classification: `REUSE AFTER FIX / HISTORICAL EVIDENCE`
- Product behavior change: `NONE`
- Migration applied by this V2 slice: `NONE`
- Kimia Write: `UNCHANGED — BLOCKED BY GROUND TRUTH`

## Purpose

Distinguish the historical Stage 02 `tenant-scoped financial kernel` from the accepted ADR-026 product multi-tenancy runtime, and verify whether either proves a tenant-owned Kimia connector/book implementation.

## Historical Stage 02 evidence

Commit `31d55fac545201c7b436e940e48e9dcd89bd553d` merged a tenant-scoped financial kernel after green CI.

Its `FinancialScope` is an internal value object carrying:

- `tenantId` (required string),
- optional `companyId`,
- optional `branchId`,
- a deterministic scope key.

It rejects an empty tenant identifier and rejects branch scope without company scope.

The Stage 02 financial-kernel migration creates internal tables such as:

- `financial_journals`,
- `financial_events`,
- `financial_idempotency_records`,
- `financial_balance_projections`.

Those internal tables contain string scope columns (`tenant_id`, `company_id`, `branch_id`, `scope_key`) and scope-aware indexes/unique constraints.

## What Stage 02 does NOT prove

The Stage 02 scope columns are not evidence of the ADR-026 Tenant runtime being complete.

They do not establish:

- a foreign key to the `tenants` table;
- `tenant_id` ownership on `users`;
- `tenant_id` ownership on `accounts`;
- `tenant_id` / `connector_id` ownership on `external_accounts`;
- authenticated host/user tenant cross-checking;
- tenant-scoped Kimia credential selection;
- tenant/connector context in `kimia:sync-*` commands;
- a `KimiaConnector` or equivalent connector entity.

Therefore Stage 02 should be read as a tenant-aware internal financial/audit kernel, not as proof of a complete white-label multi-tenant product runtime.

## ADR-026 tenancy foundation evidence

The later `work/product-kimia-next` tenancy checkpoint introduced an actual Tenant root/domain resolution foundation:

- `Tenant`,
- `TenantDomain`,
- host normalization,
- `TenantResolver`,
- request-scoped `TenantContext`,
- `ResolveTenantFromDomain` middleware alias,
- `tenants` and `tenant_domains` migrations,
- negative isolation/domain-resolution tests.

That checkpoint deliberately did not attach the middleware to production routes and deliberately deferred adding tenant ownership to users/accounts/Kimia projections and moving Kimia credentials into connector records.

## Canonical Recovery comparison

The current Recovery canonical does not contain `backend/app/Domain/Financial/ValueObjects/FinancialScope.php` at the inspected path.

A Git history comparison shows Stage 02 and the current Recovery canonical are diverged rather than a simple ancestor/carry-forward line. Therefore the historical Stage 02 implementation cannot be treated as current canonical behavior without a focused recovery/integration decision.

## Kimia connector/book evidence

A historical commit added optional `KIMIA_BOOK_ID` to the global Kimia read-client configuration. This is environment/configuration data, not a tenant-owned connector entity.

Searches for commit text `connector` and `connector_id` did not return an implementation candidate. Because repository code search is not indexed and first-search absence is not authoritative, this remains supporting evidence only, not a repository-wide proof of non-existence.

The inspected tenancy documentation explicitly deferred:

- moving Kimia credentials into connector records;
- carrying Tenant/connector context in sync commands/queues/schedulers;
- adding tenant/connector scope to external projections.

## Classification

### Stage 02 tenant-scoped financial kernel

Status: `HISTORICAL ONLY / REUSE AFTER FIX`

Useful concepts:
- explicit scope object;
- tenant/company/branch-aware idempotency and audit keys;
- no implicit global financial execution scope.

Not reusable as proof of current tenant ownership or Kimia connector resolution.

### ADR-026 Tenant root/domain foundation

Status: `REUSE AFTER FIX`

It is stronger product-tenancy evidence than Stage 02 because it creates actual Tenant/Domain runtime primitives and fail-closed resolution tests, but it remains an intentionally bounded checkpoint.

### Tenant-owned Kimia connector/book model

Status: `NOT VERIFIED — CONTINUE RECOVERY`

Current verified evidence contains a global optional `KIMIA_BOOK_ID`, not a tenant-owned connector record.

## Safety boundary

Do not:

- map a `FinancialScope.tenantId` string directly to a live Tenant without verification;
- restore Stage 02 financial migrations blindly;
- activate historical tenant middleware on current production routes before users/accounts are tenant-owned;
- convert global Kimia env credentials/book id into a tenant connector without an explicit credential migration design;
- change global Kimia unique constraints without duplicate/preflight evidence;
- auto-link users/accounts/external_accounts.

## Next safe recovery work

1. inspect commits/branches after the tenancy checkpoint for any tenant-owned integration/connector schema under names other than `connector`;
2. inspect `services.php`, Kimia client construction, providers and command dependency injection for evidence of connector selection abstractions;
3. inventory historical `tenant_id` migrations for users/accounts/Kimia projections and determine whether any were prepared after Checkpoint 1;
4. verify current Recovery canonical equivalents before proposing carry-forward;
5. keep Customer financial routes fail-closed until a verified Tenant -> Connector/Book -> User/Account -> Kimia AccountId resolution chain exists.
