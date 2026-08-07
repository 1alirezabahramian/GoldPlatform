# GoldPlatform V2 — Tenant + Kimia Connector Runtime Gap Closure

- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Classification: `NOT IMPLEMENTED IN CURRENT VERIFIED CANONICAL PATH`
- Product behavior change: `NONE`
- Migration change: `NONE`
- Kimia Write: `UNCHANGED — BLOCKED BY GROUND TRUTH`

## Purpose

Close the evidence question of whether the current Recovery canonical already contains a hidden or indirect tenant-scoped Kimia connector runtime that could safely support authenticated Customer -> Kimia AccountId resolution.

## Verified canonical evidence

### Application container / provider

`AppServiceProvider` registers SMS bindings, UserObserver, rate limits, and slow-query logging. It does not bind a tenant-aware Kimia client, connector resolver, connector repository, or per-tenant integration configuration.

### Scheduler / console routing

`routes/console.php` schedules only the guarded Outbox dispatcher. No tenant-targeted Kimia synchronization schedule or tenant/connector loop is present there.

### Environment contracts

Both `.env.example` and `.env.production.example` expose one global Kimia configuration set:

- `KIMIA_BASE_URL`
- `KIMIA_USERNAME`
- `KIMIA_PASSWORD`
- read-only / retry / timeout settings
- write enable safety flag where applicable

There is no tenant-keyed or connector-keyed environment contract in the current canonical examples.

### Kimia client construction

`App\Integrations\Kimia\Client\KimiaClient` reads `services.kimia.*` directly in its constructor. No `TenantContext`, `tenant_id`, `connector_id`, connector object, or explicit book target participates in client construction.

### Account synchronization command

Current `kimia:sync-accounts` accepts only an optional Kimia account `--type` list. It has no required `--tenant`, `--connector`, or `--book` target.

The command writes synchronized discovery/snapshot rows into `external_accounts` and resolves existing rows using only:

- `provider = kimia`
- `external_id = Kimia AccountId`

This confirms the current runtime projection key is global with respect to tenant/connector scope.

### Customer/account models

Current canonical models additionally show:

- `User` has `account_id` but no `tenant_id`;
- `Account` has `kimia_id` but no `tenant_id` or connector ownership;
- `ExternalAccount` has provider/external identity metadata but no `tenant_id`, `connector_id`, `user_id`, or `account_id` relation.

## Historical comparison

ADR-026 / historical tenancy foundation accepted one active Kimia connector/book per tenant for the first release and required explicit tenant ownership, verified domain resolution, authenticated user/tenant cross-checking, and connector-targeted background work.

That historical checkpoint intentionally deferred:

- adding tenant ownership to users/accounts/Kimia projections;
- moving Kimia credentials into connector records;
- carrying tenant/connector context through sync commands, queues, and schedules;
- replacing global Kimia uniqueness constraints.

A historical commit added optional `KIMIA_BOOK_ID` to a global read-client configuration, but that value was not a connector entity or tenant-owned integration record.

## Closure classification

### Tenant root/domain foundation

`REUSE AFTER FIX`

Historical implementation exists, but it was intentionally inactive on production routes and did not carry tenant ownership into business/integration tables.

### Tenant-owned Kimia connector/book runtime

`NOT IMPLEMENTED IN CURRENT VERIFIED CANONICAL PATH`

No verified current Model, migration, repository, resolver, provider binding, environment contract, command target, queue/scheduler context, or client-construction path supplies tenant-owned Kimia connection identity.

### Current global Kimia client configuration

`REUSE AFTER FIX`

Safe for the current single-connection read-only recovery/runtime boundary, but not sufficient as the final white-label multi-tenant connector architecture.

### Current external_accounts uniqueness / sync lookup

`REFACTOR`

Global `(provider, external_id)` identity is not safe for independent Kimia installations under accepted ADR-026. Future scope must include the approved tenant/connector identity before multi-tenant activation.

## Binding consequence

The authenticated financial read path cannot safely be completed by adding only a User -> Account link.

The recoverable target architecture must preserve this ordering:

`Resolved Tenant -> Active Kimia Connector/Book -> Verified Customer Binding -> Kimia AccountId -> Kimia Read`

The exact schema/mechanics remain a design task after evidence recovery. No auto-linking by mobile, national code, name, or account code is authorized.

## Safety boundary

Until a reviewed tenant/connector foundation is integrated:

- Customer financial balance endpoints remain fail-closed;
- no global Kimia identifier is treated as multi-tenant-safe;
- no automatic Customer/Kimia mapping is created;
- no connector credential migration is applied;
- no Kimia account creation is enabled;
- no Kimia Write is enabled;
- no historical tenancy branch is merged/cherry-picked blindly.

## V2-00 conclusion for this gap

The evidence-recovery question is now closed sufficiently to classify the runtime gap. Further work is no longer source discovery for whether a current tenant-owned Kimia connector runtime exists; it is recovery/design comparison for a non-destructive implementation candidate using accepted ADR-026 constraints and recovered historical tenancy evidence.
