# GoldPlatform V2 — Customer/Kimia Binding Tenant Scope Audit

- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Classification: `REUSE AFTER FIX / BLOCKED BY TENANT-SAFE BINDING DESIGN`
- Product behavior change: `NONE`
- Kimia Write: `UNCHANGED — BLOCKED BY GROUND TRUTH`

## Result

The authenticated Customer -> Kimia AccountId binding gap is now confirmed across four layers:

1. current canonical schema;
2. current canonical Kimia synchronization destination;
3. historical runtime/project evidence;
4. accepted multi-tenancy architecture.

The missing bridge cannot be restored safely as a global single-tenant relation without first carrying forward the accepted tenant/connector ownership boundary.

## Current canonical facts

Current canonical customer binding still uses:

`users.account_id -> accounts.id -> accounts.kimia_id`

Current canonical Kimia account synchronization writes:

`Kimia /api/account -> external_accounts(provider='kimia', external_id=AccountId)`

The current `external_accounts` schema contains no `user_id`, `account_id`, mapping FK or dedicated customer-binding relation.

The current canonical branch does not contain the previously prepared `Tenant` model at `backend/app/Models/Tenant.php`.

## Historical tenant foundation recovered

Historical branch `work/product-kimia-next` contains the prepared ADR-026 bounded tenancy foundation, including:

- `App\Models\Tenant`;
- `App\Models\TenantDomain`;
- `App\Tenancy\TenantResolver`;
- host/domain based verified tenant resolution;
- fail-closed tenant lookup.

This is valid historical implementation evidence, but it is not current canonical implementation and must not be cherry-picked blindly.

Classification: `REUSE AFTER FIX`.

## Accepted ADR-026 ground truth

Project Memory records the accepted multi-tenancy direction:

1. shared database/shared schema with mandatory `tenant_id` ownership;
2. mobile uniqueness per tenant;
3. exactly one active Kimia connector/book per tenant in first release, prepared for more later;
4. Platform Super Admin separated from Tenant Admin/Operator;
5. verified domain/subdomain resolution plus authenticated user/tenant cross-check.

It also explicitly records that independent Kimia installations may reuse numeric external identifiers. Therefore global uniqueness of Kimia identity cannot be assumed safe across tenants.

The accepted implementation boundary was intentionally incremental:

`Tenant root + verified domain + explicit context/resolver + isolation tests`

with no all-table migration and no global unique-index replacement without duplicate preflight.

## Runtime/data evidence recovered

Project Memory records:

- an historical owner-run synchronization reported 414 Kimia accounts synchronized, including AccountId 350;
- the active synchronized representation is `external_accounts`;
- at a later audit checkpoint `Account::count()` returned 0;
- `users.account_id` still targets `accounts`;
- the identity migrations/guards were prepared but not applied to shop data at that checkpoint.

This is consistent with a real split between synchronized Kimia projection data and customer financial binding data.

No recovered evidence proves that those 414 synchronized external accounts were reconciled into `accounts` or linked to `users.account_id`.

## Search result boundary

Repository PR/commit searches for account binding/reconciliation names returned no verified executable bridge. Code-search index misses are not treated as proof of non-existence.

After cross-checking:

- current canonical schema;
- current canonical sync command;
- historical ADR-024 constraints;
- historical tenancy foundation;
- Project Memory runtime evidence;
- accepted ADR-026 scope;

there is still no verified executable tenant-safe Customer -> synchronized Kimia account bridge in canonical.

## Capability classification

### Historical Tenant root/domain resolver

`REUSE AFTER FIX`

It implements accepted ADR-026 concepts, but is historical and must be compared/reintegrated against current canonical recovery code rather than merged blindly.

### Current global `accounts.kimia_id` uniqueness

`REFACTOR`

Safe only as an interim single-tenant constraint. Final multi-tenant identity must include Tenant + connector/book scope before independent Kimia installations are supported.

### Current `external_accounts(provider, external_id)` uniqueness

`REFACTOR`

The provider/external identity is useful, but global `(provider, external_id)` is insufficient once multiple independent Kimia books/connectors exist.

### Customer financial binding bridge

`NOT IMPLEMENTED IN CURRENT VERIFIED CANONICAL PATH`

A verified mapping/resolver from authenticated User to the correct Tenant + connector/book + Kimia AccountId is absent from the current canonical execution path.

### Automatic mobile/name/national-code matching

`NOT APPROVED`

No automatic account linking may be inferred from identity similarities.

## Safety boundary

Until a tenant-safe bridge is designed and validated:

- customer Money/Gold/Coin/Currency endpoints remain fail-closed;
- no Wallet/Ledger/Projection substitute is permitted;
- no global auto-link by AccountId is permitted for final multi-tenant architecture;
- no automatic matching by mobile, national code, name or account code;
- no Kimia account creation;
- no Kimia Write;
- no tenant_id backfill;
- no unique-index replacement;
- no blind cherry-pick of the historical tenancy branch.

## Next safe recovery work

1. recover exact historical Tenant migrations/middleware/tests and compare them with current canonical schema/routes;
2. inspect whether any historical connector/book model existed beyond the Tenant root checkpoint;
3. recover sanitized shop verification output for counts of `accounts`, `external_accounts`, linked `users.account_id`, duplicates and orphans where available;
4. define the smallest non-destructive tenant-safe binding architecture only after those facts are closed;
5. keep V2-00 open until this traceability chain is complete.
