# GoldPlatform V2 — Tenant Ownership and Kimia Connector Runtime Gap

- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Classification: `REUSE AFTER FIX / NOT IMPLEMENTED IN CURRENT VERIFIED CANONICAL PATH`
- Product behavior change: `NONE`
- Kimia Write: `UNCHANGED — BLOCKED BY GROUND TRUTH`

## Purpose

Close the recovery question of whether the accepted ADR-026 tenant runtime and one-active-Kimia-connector-per-tenant contract were carried into the current Recovery canonical for `users`, `accounts`, `external_accounts`, and Kimia client construction.

## Verified historical boundary

The historical `work/product-kimia-next` branch prepared Tenant Checkpoint 1 only:

- `tenants`
- `tenant_domains`
- Tenant / TenantDomain models
- host normalization and verified-domain resolver
- request-scoped TenantContext
- inactive `tenant.resolve` middleware alias
- negative tenant-domain isolation tests

That checkpoint explicitly deferred adding tenant ownership to existing business tables and explicitly deferred moving Kimia credentials into connector records or carrying tenant/connector context through sync commands, queues, and schedules.

Stage 02 later introduced tenant-aware `FinancialScope` values and tenant/company/branch columns inside internal financial-kernel tables. That work scoped journal/event/idempotency/balance-projection infrastructure; it did not establish tenant ownership on customer/Kimia identity tables and did not create a tenant-owned Kimia connector entity.

## Current Recovery canonical evidence

### User

Current `User` contains `account_id` but no `tenant_id` field in its model contract. `User::account()` still targets `Account`.

### Account

Current `Account` contains `kimia_id`, account metadata and `User` relation, but no tenant or connector ownership field in its model contract.

### ExternalAccount

Current `ExternalAccount` contains provider/external identity and synchronized metadata, but no tenant, connector, account, or user ownership field in its model contract.

### Kimia configuration

Current `backend/config/services.php` defines one process-wide `services.kimia` configuration sourced from environment variables:

- base URL
- username
- password
- timeout/read-only/read-retry policy

There is no tenant selector or connector identifier in the current canonical configuration shape.

### KimiaClient construction

Current `KimiaClient::__construct()` directly reads the process-wide `services.kimia.*` values. Request `TenantContext`, tenant id, connector id, or book entity is not an input to client construction.

Therefore the current canonical runtime has one implicit/global Kimia connection configuration, not the accepted tenant-owned connector runtime.

## Historical book-id evidence

Historical commit `81518ad0aa56e560694f5a9365331ea13581e815` added optional `KIMIA_BOOK_ID` to the global read-client config. This is evidence that a book identifier was recognized in configuration, but it is not a connector entity, tenant ownership model, credential store, or runtime connector resolver.

## Recovery search boundary

Commit/code searches for later `tenant_id` additions to `users/accounts/external_accounts` and for connector-named implementations returned no usable results. The repository code-search index is not reliable enough for absence claims, so those empty searches are not treated as proof by themselves.

The stronger evidence is structural:

1. historical Checkpoint 1 explicitly deferred the business-table and connector migrations;
2. the historical branch delta shows the Tenant root/domain migrations but no recovered later tenant-ownership migration for the three identity tables;
3. current canonical models still expose no tenant/connector ownership;
4. current canonical Kimia client is built from global environment configuration.

## Classification

### Tenant root/domain foundation

`REUSE AFTER FIX`

Useful historical implementation exists, but it is not current canonical and must be reconciled with later Recovery code before reuse.

### Stage 02 FinancialScope

`HISTORICAL ONLY / REUSE CONCEPT AFTER REVIEW`

It provides a valid tenant-aware internal financial scope pattern, but it is not proof of product tenancy and must never make internal projections a customer balance authority.

### Tenant ownership for User / Account / ExternalAccount

`NOT IMPLEMENTED IN CURRENT VERIFIED CANONICAL PATH`

No current verified ownership field or relation connects these records to a Tenant.

### Tenant-owned Kimia connector/book runtime

`NOT IMPLEMENTED IN CURRENT VERIFIED CANONICAL PATH`

The accepted first-release contract is one active connector/book per tenant, but current Kimia runtime configuration remains global.

### Global Kimia identifiers and uniqueness

`REFACTOR`

Global Kimia identifiers cannot be assumed safe across independent Kimia installations. Any future uniqueness migration must follow tenant/connector scope and duplicate/orphan preflight; no index is changed during V2-00 recovery.

## Impact on customer binding

A safe authenticated customer-to-Kimia resolver cannot be completed by simply linking `User` to `ExternalAccount` or restoring the old `accounts` sync.

The resolver eventually needs a trusted chain equivalent to:

`resolved tenant -> approved active Kimia connector/book -> verified customer binding -> Kimia AccountId`

The exact persistent model is not selected here. Automatic matching by mobile, national code, name, or account code remains prohibited.

## Safety boundary

Until tenant ownership and connector runtime are reviewed and implemented:

- customer Money/Gold/Coin/Currency reads remain fail-closed where binding is unavailable;
- current global Kimia config must not be presented as multi-tenant safe;
- no Kimia account creation;
- no Kimia Write;
- no automatic customer mapping;
- no global-unique-index rewrite;
- no credential migration;
- no blind cherry-pick of historical tenancy code.

## Next safe recovery work

1. inspect PR/commit ancestry around ADR-026 and Tenant Checkpoint 1 to determine whether any later bounded table-group checkpoint existed under a different name;
2. inspect current/historical service-provider bindings for KimiaClient to verify whether any runtime factory/resolver ever replaced direct construction;
3. inspect sync commands/jobs/schedulers for explicit tenant or connector targeting;
4. inventory current `services.kimia` / `.env.example` / production configuration contract without exposing secrets;
5. document a non-destructive recovery candidate only after the above evidence is closed.
