# GoldPlatform V2 — Customer ↔ Kimia Account Binding Drift

- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Classification: `REUSE AFTER FIX`
- Product behavior change: `NONE`
- Kimia Write: `UNCHANGED — BLOCKED BY GROUND TRUTH`

## Recovered owner-confirmed rule

Project Memory records the approved cardinality:

- one GoldPlatform login/account -> zero or one local Account -> zero or one Kimia AccountId
- one Kimia AccountId -> no more than one GoldPlatform login/account
- after approval/linking, the Kimia AccountId is unique and immutable for that platform account
- a second platform account for the same real customer requires a distinct mobile inside the tenant and a distinct Kimia AccountId

The canonical decision record named by Project Memory is:

`docs/ADR/ADR-024-platform-user-kimia-account-binding.md`

ADR-026 later clarifies tenant scope for mobile uniqueness; it does not replace the one-account-to-one-Kimia-AccountId binding rule.

## Original ADR recovered

The original accepted ADR exists on historical branch:

`work/product-kimia-next`

File:

`docs/ADR/ADR-024-platform-user-kimia-account-binding.md`

The ADR states that the current release resolves each authenticated platform account to at most one local account / Kimia AccountId, that one Kimia AccountId cannot be linked to more than one platform account, and that the established binding is immutable.

The ADR also records an unresolved architecture boundary: active Kimia synchronization writes `external_accounts`, while `users.account_id` targets `accounts`. Those two representations must not be silently merged without a separate code-path and migration audit.

## Current exact sync trace

Current canonical `kimia:sync-accounts` is implemented by:

`backend/app/Console/Commands/SyncKimiaAccountsCommand.php`

The command:

1. resolves the requested Kimia account types;
2. reads accounts through `KimiaAccountRepository::all($type)`;
3. adapts the Kimia DTO into local projection data;
4. searches `external_accounts` by `provider='kimia'` and Kimia `external_id`;
5. creates, updates, or skips rows according to a raw-response-derived sync hash;
6. records synchronization metadata including `sync_status`, `sync_error`, and `last_synced_at`.

A second current service, `KimiaAccountSyncService`, also writes Kimia account data to `ExternalAccount::updateOrCreate(...)` using the same provider/external-id identity and records `raw_data`, `sync_hash`, and `last_synced_at`.

Therefore the active Kimia account synchronization destination is verified as `external_accounts`.

## Recovered Project Memory update

The living Project Memory explicitly records the current sync contract:

- `kimia:sync-accounts` writes Kimia account data into `external_accounts` with provider `kimia`;
- it uses Kimia `AccountId` as the external identity;
- `Type` is used for `/api/account`, while `accountType` is used for `/api/account/groups`;
- the sync path is a local projection and must still be revalidated by controlled read-only runtime evidence.

Older Project Memory text that described direct `Account::updateOrCreate` behavior is historical drift and is not the current implementation source of truth.

## Historical implementation evidence recovered

The historical branch contains implementation aligned with ADR-024:

- `backend/app/Observers/UserObserver.php`
  - permits the first `account_id` link from null;
  - rejects later change or removal of an established binding.
- `backend/database/migrations/2026_08_03_120100_enforce_unique_user_account_binding.php`
  - checks for duplicate non-null `users.account_id` values before adding a unique index;
  - aborts instead of silently rewriting duplicates.
- `backend/tests/Feature/UserIdentityConstraintsTest.php`
  - verifies one local account cannot be linked to two users;
  - verifies an established binding cannot be changed or removed;
  - verifies synchronized Kimia identifiers cannot be changed.
- historical `Account` and `ExternalAccount` model changes add immutable Kimia identifier guards.

## Current canonical schema/model audit

Current `recovery/rc2-product-rebuild` evidence:

- `User::account()` is a `belongsTo(Account::class)` relation and `users.account_id` remains fillable.
- `Account::user()` is a `hasOne(User::class)` relation.
- `accounts.kimia_id` is database-unique.
- `users.account_id` is a nullable foreign key to `accounts`, but the current canonical migration does not make it unique.
- `external_accounts` separately enforces unique `(provider, external_id)` and is the active Kimia account-sync destination.
- `ExternalAccount` is a synchronization-oriented model containing `raw_data`, `sync_hash`, `sync_status`, `sync_error`, and `last_synced_at`.
- `ExternalAccount` remains a separate model from `Account`.
- the recovered `UserObserver` immutability guard is absent from the current canonical `UserObserver`.

Historical shop migration evidence confirms the original `users.account_id` foreign-key migration and `external_accounts` migrations had been applied, while the later ADR-024 unique-binding migration was only prepared and had not been applied at that checkpoint.

## ADR-026 / Tenant impact

Project Memory records ADR-026 as Accepted with these relevant constraints:

- GoldPlatform is White-label / Multi-tenant;
- the target schema uses explicit `tenant_id` ownership;
- mobile uniqueness is per tenant, not global;
- each tenant has its own verified Kimia connector/book boundary;
- several current Kimia identifiers and unique constraints are still global in the interim schema and cannot be assumed safe across independent tenant Kimia installations.

