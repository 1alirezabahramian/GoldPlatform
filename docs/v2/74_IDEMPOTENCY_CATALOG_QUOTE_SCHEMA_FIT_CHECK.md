# GoldPlatform V2 — Idempotency / Catalog / Quote Schema Fit Check

Status: STRUCTURAL FIT CHECK — MIGRATION BLOCKED PENDING CATALOG IDENTITY DECISION
Date: 2026-08-08
Work slice: PR #201 (`v2/identity-onboarding-policy-foundation`)

## Verified checkpoint

Verified execution path at the start of this fit check:

- Base: `v2/v2-01-canonical-runtime-integration`
- Base SHA: `640bae5aac57350803554f0a1e75f91b7856e20b`
- Starting head: `c42b2af88887fb9da2bd6ed34561400127373d8a`
- PR #201: OPEN / DRAFT / NOT MERGED / MERGEABLE
- Operational Readiness #183: EXECUTED — PASS
- Backend RC1 Validation #567: EXECUTED — PASS

No newer PR created after PR #201 was found at checkpoint validation.

## 1. Existing idempotency registry inventory

Canonical current-branch components:

- `App\\Models\\IdempotencyRecord`
- table: `idempotency_keys`
- `App\\Http\\Middleware\\IdempotencyMiddleware`
- route scopes currently include at least:
  - `order.create`
  - `delivery.request`
  - `delivery.approve`
  - `delivery.ready`
  - `delivery.deliver`
  - `policy.update`
  - `staff.create`

Database uniqueness is currently:

`UNIQUE(user_id, scope, key_hash)`

Middleware lookup uses the same effective tuple:

`user_id + scope + key_hash`

Same key with a different request hash conflicts. Completed replay returns the stored response except for explicitly non-replayable sensitive scopes such as `staff.create`.

### Tenant implication

`tenant_id` is not an explicit column in the idempotency lookup or unique constraint. However the current User model is Tenant-bound through `users.tenant_id`, and sensitive Tenant routes are protected by Host/Tenant resolution plus user↔Tenant matching.

This means there is not enough evidence to call the current registry unsafe solely because `tenant_id` is absent from the unique key. For authenticated customer/staff operations, a globally stable User ID already isolates different users. The remaining fit question for Quote is whether all future Quote idempotency call sites are guaranteed to be authenticated and Tenant-resolved before the middleware lookup and whether any lifecycle/system operation could require Tenant scope independently of User scope.

Classification:

**Existing Idempotency Registry: REUSE AFTER FIT CHECK**

No parallel Quote idempotency table/registry should be created at this point.

## 2. Current financial asset/catalog identity inventory

Canonical V2 financial read path already establishes:

- Money / Gold / Coin / Currency are Kimia-owned financial balances.
- Coin catalog is read dynamically from `GET /api/product/coins`.
- Currency catalog is read dynamically from `GET /api/product/currencies`.
- `ProductReadRepository` exposes these two dynamic Kimia catalogs.
- `CustomerKimiaFinancialAssetReadService` classifies balance rows against live Kimia CoinId/CurrencyId catalogs and does not expose those internal identifiers to the customer API.
- ambiguous Coin/Currency identity fails closed.

Current `AssetType` supports:

- `money`
- `gold`
- `coin`
- `currency`

and requires `external_asset_id` for Coin/Currency.

Current `Order` persists `asset_type` plus nullable/integer `external_asset_id` rather than a canonical local Product FK.

A canonical current-head `App\\Models\\Product` file was directly checked and was not found at the expected path. This single miss is **not** treated as proof that no product/catalog persistence exists anywhere in history.

Historical PR #129 is CLOSED — NOT MERGED and therefore not canonical. It is useful only as evidence that an older branch read `product_categories` and `products` tables directly, including `products.kimia_product_id`, while explicitly reporting that dynamic Coin/Currency catalog and Kimia product-sync support were absent in that historical slice.

### Classification

- Dynamic Kimia Coin/Currency identity for financial reads: **REUSE AS-IS**
- `AssetType + external_asset_id` order identity: **REUSE AFTER FIT CHECK** for Quote linkage
- historical `products` / `product_categories` read slice from PR #129: **HISTORICAL ONLY**
- canonical Quote Product/Catalog FK target: **UNRESOLVED**

## 3. Quote/Pricing duplicate re-search

Immediately before any Quote migration, the current branch and relevant historical PR chronology were re-checked.

Current canonical V2 docs still classify:

- current Quote runtime: NOT IMPLEMENTED
- historical Quote lifecycle patterns: REUSE AFTER FIX
- historical Quote persistence: REBUILD
- persistence architecture: DESIGNED — NOT IMPLEMENTED

Historical PR #109 is CLOSED — NOT MERGED and explicitly excluded pricing formulas, freeze duration, customer limits, Wallet/Ledger mutation and Kimia write mappings. It cannot be treated as canonical Quote persistence.

Historical PR #129 is CLOSED — NOT MERGED and provided read-only Product/Pricing visibility only. It cannot be promoted to canonical Product/Pricing authority.

No historical branch is merged or cherry-picked by this fit check.

## 4. Idempotency decision for Quote creation

The existing registry is preferred over introducing duplicate infrastructure.

A future Quote-create route may reuse it only if all of these are true and tested:

1. authenticated Customer is resolved before idempotency handling;
2. Tenant is resolved from Host/TenantContext and user↔Tenant mismatch fails closed;
3. normalized request hashing excludes no materially relevant customer intent;
4. same key + same normalized intent resolves to the same safe Quote response;
5. same key + materially different intent conflicts;
6. replay cannot cross Customer or Tenant scope;
7. expiry/use of a previously returned Quote is enforced by Quote lifecycle, not by mutating the idempotency record.

No schema change to `idempotency_keys` is justified yet.

## 5. Quote persistence migration decision

Do **not** create the Quote migration yet.

The remaining structural blocker is the canonical Quote product/asset identity target.

A Quote must preserve immutable product/asset identity without hard-coding Coin/Currency IDs. Current evidence supports dynamic Kimia Coin/Currency identifiers and an existing `AssetType + external_asset_id` pattern, but does not yet prove whether V2 should:

- bind Quote to a canonical local Product/Catalog record;
- bind Quote structurally to `AssetType + external_asset_id`;
- or use another existing canonical catalog primitive not yet verified in current migration/schema history.

Choosing one now would be an architectural guess and could create duplicate Product/Catalog authority.

Therefore:

**Quote additive migration: BLOCKED BY STRUCTURAL GROUND TRUTH — PRODUCT/CATALOG IDENTITY TARGET**

No financial value/default is blocked by this document because runtime financial activation is not being attempted. Exact Decimal scale, provider mapping, x/y/z, Customer Level adjustments, commission, tax, rounding, limits, credit semantics and Quote→Order/Kimia Write remain separately blocked where already documented.

## Next safe step

Resolve the current canonical database/catalog identity by locating and validating the migrations/schema that define any surviving `products`, `product_categories`, product↔Kimia mapping or equivalent local catalog structure on the exact V2 ancestry. Only after that evidence is complete should the physical Quote FK/layout decision be made.

No Merge is authorized by this document.
