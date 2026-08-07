# GoldPlatform V2 — Customer/Kimia Bridge Schema Audit

- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Classification: `BLOCKED BY VERIFIED ARCHITECTURE GAP — DESIGN NOT YET SELECTED`
- Product behavior change: `NONE`
- Migration applied: `NO`
- Kimia Write: `UNCHANGED — BLOCKED BY GROUND TRUTH`

## Result

The authenticated customer-to-Kimia AccountId gap is now verified across current code, current migrations, historical runtime evidence and Project Memory.

The current schema contains two independent local representations:

1. `accounts`
   - `kimia_id` is unique;
   - `users.account_id` is a nullable foreign key to `accounts.id`;
   - this is the model used by the accepted ADR-024 customer binding contract.

2. `external_accounts`
   - keyed by `(provider, external_id)`;
   - contains synchronized external metadata, sync hash, raw payload and last sync timestamp;
   - current `kimia:sync-accounts` writes here;
   - contains no `user_id`, `account_id`, bridge foreign key or explicit mapping relation.

There is no current schema relation from `external_accounts` to `accounts` or from `external_accounts` to `users`.

## Runtime evidence

Historical shop Docker evidence confirms both `accounts` and `external_accounts` existed simultaneously in the same database and both migrations had been applied.

The same runtime evidence shows synchronized Kimia retail accounts present in `external_accounts`, including AccountId `350`.

Earlier Project Memory records `Account::count() = 0` during the stabilization period while `external_accounts` became the active synchronization target. This is consistent with the later customer-resolution blocker.

No runtime evidence recovered in this checkpoint proves that the two tables were reconciled or linked.

## Historical destination change

Earlier runtime code synchronized Kimia accounts directly into `accounts` using:

`accounts.kimia_id = Kimia AccountId`

Later/current synchronization writes the same external identity into:

`external_accounts(provider='kimia', external_id=Kimia AccountId)`

The customer-binding foreign key remained:

`users.account_id -> accounts.id`

No migration recovered in the current schema adds a replacement bridge after this destination change.

## What this proves

The current Customer financial 503 blocker is not caused by a missing Kimia balance endpoint. The read repository already accepts a Kimia AccountId.

The missing verified capability is:

`Authenticated User -> approved local binding -> verified Kimia AccountId`

The schema currently does not provide a verified path from the active synchronized `external_accounts` rows into the accepted `User -> Account` binding model.

## What this does NOT authorize

This finding does not authorize any of the following:

- restoring the older dual-write sync into `accounts`;
- binding users directly to `external_accounts`;
- matching by mobile, national code, name or account code;
- copying all `external_accounts` into `accounts`;
- adding a migration without tenant/connector scope review;
- creating Kimia accounts;
- enabling any Kimia Write.

Project Memory explicitly requires the consolidation of these models to receive a separate code-path and migration audit.

## Tenant impact

ADR-026 changes the target architecture to explicit multi-tenancy. Global Kimia identifiers cannot automatically be treated as globally unique across independent tenant Kimia installations.

Therefore any future bridge must be tenant/connector safe. A final uniqueness key must not be inferred from the current single-tenant `(provider, external_id)` constraint alone.

## Classification

### `external_accounts`

`REUSE AS-IS` for read-only synchronized external discovery/snapshot responsibility.

### `accounts`

`REUSE AFTER FIX` as the accepted customer binding anchor, subject to tenant-safe carry-forward and verified population/reconciliation behavior.

### `users.account_id -> accounts`

`REUSE AFTER FIX`; one-to-one/immutability rules are accepted, but current canonical enforcement and tenant scope remain incomplete.

### Bridge / reconciliation capability

`NOT IMPLEMENTED IN CURRENT VERIFIED SCHEMA`

This classification is now stronger than `NOT VERIFIED` for the schema layer specifically: the current migrations contain no bridge relation.

Application-level historical code recovery must still continue before declaring that no service/workflow ever existed elsewhere in project history.

## Safe next steps

1. inspect historical branches/PRs for any explicit account-link service, admin/customer linking workflow or reconciliation command;
2. recover applied/live counts for `accounts`, linked `users.account_id`, and `external_accounts` from existing reports if present;
3. inspect ADR-026 implementation status for Tenant/connector identity scope;
4. draft candidate bridge designs only after those evidence checks;
5. no migration or Kimia Write until the architecture choice is explicit and reviewed.

## Current customer financial behavior

Remain fail-closed:

- Money/Gold/Coin/Currency balances unavailable until verified account resolution exists;
- no Wallet/Ledger/Projection substitution;
- no fake zero;
- no frontend financial calculation.