This means the current global uniqueness of `accounts.kimia_id` and `external_accounts(provider, external_id)` is not sufficient evidence for the final multi-tenant constraint shape. Tenant scoping must be incorporated by the reviewed tenancy table-group migration rather than silently patched inside this binding checkpoint.

## Verified responsibility split

The two current representations have different verified responsibilities:

### `external_accounts`

Classification: `REBUILDABLE KIMIA PROJECTION / SNAPSHOT`

Evidence:

- it is the active sync destination;
- rows are created/updated from Kimia reads;
- it stores raw synchronized data;
- it stores a sync hash and synchronization timestamp/status;
- it can therefore be reconstructed from Kimia read evidence.

It must not become the authenticated customer financial binding merely because it contains Kimia AccountId values.

### `accounts`

Classification: `INTENDED DURABLE CUSTOMER ↔ KIMIA BINDING MODEL — INCOMPLETE CARRY-FORWARD`

Evidence:

- `users.account_id` points to `accounts`;
- `Account::user()` / `User::account()` express one selected local financial context;
- `accounts.kimia_id` is the historical durable Kimia identifier;
- ADR-024 explicitly defines the one-user-to-one-Kimia-AccountId binding through this path;
- the historical uniqueness/immutability guard and tests were written for this binding.

However, current sync no longer populates `accounts`, and no verified canonical bridge currently resolves a synchronized `external_accounts` row into the `accounts` row referenced by an authenticated user.

Therefore the intended durable model is recoverable, but the current runtime mapping path is not complete.

## Architecture decision boundary

No new owner decision is required to state the responsibility split above because it follows directly from accepted ADR-024, Project Memory, and current code.

A future implementation decision is still required for the **bridge** between the two representations. Safe options may include adapting the current `accounts` row from verified synchronized Kimia identity or introducing an explicit tenant-scoped mapping relation, but no option is selected here because it affects schema, tenant isolation, migration/backfill, authorization, and reconciliation.

This checkpoint does not:

- merge `accounts` and `external_accounts`;
- copy synchronized rows into `accounts`;
- introduce a new foreign key;
- modify global unique constraints;
- apply a migration;
- enable Kimia Write.

## Historical ADR-024 implementation classification

Capability: Customer ↔ Kimia Account binding

Overall status: `REUSE AFTER FIX`

Component classification:

- ADR-024 business/cardinality rule: `REUSE AS-IS` subject to ADR-026 tenant scope.
- `UserObserver` established-binding immutability guard: `REUSE AFTER FIX` because the rule remains valid but current tenant/account resolution and audit behavior must be integrated with canonical architecture.
- `users.account_id` unique migration: `REFACTOR` because duplicate preflight remains correct in principle, but final uniqueness and migration ordering must be validated against the accepted tenant migration/table group and current deployed schema.
- historical identity feature tests: `REUSE AFTER FIX` because assertions remain valuable, but fixtures must cover current tenant isolation, current models, and the synchronized-projection boundary.
- historical branch as a whole: `HISTORICAL ONLY` for direct integration; no blind cherry-pick or merge.

## Current customer balance behavior

Customer financial balances must remain fail-closed until all of these are verified for the authenticated request:

1. authenticated user;
2. verified Host Tenant / user-tenant equality;
3. exactly one authorized durable customer account binding;
4. exactly one verified Kimia AccountId for that binding;
5. read-only balance retrieval from Kimia for that AccountId.

No Money/Gold/Coin/Currency value may be substituted from Wallet/Ledger/Projection and unavailable values must not become zero.

Therefore:

- financial balance authority remains Kimia;
- customer financial display remains blocked when verified binding resolution is unavailable;
- `external_accounts` may support discovery/reconciliation but cannot independently authorize a customer's balance;
- Custody remains independent and GoldPlatform-owned.

## Canonical drift verified

The original ADR-024 file is absent from the expected current canonical path.

Historical branch comparison shows `work/product-kimia-next` is heavily diverged from current Recovery canonical. Therefore:

- no direct merge;
- no blind cherry-pick;
- no migration application;
- no copy of the whole historical branch.

## Safe next work

1. recover/compare the current tenancy root, connector, and user table-group implementation against ADR-026;
2. inventory every current runtime consumer of `users.account_id`, `accounts.kimia_id`, and `external_accounts.external_id`;
3. verify whether any current authenticated customer balance resolver already bridges the two representations;
4. if no bridge exists, document the candidate bridge contracts and migration/backfill preconditions without applying them;
5. keep customer balances fail-closed and Kimia Write deny-by-default.

No financial rule, Kimia payload, permission, API behavior, Kimia Write, or applied migration is changed by this evidence document.

## Handoff checkpoint

The durable-vs-projection responsibility split is now evidence-backed:

- `accounts` = intended durable authenticated customer binding;
- `external_accounts` = rebuildable Kimia synchronization projection;
- the missing verified bridge between them is the current canonical gap.

V2-00 remains `GATE NOT PASSED` until that authenticated mapping path and the broader source-recovery gates are closed.
