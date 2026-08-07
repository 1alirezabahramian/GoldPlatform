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

The ADR also records an unresolved architecture boundary: active Kimia synchronization writes `external_accounts`, while the current `users.account_id` foreign key targets `accounts`. Those two representations must not be silently merged without a separate code-path and migration audit.

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

## Canonical drift verified

Current `recovery/rc2-product-rebuild` still has `UserObserver`, but the recovered `account_id` immutability guard is absent from that canonical file.

The original ADR-024 file is also absent from the expected canonical path.

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
- current multi-tenancy and account-model boundaries require comparison before integration.

This is **not** `NOT IMPLEMENTED` and does not require redesign from zero.

## Current customer balance behavior

Recovery evidence for PR #186 confirms dashboard/assets remain fail-closed until the authenticated customer resolves to a verified Kimia account. No Money/Gold/Coin/Currency value may be substituted from Wallet/Ledger/Projection and unavailable values must not become zero.

Therefore:

- financial balance authority remains Kimia;
- customer financial display remains blocked when verified binding resolution is unavailable;
- Custody remains independent and GoldPlatform-owned.

## Safe next work

Before canonical integration:

1. compare current `users`, `accounts`, and `external_accounts` schema/models with ADR-024/ADR-026;
2. determine whether the historical unique-index migration remains valid under the current tenant model;
3. recover/adapt immutability guards without restoring obsolete wallet/customer behavior;
4. adapt targeted tests to current canonical models and run them;
5. keep balance resolution fail-closed until that exact path is verified.

No financial rule, Kimia payload, permission, API behavior, Kimia Write, or applied migration is changed by this evidence document.
