# ADR-026 — Multi-tenancy Isolation Strategy

**Status:** Accepted

**Date:** 2026-08-03

**Accepted by:** Alireza Bahramian, project owner — 2026-08-03

## Context

GoldPlatform must be a White-label product for several jewelers or companies, while
`Khalifeh Coin` remains the first pilot tenant. The current code and schema are still
single-tenant: customer, product, order, wallet, ledger, connector, and Kimia projection
tables do not carry a tenant boundary, and several external identifiers are globally
unique.

Building Catalog, Pricing, OMS, Custody, or production panels before selecting an
isolation strategy would preserve these single-tenant assumptions and create avoidable
rework.

The evidence inventory is recorded in
[`../architecture/MULTI_TENANCY_IMPACT_AUDIT.md`](../architecture/MULTI_TENANCY_IMPACT_AUDIT.md).

## Decision

Choose the first production isolation model and tenant-resolution contract before any
tenancy Migration is created.

### Option A — Shared database, shared schema, mandatory tenant ownership

Each tenant-owned row carries `tenant_id`. External identifiers and configurable slugs are
unique inside the tenant/connector boundary rather than across the whole platform.

**Advantages**

- Fits the current Laravel monolith and existing deployment model.
- Lowest initial operational complexity for backups, migrations, monitoring, and support.
- Supports platform-level administration and reporting without copying data between
  databases.
- Allows Khalifeh Coin to be backfilled as the first explicit tenant in small steps.

**Risks**

- A missing tenant condition can expose another tenant's data.
- Every request, job, command, repository, policy, and test must enforce isolation.
- Global unique constraints must be replaced carefully after duplicate preflight checks.

### Option B — Separate database per tenant

Each tenant receives a separate business database while a platform database keeps the
tenant directory and connection metadata.

**Advantages**

- Stronger physical data separation.
- Per-tenant backup, restore, or data export can be simpler conceptually.

**Risks**

- Significantly more complex migrations, connection management, queues, monitoring,
  reporting, support, and disaster recovery.
- Cross-tenant platform administration becomes harder.
- It adds substantial infrastructure before the first production tenant is complete.

### Option C — Independent deployment per customer

Every White-label customer receives a separate application deployment and database.

This provides strong separation but is not the shared Multi-tenant product direction
already accepted for GoldPlatform. It multiplies release, monitoring, security-patch, and
support work and is not recommended as the default product architecture.

## Accepted architecture

Adopt **Option A: shared database/shared schema with mandatory tenant ownership** for the
first production architecture, with these mandatory safeguards:

1. `tenants` is the explicit root of tenant-owned data; Khalifeh Coin is inserted as the
   first tenant rather than assumed as a default.
2. Public requests resolve a tenant from a verified active domain/subdomain.
3. Authenticated requests cross-check the resolved tenant against the authenticated
   user's tenant and reject mismatches.
4. Queue jobs, scheduled tasks, and Kimia sync commands carry an explicit tenant and
   connector identifier.
5. Kimia credentials move from one global runtime configuration to tenant-scoped secure
   connector configuration in a later, separately reviewed checkpoint.
6. External Kimia identifiers use tenant/connector-scoped composite uniqueness.
7. Tenant Admin and Operator access is tenant-scoped. Platform Super Admin access is
   separate, explicit, and audited.
8. Isolation is enforced in Backend policies/repositories and verified with negative
   cross-tenant tests; hiding Frontend navigation is not considered security.
9. Tenancy is migrated table group by table group with backfill and preflight checks; no
   all-table Migration is allowed.

## Tenant resolution contract

```text
Public request
    -> verified host/domain
    -> active tenant
    -> safe branding/config bootstrap

Authenticated request
    -> verified host/domain tenant
    + authenticated user's tenant
    -> must match

Queue / command
    -> explicit tenant_id + connector_id
    -> no implicit host fallback
```

A free-form browser header is not a trusted tenant source. An inactive or unknown domain
must fail closed and must never fall back to Khalifeh Coin.

## Accepted owner decisions

On 2026-08-03, project owner Alireza Bahramian approved all five decisions:

1. **Isolation:** use Option A — one shared database and shared schema with mandatory
   `tenant_id` ownership and enforced cross-tenant isolation.
2. **Mobile uniqueness:** a mobile number is unique inside one tenant, not globally. The
   same mobile may own one separate platform account in another tenant. Future constraints
   therefore use tenant-scoped uniqueness such as `(tenant_id, mobile)`.
3. **Kimia connection cardinality:** the first release supports exactly one active Kimia
   connector/book per tenant. The connector model and foreign-key boundaries must remain
   ready for multiple branches/books in a later reviewed release without redefining
   tenant ownership.
4. **Administration:** `Platform Super Admin` is separate from every tenant's
   `Admin/Operator`; cross-tenant access must be explicit and audited.
5. **Tenant resolution:** public requests resolve from a verified active domain/subdomain,
   and authenticated requests must cross-check that tenant against the user's tenant.

These decisions authorize the bounded tenancy foundation described below. They do not
authorize an all-table migration, live Kimia writes, KYC reuse across tenants, branch
accounting rules, or any new financial rule.

## Implementation status

The first bounded checkpoint is prepared: new Tenant root/domain tables, Models, canonical
Host resolution, request-scoped TenantContext, an inactive Middleware alias, and negative
isolation tests. It does not modify any existing business table or unique index and is not
attached to production routes. Runtime and migration verification remain pending. See
[`../architecture/TENANCY_FOUNDATION.md`](../architecture/TENANCY_FOUNDATION.md).

## Consequences

- The first implementation checkpoint prepares only the tenant root, domain resolution
  foundation, and isolation tests.
- Existing tables are migrated in later bounded groups after data preflight.
- Catalog and Pricing can then be designed with stable tenant ownership.
- Frontend can safely load tenant branding and feature availability from a stable bootstrap
  contract.

## Scope boundary

This proposal does not decide pricing rules, commissions, KYC reuse, branch accounting,
Kimia write Payloads, licensing plans, or tenant billing. It performs no data migration and
does not enable any live Kimia write.
