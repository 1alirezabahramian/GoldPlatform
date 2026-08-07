# V2-00 — Tenancy + Kimia Migration Preflight Design

Status: DESIGN ONLY — NO MIGRATION EXECUTED

## Purpose
Define the safest bounded migration sequence for the accepted V2 recovery candidate without changing runtime data, Kimia writes, customer balances, API contracts, or current tenant behavior.

## Grounded constraints
- ADR-026 requires shared database/shared schema with mandatory tenant ownership.
- Kimia sync commands must eventually carry explicit tenant + connector identity.
- External Kimia identifiers must become tenant/connector scoped.
- No all-table migration is allowed.
- Existing customer binding remains User -> Account -> Kimia AccountId for the current release.
- Current external_accounts uniqueness is global on (provider, external_id).
- Existing user/account one-to-one enforcement must fail closed when duplicates exist.

## Safe bounded sequence

### Checkpoint A — Observe only
Before writing any migration or backfill, capture counts and duplicate/orphan evidence for:
- tenants / tenant_domains if present in the target runtime
- users
- users with account_id null / non-null
- duplicate non-null users.account_id
- accounts
- duplicate accounts.kimia_id
- external_accounts where provider = kimia
- duplicate external_accounts external_id within provider=kimia
- accounts without a matching Kimia external identity
- Kimia external identities without an account row

No matching by mobile, national code, name, or account_code is allowed to repair these gaps automatically.

### Checkpoint B — Tenant root only
Create/verify Tenant root + verified domains as a bounded table group. Register the pilot tenant explicitly; never use a hidden fallback tenant.

Gate before continuation:
- exactly expected pilot tenant rows
- no duplicate normalized host
- unknown/unverified/inactive domains fail closed

### Checkpoint C — Connector metadata contract
Introduce a tenant-owned connector/book record only after its exact schema/security contract is reviewed. Required minimum identity shape:
- tenant_id
- provider = kimia
- logical connector/book identity
- active state
- one active connector per tenant in first release

Credentials must remain secret-managed; no credentials in logs, UI, docs, or plaintext database fields without an approved secure storage mechanism.

This checkpoint does not enable Kimia Write.

### Checkpoint D — Add nullable ownership columns, one table group at a time
Recommended first bounded group:
1. users.tenant_id nullable
2. accounts.tenant_id nullable
3. external_accounts.tenant_id nullable
4. external_accounts.connector_id nullable

Do not remove global unique indexes yet.

Gate:
- migration fresh tests
- existing rows unchanged except new nullable columns
- zero unexpected deletes/updates

### Checkpoint E — Pilot tenant backfill
Backfill only rows whose ownership is already established by the current single-tenant deployment evidence.

Backfill must be deterministic and auditable.

Required pre/post counters:
- table row count before == after
- count(tenant_id is null) decreases by exactly expected amount
- no account_id changes
- no kimia_id changes
- no external_id changes
- no balance or ledger mutation

Do not infer account linkage from personal identity fields.

### Checkpoint F — Account ↔ ExternalAccount reconciliation evidence
Build a read-only reconciliation report using durable Kimia AccountId identity.

Expected classes:
- exact match: accounts.kimia_id == external_accounts.external_id for provider=kimia and same tenant/connector
- account-only
- external-only
- duplicate/conflict

Only exact durable-ID matches may become candidates for an explicit reconciliation write in a later reviewed step.

### Checkpoint G — Scoped uniqueness preflight
Before replacing indexes, prove zero conflicts for target constraints:
- users: unique (tenant_id, mobile)
- accounts: unique (tenant_id, kimia_id)
- external_accounts: unique (tenant_id, connector_id, external_id)

Also prove one-to-one user/account binding:
- duplicate non-null users.account_id = 0

If any conflict exists: STOP. Do not auto-resolve.

### Checkpoint H — Replace uniqueness only after green preflight
Only after the exact duplicate reports are zero may the global constraints be replaced with the approved tenant/connector scoped constraints.

Every constraint change must be its own reversible migration where practical.

### Checkpoint I — Make ownership mandatory
Make tenant_id / connector_id non-null only after:
- all existing rows are backfilled
- orphan count = 0
- cross-tenant isolation tests pass
- explicit command/job context tests pass
- rollback strategy is documented

### Checkpoint J — Activate runtime context last
Only after data ownership is proven:
- activate tenant.resolve on relevant routes
- enforce authenticated user/tenant cross-check
- require explicit tenant + connector for Kimia sync commands/jobs
- construct Kimia Read clients from resolved connector context

Customer financial reads remain fail-closed until authenticated Customer -> verified Account -> tenant connector -> Kimia AccountId resolution is proven.

## Non-negotiable preflight queries/checks
Conceptually required checks (implementation syntax to be written later):
- duplicate users.account_id where non-null
- duplicate users.mobile grouped by future tenant
- duplicate accounts.kimia_id grouped by future tenant
- duplicate external_accounts.external_id grouped by future tenant + connector
- user.account_id pointing to missing account
- Account without expected owner tenant
- ExternalAccount without expected tenant/connector
- exact AccountId reconciliation counts
- before/after row-count equality for every backfill migration

## Explicitly forbidden shortcuts
- no backfill by mobile/name/national_code/account_code guess
- no default tenant fallback in runtime
- no blind reassignment of rows to pilot tenant without runtime ownership evidence
- no all-table migration
- no dropping global unique indexes before duplicate preflight
- no direct User -> ExternalAccount replacement
- no Kimia Write activation
- no customer balance fallback to Wallet/Ledger/zero

## Current classification
- Tenant root/domain foundation: REUSE AFTER FIX
- Tenant connector/book runtime: REBUILD / NOT IMPLEMENTED IN CURRENT VERIFIED CANONICAL PATH
- User/Account tenant ownership: REFACTOR
- ExternalAccount tenant/connector ownership: REFACTOR
- User -> Account durable binding: REUSE AFTER FIX
- Account/ExternalAccount reconciliation: NOT IMPLEMENTED — DESIGN READY FOR READ-ONLY PRECHECK

## V2-00 boundary
This document authorizes no migration execution. The next safe step is to recover or construct a READ-ONLY preflight/reconciliation specification and prove which runtime evidence already exists before any schema change proposal is converted into executable code.
