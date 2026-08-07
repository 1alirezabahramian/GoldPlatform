# GoldPlatform V2 — Authenticated Customer → Kimia Resolution Audit

- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Classification: `REUSE AFTER FIX`
- Product behavior change: `NONE`
- Kimia Write: `UNCHANGED — BLOCKED BY GROUND TRUTH`
- Financial balance authority: `KIMIA`

## Verified current state

The canonical Backend already contains a read-only Kimia balance path:

- `App\Repositories\Kimia\Read\BalanceReadRepository::forAccount(int $accountId)`
- endpoint: `GET /api/voucher/balance/{AccountId}`
- errors are not silently converted into customer balances.

Therefore the missing capability is not the Kimia read adapter itself.

The missing capability is the verified resolution path from the authenticated GoldPlatform customer to the correct Kimia `AccountId`.

## Customer HTTP boundary

Current canonical Customer V1 routes are authenticated through Sanctum and the `customer` role.

The current financial endpoints intentionally fail closed:

- `GET /api/v1/customer/dashboard`
- `GET /api/v1/customer/assets`
- `GET /api/v1/customer/assets/money`
- `GET /api/v1/customer/assets/gold`
- `GET /api/v1/customer/assets/coins`
- `GET /api/v1/customer/assets/currencies`

They return HTTP 503 with `KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED` rather than Wallet/Ledger/Projection values or invented zero balances.

This is verified current Backend behavior, not only frontend copy or documentation.

## Current identity/binding structures

Canonical models still expose:

`Authenticated User -> users.account_id -> Account -> accounts.kimia_id`

This is the existing customer-binding anchor represented by the schema/model layer.

Separately, active `kimia:sync-accounts` synchronization writes Kimia account snapshots into:

`external_accounts(provider='kimia', external_id=AccountId)`

with sync-specific state such as:

- `raw_data`
- `sync_hash`
- `sync_status`
- `sync_error`
- `last_synced_at`

This establishes `external_accounts` as a rebuildable synchronized representation of Kimia account data.

## Important unresolved bridge

No verified canonical Customer resolver has been found that performs this complete sequence:

1. obtain the authenticated Customer User;
2. require an established local account binding;
3. resolve the immutable Kimia `AccountId`;
4. verify that the synchronized Kimia identity corresponds to that binding where reconciliation is required;
5. pass the verified AccountId to `BalanceReadRepository`;
6. return customer-safe Money/Gold/Coin/Currency resources.

Until this resolver exists and is tested, the current 503 fail-closed state is correct and must remain.

## `accounts` versus `external_accounts`

Evidence supports the following responsibility split without silently redesigning the database:

### `external_accounts`

Classification: `REBUILDABLE KIMIA SYNC SNAPSHOT / PROJECTION`

Reason:

- it is populated by `kimia:sync-accounts`;
- identity is keyed by `(provider, external_id)`;
- its fields explicitly include synchronization metadata and raw payload state;
- it can be refreshed from Kimia.

### `accounts`

Classification: `CURRENT CUSTOMER BINDING ANCHOR — POPULATION/RECONCILIATION PATH STILL REQUIRES VERIFICATION`

Reason:

- `User::account()` targets `Account`;
- `Account` stores `kimia_id`;
- historical accepted ADR-024 defines the durable one-user-to-one-Kimia-AccountId binding through this relationship;
- `accounts.kimia_id` is unique in the current schema;
- the exact current mechanism that creates/reconciles `accounts` from synchronized Kimia identities has not yet been verified in this V2 recovery checkpoint.

Therefore `accounts` must not yet be declared a rebuildable snapshot, and `external_accounts` must not silently replace the authenticated binding relation.

## Tenant scope

Current canonical evidence does not show a completed Tenant-scoped identity model on `User`.

Recent recovery security work explicitly describes current customer isolation as owner-based and avoids inventing a Tenant model. Historical Admin/Operator discovery work likewise recorded that Tenant/Company scoping was not yet canonical product architecture.

ADR-026 remains relevant as target scope clarification for future `(tenant_id, mobile)` uniqueness, but it does not by itself prove that tenant-scoped user/account constraints are implemented on the current canonical branch.

Consequences:

- do not change current global constraints as part of this V2-00 evidence slice;
- do not add tenant-aware Kimia uniqueness without a dedicated schema/backfill audit;
- do not infer that a Kimia `AccountId` is globally unique across future independent Tenant/Kimia contexts.

## Historical ADR-024 implementation classification

### Business rule

`REUSE AS-IS`

- one platform login/account -> at most one Kimia AccountId;
- one Kimia AccountId -> at most one platform account in the active binding scope;
- established binding is immutable.

### `UserObserver` immutability guard

`REUSE AFTER FIX`

The behavior remains aligned with the accepted rule, but integration must be adapted to current observer responsibilities and tested against the current schema.

### unique `users.account_id` migration

`REFACTOR`

The preflight duplicate check is valuable, but the migration must not be applied until current production/applied-migration evidence, Tenant scope, duplicate state, and rollback behavior are verified.

### `UserIdentityConstraintsTest`

`REUSE AFTER FIX`

The assertions remain useful, but fixtures and expected scope must be aligned to the current canonical models and future Tenant boundary.

## Verified gap statement

Capability: Authenticated Customer → verified Kimia AccountId → Kimia financial balance read

Status: `REUSE AFTER FIX`

Already present:

- authenticated Customer route boundary;
- Kimia read client/repositories;
- read-only balance endpoint adapter;
- existing `User -> Account -> kimia_id` binding shape;
- synchronized `external_accounts` snapshot;
- fail-closed Customer financial HTTP behavior;
- historical accepted binding rule and safety guards.

Missing / not yet verified:

- canonical resolver service joining authenticated User to a verified Kimia AccountId;
- verified reconciliation contract between `accounts.kimia_id` and `external_accounts(provider='kimia', external_id)`;
- current population lifecycle for `accounts`;
- Tenant-scoped uniqueness/backfill implementation;
- customer-safe balance mapper/resource contract fed by real Kimia reads;
- feature/architecture tests proving wrong-user/wrong-account/cross-customer binding rejection.

## Safety decision

Customer financial endpoints remain fail-closed until the resolver is complete and verified.

No Wallet/Ledger/Projection fallback is allowed.
No unavailable value becomes zero.
No frontend financial calculation is allowed.
No Kimia Write is enabled.
No migration is applied by this checkpoint.

## Next bounded work

1. trace every canonical writer/creator/updater of `Account` and `users.account_id`;
2. determine whether any current service already reconciles `Account.kimia_id` with `ExternalAccount.external_id`;
3. inspect registration/linking flows for how the first binding is intended to be established;
4. compare current database/applied-migration evidence before adapting ADR-024 constraints;
5. only after those checks, define the smallest resolver service and tests without activating financial writes.
