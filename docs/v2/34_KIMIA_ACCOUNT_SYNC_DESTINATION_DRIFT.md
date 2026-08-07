# GoldPlatform V2 — Kimia Account Sync Destination Drift

- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Classification: `REUSE AFTER FIX`
- Product behavior change: `NONE`
- Kimia Write: `UNCHANGED — BLOCKED BY GROUND TRUTH`

## Finding

Historical evidence shows two materially different implementations of `kimia:sync-accounts` across project history.

### Earlier runtime evidence

A preserved shop/runtime search output from July 2026 shows an earlier command:

`backend/app/Console/Commands/KimiaSyncAccounts.php`

That command synchronized Kimia accounts into the local `accounts` model and assigned:

`accounts.kimia_id = Kimia AccountId`

This matters because `users.account_id` targets `accounts`, so the earlier account-sync destination naturally populated the model used by the authenticated customer binding design.

The same preserved runtime evidence also shows the registration flow still contained a TODO for creating/linking a Kimia account; therefore account synchronization and user-to-account linking were separate concerns even in that older architecture.

### Later / recovered architecture

The historical `work/product-kimia-next` branch contains `SyncKimiaAccountsCommand`, whose explicit description is:

`Synchronize Kimia accounts with the local external_accounts table`

It reads `/api/account` using `Type`, deduplicates by `AccountId`, and creates/updates `ExternalAccount` rows keyed by:

- `provider = kimia`
- `external_id = AccountId`

It stores synchronized metadata including raw source data, sync hash, sync status and last-sync timestamp.

The current Recovery canonical keeps the same architectural direction: active `kimia:sync-accounts` writes `external_accounts` rather than `accounts`.

## Architecture interpretation

The historical evidence establishes a real destination change:

`Kimia Account API -> accounts` (older implementation)

became

`Kimia Account API -> external_accounts` (later/current implementation)

This change is not equivalent to a harmless rename because:

- `users.account_id` still targets `accounts`;
- ADR-024 binding rules use `User -> Account -> kimia_id` as the customer financial binding model;
- `external_accounts` is a separate synchronized representation with source payload/hash/timestamp fields;
- no verified bridge or reconciliation relation between the two representations has yet been recovered.

Therefore the authenticated Customer -> Kimia AccountId resolution gap is partly explained by an incomplete carry-forward after the sync destination changed.

## What is NOT proven

This checkpoint does not prove why the destination changed, does not prove the old `accounts` sync should be restored, and does not prove that `external_accounts` should become the direct user binding target.

No automatic matching by mobile, national code, name or account code is accepted. Project Memory requires verified identity evidence and explicit review for mapping; zero duplicate/orphan counts do not authorize automatic mapping.

## Classification

### Earlier `accounts` sync

Status: `HISTORICAL ONLY / REUSE CONCEPT AFTER ARCHITECTURE REVIEW`

Reason:
- it demonstrates how `accounts.kimia_id` was populated historically;
- blindly restoring it would create a second active sync destination and risk duplicate sources of local representation;
- current sync/reconciliation architecture has moved to `external_accounts`.

### Current `external_accounts` sync

Status: `REUSE AS-IS` for read-only Kimia discovery/snapshot responsibility.

Reason:
- explicit provider/external identity;
- source payload/hash/sync timestamp;
- rebuildable synchronized representation;
- no customer balance authority claim.

### Customer -> Kimia binding bridge

Status: `NOT VERIFIED — CONTINUE RECOVERY`

The repository still needs a verified, tenant-safe, auditable mapping/resolution path from authenticated `User` through the approved binding model to the synchronized Kimia AccountId.

## Safety boundary

Until that bridge is verified:

- Customer financial Dashboard/Assets remain fail-closed;
- Wallet/Ledger/Projection cannot substitute for Kimia balances;
- no name/mobile/national-code auto-linking;
- no Kimia account creation;
- no Kimia Write;
- no migration or foreign-key rewrite;
- no blind reuse of the older sync command.

## Next safe recovery work

1. recover the exact older `KimiaSyncAccounts` implementation/commit if available and compare all populated `accounts` fields with the current `ExternalAccount` adapter;
2. search historical/current code for a service or command that links `users.account_id` after account sync;
3. inspect database/runtime evidence for existing `accounts` rows versus `external_accounts` rows and duplicate/orphan counts before any schema proposal;
4. trace ADR-026 Tenant/connector implications for account identity scope;
5. only then propose a non-destructive mapping/reconciliation design, with no applied migration until reviewed.
