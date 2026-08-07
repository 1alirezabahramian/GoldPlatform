# GoldPlatform V2 — Customer ↔ Kimia Binding Writer Audit

- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Product behavior change: `NONE`
- Kimia Write: `UNCHANGED — BLOCKED BY GROUND TRUTH`
- Migration application: `NONE`

## Purpose

Determine whether the current canonical code already contains a verified writer/resolver that links an authenticated GoldPlatform customer to a stable Kimia `AccountId`, without assuming that Kimia account synchronization itself performs that binding.

## Current canonical facts

1. `User::account()` still resolves through nullable `users.account_id` to the local `accounts` model.
2. `Account` carries `kimia_id`; the customer-binding design recovered in ADR-024 is therefore still structurally represented.
3. `kimia:sync-accounts` does **not** write `accounts` or `users.account_id`. It synchronizes Kimia account snapshots into `external_accounts(provider='kimia', external_id=...)` with raw payload, sync hash, sync state and `last_synced_at`.
4. `BalanceReadRepository::forAccount(int $accountId)` already provides the accepted read path to `/api/voucher/balance/{AccountId}`.
5. Customer Dashboard and Money/Gold/Coin/Currency endpoints remain intentionally fail-closed with `KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED` until authenticated account resolution exists.
6. Current registration creates the local User and compatibility Wallet records but leaves `Create Kimia Account`, `Link Account`, and `Assign Default Group` as explicit TODOs.

## Writer search result — bounded evidence

Repository code search for direct `account_id`, `Account::updateOrCreate`, and combined `kimia_id` / `ExternalAccount` writer patterns did not return a current canonical customer-linking service.

This is **not** treated as proof of absence. Per V2 recovery rules, a failed first search is only negative evidence and requires historical/code-path cross-checking.

## Historical cross-check

ADR-024 historical evidence on `work/product-kimia-next` contains constraints, not a complete linking workflow:

- `UserObserver::updating()` allows the first `account_id` assignment from null and rejects subsequent replacement/removal.
- the prepared migration rejects duplicate non-null `users.account_id` values before adding the unique index.
- `Account` prevents mutation of an established `kimia_id`.
- `ExternalAccount` prevents mutation of `(provider, external_id)` identity.
- `UserIdentityConstraintsTest` creates `Account` rows directly in the test, then assigns `user.account_id` directly to prove one-to-one/immutability constraints.

The historical test therefore proves the intended constraint contract but does **not** prove existence of an application service that selects, verifies, links or reconciles an authenticated customer against a synchronized Kimia account.

## Historical Kimia sync distinction

Older Kimia integration history also shows Account Group synchronization and later account-read work, but those are not evidence of authenticated customer binding. Current canonical account synchronization remains the `external_accounts` snapshot path.

## Classification

### Customer ↔ Kimia constraint contract
`REUSE AFTER FIX`

Reason: the one-to-one and immutable rules remain valid, but tenant scope/current schema must be adapted before enforcement is integrated.

### Historical User/Account/ExternalAccount guards
`REUSE AFTER FIX`

Reason: behavior is useful but must be adapted to current canonical models, tenant scope, sync/reconciliation behavior and exact migration state.

### Historical unique `users.account_id` migration
`REFACTOR`

Reason: the duplicate-abort safety pattern is reusable, but applying a global unique constraint before the current tenant/table-group migration and production-data audit would be an unsafe architecture/database change.

### Authenticated Customer → Kimia resolver/linking workflow
`NOT VERIFIED — CONTINUE RECOVERY`

No verified current canonical application service has yet been recovered that:

1. receives the authenticated customer context;
2. selects exactly one authorized Kimia `AccountId`;
3. verifies that identity against synchronized/read Kimia evidence;
4. transactionally rejects an already-bound account;
5. persists the stable local binding;
6. records auditable link evidence;
7. returns the verified `AccountId` to the Kimia balance read boundary.

This is deliberately **not** classified `NOT IMPLEMENTED` until broader historical branches/PRs and migration/application-service evidence are exhausted.

## Safety consequence

Until that resolver/link workflow is verified:

- `/api/v1/customer/dashboard` stays fail-closed;
- `/api/v1/customer/assets*` stays fail-closed;
- Wallet/Ledger/Projection remains forbidden as a customer balance fallback;
- unavailable financial values must not become zero;
- no Kimia account-creation POST is enabled;
- no `users.account_id` migration is applied;
- no automatic `accounts` ↔ `external_accounts` merge/reconciliation is introduced.

## Next recovery slice

1. inspect historical branches/commits around customer registration, account synchronization and identity linking for any application-level Link/Resolver service;
2. inspect current/historical migrations for the exact creation and uniqueness lifecycle of `accounts`, `users.account_id`, and `external_accounts`;
3. inspect tests that exercise authenticated customer account resolution rather than only constraint behavior;
4. compare tenant/white-label direction before choosing uniqueness scope;
5. only after evidence exhaustion classify the missing resolver as `REUSE`, `REFACTOR`, or `NOT IMPLEMENTED`.

No architecture choice, financial rule, Kimia payload, Kimia Write, API behavior, permission, or applied database migration is changed by this audit.
