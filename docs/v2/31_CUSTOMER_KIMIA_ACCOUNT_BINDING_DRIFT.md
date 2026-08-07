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

## Historical implementation evidence recovered

The same historical branch contains implementation aligned with ADR-024:

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
- `external_accounts` separately enforces unique `(provider, external_id)` and is the active Kimia account-sync destination recorded by Project Memory.
- `ExternalAccount` remains a separate model from `Account`.
- the recovered `UserObserver` immutability guard is absent from the current canonical `UserObserver`.

Historical shop migration evidence confirms the original `users.account_id` foreign-key migration and `external_accounts` migrations had been applied, while the later ADR-024 unique-binding migration was only prepared and had not been applied at that checkpoint.

## Verified architecture boundary

The current repository therefore contains two distinct Kimia-account representations:

1. `accounts.kimia_id` — the target of `users.account_id` and the historical customer binding model;
2. `external_accounts(provider='kimia', external_id=AccountId)` — the active synchronized external-account snapshot path.

Project Memory explicitly records that active Kimia account synchronization writes `external_accounts` and that these two representations were not yet consolidated.

This checkpoint does not choose one representation, merge them, add a new foreign key, or migrate customer bindings. That would be an architecture/database change requiring a dedicated comparison of current sync, tenant, authorization, migration and reconciliation paths.

## Canonical drift verified

The original ADR-024 file is absent from the expected current canonical path.

Historical branch comparison shows `work/product-kimia-next` is heavily diverged from current Recovery canonical (`6 ahead / 577 behind` at inspection). Therefore:

- no direct merge;
- no blind cherry-pick;
- no migration application;
- no copy of the whole historical branch.

## Classification

Capability: Customer ↔ Kimia Account binding

Status: `REUSE AFTER FIX`

Reason:

- business decision recovered and owner-confirmed;
- original accepted ADR recovered;
- historical migration, guard and tests recovered;
- current canonical carry-forward is incomplete;
- current canonical still preserves the underlying `User -> Account -> kimia_id` structure;
- active Kimia sync uses `external_accounts`, creating a real unresolved representation boundary;
- current multi-tenancy and database constraints must be compared before integration.

This is **not** `NOT IMPLEMENTED` and does not require redesign from zero.

## Current customer balance behavior

Recovery evidence for PR #186 confirms dashboard/assets remain fail-closed until the authenticated customer resolves to a verified Kimia account. No Money/Gold/Coin/Currency value may be substituted from Wallet/Ledger/Projection and unavailable values must not become zero.

Therefore:

- financial balance authority remains Kimia;
- customer financial display remains blocked when verified binding resolution is unavailable;
- Custody remains independent and GoldPlatform-owned.

## Safe next work

The next checkpoint must begin with architecture comparison, not implementation:

1. trace the current `kimia:sync-accounts` implementation and its `external_accounts` write path on current canonical;
2. inspect current ADR-026 / Tenant implementation and determine the intended scope of identity uniqueness;
3. compare `accounts` and `external_accounts` responsibilities and decide whether one is canonical mapping and the other a rebuildable snapshot, or whether an explicit mapping relation is required;
4. only after that decision, adapt the historical immutability guard and unique-binding test/migration to the current schema;
5. keep customer financial balance resolution fail-closed until the exact authenticated mapping path is verified.

No financial rule, Kimia payload, permission, API behavior, Kimia Write, or applied migration is changed by this evidence document.

## Handoff checkpoint

This is a safe conversation handoff point after exact-head CI passes: the original business decision and historical implementation have been recovered, current schema drift has been identified, destructive integration has been avoided, and the next work is a bounded architecture comparison with a clearly documented stop condition.
