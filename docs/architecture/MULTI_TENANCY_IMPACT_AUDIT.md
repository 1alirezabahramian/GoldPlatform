# GoldPlatform — Multi-tenancy Impact Audit

**Status:** Completed architecture audit; no runtime change

**Date:** 2026-08-03

## Purpose

GoldPlatform is intended to be a White-label platform with `Khalifeh Coin` as the first
real tenant. This audit identifies the current single-tenant assumptions that must be
resolved before production Catalog, Pricing, OMS, Custody, or tenant-facing panels are
built on top of them.

This document records evidence from the current schema and code. It does not approve a
tenancy strategy and does not authorize a Migration.

## Confirmed boundary

- A tenant represents one business operating a branded GoldPlatform environment.
- Tenant data must never be readable or writable by another tenant.
- Tenant-specific branding, domains, limits, commissions, features, branches, and
  connectors must not be hard-coded in the platform core.
- Kimia identifiers are external identifiers. Their uniqueness cannot safely be assumed
  across independent tenants or independent Kimia installations.
- `Khalifeh Coin` is the pilot tenant, not a permanent global default hidden in code.

## Current single-tenant assumptions

| Current area | Current constraint or behavior | Multi-tenant impact |
|---|---|---|
| `users` | `mobile` is globally unique | The owner must decide whether uniqueness is global or per tenant. |
| `user_groups` | No tenant ownership | Commissions, limits, and policies could leak across businesses. |
| `accounts` | `kimia_id` is globally unique | Independent Kimia systems may reuse the same numeric AccountId. |
| `external_accounts` | `(provider, external_id)` is globally unique | Provider identity needs tenant or connector scope. |
| `account_groups` | `kimia_id` is globally unique | Kimia group identifiers may repeat across tenants. |
| `kimia_coins` | `kimia_id` is globally unique | Coin projections cannot safely contain two independent Kimia sources. |
| `kimia_currencies` | `kimia_id` is globally unique | Currency projections cannot safely contain two independent Kimia sources. |
| `product_categories` | `slug` is globally unique | Tenant catalogs cannot independently reuse familiar slugs. |
| `products` | `barcode` is globally unique; no tenant ownership | Catalog, visibility, stock, and pricing are not isolated. |
| `wallets` and ledger tables | Ownership is only indirect through `user_id`/wallet | Financial queries need an explicit tenant boundary and consistency checks. |
| `orders` and `trades` | No explicit tenant ownership | Operational queries and identifiers are unsafe for tenant-wide access. |
| Kimia/SMS/Jibit logs | No tenant ownership | Integration evidence and personal data could mix. |
| Kimia configuration | One global environment configuration | A White-label tenant cannot have an isolated connector configuration. |
| Roles and permissions | No accepted tenant-team contract | Tenant admins/operators could receive cross-tenant authority if scoped incorrectly. |
| HTTP routing | No tenant resolver | Branding, Auth, Catalog, and policies have no trusted current-tenant source. |
| Queue/commands | No required tenant context | Background sync could run against an implicit or wrong connector. |

## Tables requiring classification

Before any tenancy Migration, every table must be classified as one of the following:

1. **Platform-global:** system-level configuration that is intentionally shared.
2. **Tenant-owned:** data that belongs to exactly one tenant.
3. **User-owned within a tenant:** data reached through a user but still carrying a
   verifiable tenant boundary.
4. **External projection:** rebuildable data scoped to a tenant and a specific connector.
5. **Operational evidence:** audit/log/idempotency records that retain tenant ownership.

The default for financial, customer, order, custody, pricing, connector, and audit data is
tenant-owned. Any exception requires an explicit reason.

## Required uniqueness changes if shared-schema tenancy is approved

The following examples describe the shape of future constraints; they are not Migration
instructions yet:

```text
accounts                  unique (tenant_id, kimia_id)
account_groups            unique (tenant_id, kimia_id)
kimia_coins               unique (tenant_id, connector_id, kimia_id)
kimia_currencies          unique (tenant_id, connector_id, kimia_id)
external_accounts         unique (tenant_id, connector_id, external_id)
product_categories        unique (tenant_id, slug)
products                  unique (tenant_id, barcode) where barcode is present
```

Whether `users.mobile` becomes `unique (tenant_id, mobile)` or remains globally unique is
an owner decision and must not be inferred.

## Safe migration sequence after approval

A future implementation should use small, reversible checkpoints:

1. Create the tenant root and register `Khalifeh Coin` explicitly.
2. Add nullable tenant references to one bounded table group.
3. Backfill the pilot tenant without changing business data.
4. Run orphan, count, duplicate, and cross-tenant preflight checks.
5. Replace global unique constraints with approved tenant-scoped constraints.
6. Make tenant ownership mandatory only after verification.
7. Add request, policy, job, and console-command isolation tests.
8. Repeat for the next table group.

No large all-table Migration should be applied in one step.

## Backend contract required before implementation

- A trusted `TenantContext` must be resolved before tenant-owned use cases run.
- Public host/domain resolution must not trust a free-form tenant identifier from the
  browser.
- Authenticated requests must reject a user/host tenant mismatch.
- Super-admin cross-tenant access must be explicit, permission-checked, and audited.
- Queue jobs and scheduled commands must carry an explicit tenant identifier.
- Kimia sync commands must require an explicit tenant/connector target.
- Repository and policy tests must prove that records from tenant B are invisible and
  unmodifiable while tenant A is active.
- UI filtering is never an authorization boundary.

## Frontend contract required before implementation

- A public bootstrap response may expose only safe branding, locale, support contact, and
  enabled customer-facing modules for the resolved tenant.
- Kimia credentials, connector identifiers, license internals, and raw financial codes are
  never exposed.
- An unknown or inactive domain receives a neutral unavailable response and no fallback
  tenant data.
- Changing domain or tenant context must not silently retain another tenant's selected
  account, cached assets, or session state.
- Navigation visibility improves UX but Backend policies remain authoritative.

## Decisions still required from the owner

1. **Isolation model:** shared database/shared schema or database per tenant.
2. **Mobile uniqueness:** one mobile across the entire platform or one mobile per tenant.
3. **Kimia connection cardinality:** exactly one connector/book per tenant in the first
   release, or multiple connectors/branches from the start.
4. **Tenant administration:** confirm separate platform Super Admin and tenant Admin roles.
5. **Tenant resolution:** approve custom domain/subdomain as the public source of tenant
   identity, with authenticated user-to-tenant cross-checking.

## Stop condition

Do not add `tenant_id`, change unique indexes, move credentials, or build tenant-specific
Catalog/OMS behavior until ADR-026 is accepted and the open decisions above are closed.
