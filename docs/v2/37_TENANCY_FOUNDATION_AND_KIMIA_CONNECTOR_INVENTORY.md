# GoldPlatform V2 — Tenancy Foundation and Kimia Connector Inventory

- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Scope: evidence recovery only
- Product behavior change: `NONE`
- Kimia Write: `UNCHANGED — BLOCKED BY GROUND TRUTH`
- Migration execution: `NONE`

## Purpose

Inventory the recoverable historical tenancy foundation and determine whether the accepted first-release Kimia connector/book model was actually implemented, before proposing any Customer -> Tenant -> Kimia AccountId bridge.

## Historical tenancy foundation recovered

The historical branch `work/product-kimia-next` contains a bounded tenancy checkpoint with the following concrete artifacts:

- `App\Models\Tenant`
- `App\Models\TenantDomain`
- `App\Tenancy\TenantHost`
- `App\Tenancy\TenantResolver`
- `App\Tenancy\TenantContext`
- `App\Http\Middleware\ResolveTenantFromDomain`
- middleware alias `tenant.resolve`
- migration `2026_08_03_130000_create_tenants_table.php`
- migration `2026_08_03_130100_create_tenant_domains_table.php`
- `TenantDomainResolutionTest`
- `docs/architecture/TENANCY_FOUNDATION.md`
- `docs/architecture/MULTI_TENANCY_IMPACT_AUDIT.md`

### Verified behavior

The recovered resolver accepts only a normalized host whose domain row is active and verified and whose Tenant is active. Unknown, inactive and unverified hosts fail closed.

`TenantContext` is request/execution scoped and refuses switching from one Tenant to another inside the same execution scope.

`ResolveTenantFromDomain` activates the trusted context, stores the resolved Tenant in request attributes and returns a neutral 404 when no trusted Tenant can be resolved.

`tenant_domains.host` is globally unique, and Tenant deletion is restricted while domain records reference it.

The feature test covers:

1. verified active domain resolution;
2. rejection of unknown, inactive and unverified domains;
3. normalized global domain uniqueness;
4. middleware context propagation and fail-closed unknown-host handling;
5. rejection of Tenant switching inside one execution scope.

## Important activation boundary

The recovered tenancy checkpoint intentionally registers the `tenant.resolve` middleware alias without attaching it to production routes.

The accompanying architecture documentation explicitly defers:

- production Tenant backfill;
- `tenant_id` on users/accounts/Kimia projections and other business tables;
- replacement of current global unique indexes;
- moving Kimia credentials into connector records;
- Tenant/connector context in queue, scheduler and Kimia sync commands;
- authenticated user/Tenant cross-check activation;
- Kimia Write.

This was a deliberate safety boundary, not an incomplete accidental route registration.

## Kimia connector/book inventory

ADR-026 / accepted architecture requires one active Kimia connector/book per Tenant for the first release, with an extension path for multiple connectors later.

Current recovered evidence does **not** establish a persisted Kimia connector entity or connector table in the historical tenancy checkpoint.

The branch comparison that exposes the tenancy checkpoint contains no Kimia connector model/migration. `MULTI_TENANCY_IMPACT_AUDIT.md` explicitly records current Kimia configuration as one global environment configuration and states that a White-label Tenant cannot have an isolated connector configuration under that state.

A separate historical commit `81518ad0aa56e560694f5a9365331ea13581e815` adds optional `KIMIA_BOOK_ID` to the global `services.kimia` environment configuration. This is a read-client configuration value, not a Tenant-owned connector/book record.

No commit named around `connector_id` was recovered through commit search in this pass. The repository code-search index is unavailable, therefore zero search results are not treated as proof of project-wide absence.

## Classification

### Historical Tenant root/domain foundation

Status: `REUSE AFTER FIX`

Reason:
- concrete models, migrations, resolver, context, middleware and tests exist;
- fail-closed domain resolution matches accepted isolation principles;
- it is historical and not verified as active in the current Recovery canonical;
- users/accounts/Kimia projections were deliberately not tenant-scoped in that checkpoint.

### Tenant production activation

Status: `NOT IMPLEMENTED IN CURRENT VERIFIED CANONICAL PATH`

Production routes cannot safely activate Tenant context until bounded table ownership/backfill and authenticated user/Tenant cross-checking are implemented and validated.

### Persisted Tenant-owned Kimia connector/book model

Status: `NOT VERIFIED — CONTINUE RECOVERY`

Current evidence proves only:
- accepted connector cardinality in ADR-026;
- a global environment Kimia configuration;
- optional global `KIMIA_BOOK_ID` read-client setting;
- explicit deferral of moving credentials into connector records in the recovered tenancy checkpoint.

Do not classify the connector model as globally nonexistent until remaining branches/commits/docs are inspected.

### Global Kimia identifiers

Status: `REFACTOR`

The recovered impact audit explicitly identifies these global constraints as unsafe for White-label multi-tenancy:

- `accounts.kimia_id`
- `external_accounts(provider, external_id)`
- `account_groups.kimia_id`
- `kimia_coins.kimia_id`
- `kimia_currencies.kimia_id`

Future shape requires Tenant and, where appropriate, connector scope. No unique constraint is changed in V2-00.

## Customer binding consequence

The Customer -> Kimia resolution path cannot be safely repaired by simply restoring the older `accounts` sync or by linking directly to globally scoped `external_accounts`.

The minimum architecture dependency chain now verified is:

`verified host -> TenantContext -> authenticated user/Tenant cross-check -> Tenant-owned Kimia connector/book -> Tenant/connector-scoped external AccountId -> approved stable customer binding -> Kimia balance read`

The historical tenancy checkpoint implements only the first trusted host/context foundation of that chain.

## Safety boundary

Until the remaining connector and table-ownership evidence is recovered:

- no Tenant backfill;
- no production middleware activation;
- no unique-index replacement;
- no auto-link by name/mobile/national code;
- no direct `external_accounts` user binding;
- no Kimia account creation;
- no Kimia Write;
- Customer Money/Gold/Coin/Currency remain fail-closed when verified Kimia binding is unavailable.

## Next safe recovery work

1. inspect commits/branches after the tenancy checkpoint for connector/book model, connector configuration storage, Tenant-owned credentials or `connector_id` schema;
2. inspect historical migration/test evidence for `tenant_id` on users/accounts/external projections;
3. map which parts of Stage 02 tenant-scoped financial kernel are abstract `FinancialScope` only versus actual Tenant entity ownership;
4. compare those historical artifacts against current Recovery canonical before any carry-forward proposal;
5. only after those checks, prepare a non-destructive bounded recovery plan for the next table group.
