# V2-00 — Customer ↔ Kimia Binding Recovery Candidate Design

Status: DESIGN CANDIDATE — NO RUNTIME CHANGE — NO MIGRATION AUTHORIZED

## Purpose

Define the lowest-risk recovery shape for authenticated Customer → verified Kimia AccountId resolution using recovered evidence. This document does not apply a migration, activate tenancy, create connector credentials, auto-link customers, or enable Kimia Write.

## Accepted boundaries carried forward

From ADR-024:
- one GoldPlatform login/account resolves to at most one local Account and at most one Kimia AccountId in the current release;
- one Kimia AccountId must not be linked to more than one GoldPlatform login/account;
- AccountId is the durable immutable financial binding;
- mobile and national code are not durable financial identifiers;
- the current structural model is User belongsTo Account / Account hasOne User;
- active Kimia sync writing to external_accounts requires a separate consolidation task.

From ADR-026:
- shared DB/shared schema with mandatory tenant ownership;
- public tenant resolution is from verified active domain and authenticated requests must cross-check user tenant;
- first release supports exactly one active Kimia connector/book per tenant;
- commands/jobs must carry explicit tenant_id + connector_id;
- Kimia credentials move from global config to tenant-scoped secure connector configuration in a separately reviewed checkpoint;
- external Kimia identifiers require tenant/connector-scoped uniqueness.

## Current verified drift

Current canonical still has:
- User.account_id → Account;
- Account.kimia_id with no tenant/connector ownership;
- ExternalAccount(provider, external_id) as active Kimia sync projection with no user/account/tenant/connector link;
- global services.kimia configuration and a KimiaClient built directly from that global config;
- kimia:sync-accounts with no tenant/connector target.

Therefore Customer → Kimia resolution cannot be safely restored by filling one existing FK or by matching identity fields.

## Candidate A — Preserve Account as customer financial binding

Shape:

Tenant
→ active KimiaConnector/Book
→ ExternalAccount projection
→ explicit verified reconciliation/link
→ Account
→ User.account_id

Responsibilities:
- Tenant + connector identify which Kimia installation/book owns an external AccountId.
- ExternalAccount remains rebuildable synchronized projection/raw external evidence.
- Account remains the stable local customer financial binding referenced by User.
- Linking/reconciliation is explicit, transactional, auditable and idempotent.
- AccountId cannot be inferred from mobile/name/national code.

Required future schema direction, subject to migration preflight:
- explicit tenant ownership on User and Account;
- connector entity owned by Tenant;
- ExternalAccount scoped by tenant + connector;
- Account Kimia identity scoped by tenant + connector rather than global numeric uniqueness;
- preserve users.account_id relationship;
- unique one-user-per-account constraint after duplicate preflight.

Classification: REUSE AFTER FIX

Advantages:
- best alignment with ADR-024 and current User→Account domain contract;
- smallest semantic change to customer authorization and existing code;
- keeps ExternalAccount correctly classified as projection rather than customer identity authority;
- allows migration in bounded table groups as ADR-026 requires.

Risks:
- Account and ExternalAccount require explicit reconciliation semantics;
- current global Account.kimia_id uniqueness must be replaced only after tenant/connector backfill and duplicate preflight;
- cannot activate until tenant/connector ownership is established.

## Candidate B — Bind User directly to ExternalAccount

Shape:

Tenant
→ connector
→ ExternalAccount
→ User

Classification: REBUILD / NOT RECOMMENDED FOR CURRENT RELEASE

Advantages:
- fewer visible account representations.

Problems:
- conflicts with ADR-024's accepted User→Account binding model;
- turns a rebuildable synchronization projection into durable customer financial identity;
- requires wider authorization/API/model migration;
- makes external projection lifecycle/deletion/sync semantics part of login identity;
- increases coupling between Customer domain and Kimia projection schema.

## Candidate C — Add a new CustomerFinancialAccountBinding entity

Shape:

Tenant
→ connector
→ ExternalAccount
↔ CustomerFinancialAccountBinding
↔ User / Account

Classification: REBUILD / DEFER UNLESS NEW REQUIREMENT REQUIRES IT

Advantages:
- most explicit and extensible model;
- could support future multiple connectors/accounts/delegation more cleanly.

Problems:
- introduces another identity/binding abstraction before the current one-account release needs it;
- risks duplicating Account semantics;
- exceeds the minimal-recovery objective unless a later account-selector/person-layer requirement is approved;
- ADR-024 explicitly defers multi-account selector/person-layer work to a separate ADR.

## Recommended recovery candidate

Candidate A is the lowest-risk architecture candidate because it preserves accepted current-release semantics while adding the missing tenant/connector boundary around external Kimia identity.

This is not authorization to implement migrations yet. The safe implementation order must remain bounded:

1. recover/validate Tenant root and trusted TenantContext;
2. define tenant-owned KimiaConnector/Book contract without moving live credentials yet;
3. add nullable tenant/connector ownership to one reviewed table group;
4. backfill pilot-tenant ownership with count/orphan/duplicate preflight;
5. scope ExternalAccount uniqueness to tenant + connector + external_id;
6. scope Account Kimia identity to tenant + connector + kimia_id;
7. introduce explicit reconciliation/link service from verified ExternalAccount to Account;
8. enforce one User ↔ one Account after duplicate preflight;
9. resolve authenticated Customer through Tenant → Connector → Account → Kimia AccountId;
10. keep Customer balances fail-closed until this resolver is verified;
11. only then route Kimia Read through tenant/connector-aware client construction;
12. Kimia Write remains separately blocked by Ground Truth.

## Link service safety contract

A future link/reconciliation service must:
- require explicit tenant and connector context;
- accept a verified external AccountId, never infer one from identity/contact fields;
- reject tenant/connector mismatch;
- reject an AccountId already linked to another platform account;
- reject relinking an established AccountId;
- be transactional;
- be idempotent for an identical already-established link;
- create audit evidence with actor, tenant, connector, platform user/account, external AccountId, trace/request identifiers and result;
- never mutate Money/Gold/Coin/Currency balances;
- perform no Kimia Write.

## Current classification

- Existing User→Account structural contract: REUSE AFTER FIX
- Existing Account representation: REUSE AFTER FIX
- Existing ExternalAccount synchronized projection: REUSE AFTER FIX
- Existing global KimiaClient/config: REFACTOR
- Historical Tenant foundation: REUSE AFTER FIX
- Tenant-owned KimiaConnector/Book runtime: NOT IMPLEMENTED IN CURRENT VERIFIED CANONICAL PATH
- Direct User→ExternalAccount binding: NOT APPROVED
- New binding entity: DEFER / REBUILD only if later requirements justify it
- Candidate A implementation: IMPLEMENTATION NOT STARTED

## Gate

No runtime change is approved by this document. Before implementation, V2-00 still needs exact migration/applied-state evidence, bounded table-group preflight design, and traceability from ADR-024/026 through schema/tests/CI. Kimia Write remains blocked.
