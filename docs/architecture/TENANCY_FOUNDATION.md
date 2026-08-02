# GoldPlatform — Tenancy Foundation

**Status:** Implementation prepared; Laravel/MySQL runtime verification pending

**Date:** 2026-08-03

## Accepted contract

ADR-026 fixes the first-release architecture as:

- shared database/shared schema with mandatory tenant ownership;
- mobile uniqueness inside each tenant, not globally;
- one active Kimia connector/book per tenant in the first release, with a later extension
  path for multiple connectors;
- Platform Super Admin separate from tenant Admin/Operator;
- verified domain/subdomain resolution and authenticated user/tenant cross-checking.

## This checkpoint

Only the independent root and resolution foundation is prepared:

```text
tenants
tenant_domains
Tenant / TenantDomain models
TenantHost normalization
TenantResolver
request-scoped TenantContext
tenant.resolve middleware alias
negative isolation tests
```

`tenant_domains.host` is globally unique and stored in canonical lowercase form without a
port or trailing dot. Resolution succeeds only when both the domain and its Tenant are
active and the domain has `verified_at`. Unknown, inactive, or unverified domains return no
Tenant; there is no Khalifeh Coin fallback. Tenant deletion is restricted while domain
records exist rather than cascading silently.

`TenantContext` rejects switching from one Tenant to another inside the same execution
scope. The resolved Tenant is also placed in the Request attributes for downstream code.

## Runtime activation boundary

The `tenant.resolve` alias is registered but is not attached to production routes in this
checkpoint. That prevents partial tenancy from changing current Auth, Kimia, Order, or
customer behavior before tenant ownership is added to their tables.

Authenticated user/tenant cross-checking is an accepted invariant, but it cannot be
activated safely until the separately reviewed `users` table-group migration adds and
backfills explicit Tenant ownership. The future mobile constraint will be
`unique (tenant_id, mobile)`; the current global mobile constraint is unchanged here.

## Explicitly deferred

- inserting or backfilling Khalifeh Coin production data;
- adding `tenant_id` to users, roles, accounts, Kimia projections, Catalog, Orders,
  Wallet/Ledger, Custody, or integration logs;
- replacing any current unique index;
- moving Kimia credentials into connector records;
- creating the public branding/bootstrap endpoint;
- applying Tenant global scopes to Eloquent models;
- carrying Tenant/connector context in queues, schedules, or sync commands;
- enabling any Kimia write.

## Prepared verification

`TenantDomainResolutionTest` checks:

1. canonical host resolution;
2. rejection of unknown, inactive, and unverified domains;
3. global host uniqueness after normalization;
4. Middleware context propagation and neutral 404 behavior;
5. rejection of Tenant switching inside one execution scope.

Static PHP parsing and duplicate-method checks pass. Laravel, SQLite/MySQL migrations, and
the prepared tests remain pending the automated CI/shop Docker runtime.
