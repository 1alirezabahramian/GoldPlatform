# GoldPlatform V2 — Customer ↔ Kimia Account Binding Drift

- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Classification: `RECOVERED DECISION — CANONICAL CARRY-FORWARD MISSING`
- Product behavior change: `NONE`
- Kimia Write: `UNCHANGED — BLOCKED BY GROUND TRUTH`

## Recovered owner-confirmed rule

Project Memory records the approved cardinality:

- one GoldPlatform login/account -> zero or one local Account -> zero or one Kimia AccountId
- one Kimia AccountId -> no more than one GoldPlatform login/account
- after approval/linking, the Kimia AccountId is unique and immutable for that platform account
- a second platform account for the same real customer requires a distinct mobile inside the tenant and a distinct Kimia AccountId

Project Memory identifies the original decision record as:

`docs/ADR/ADR-024-platform-user-kimia-account-binding.md`

## Current canonical drift

Inspection of `recovery/rc2-product-rebuild` did not find the referenced ADR file at the expected path.

This does **not** invalidate the recovered owner-confirmed rule. It means the current canonical repository does not presently carry the referenced decision document at that path.

Classification:

`DOCUMENTATION DRIFT — CARRY-FORWARD REQUIRED`

No ADR is recreated from memory in this slice because the original wording/source history should be recovered first where possible.

## Current customer balance behavior

Historical Recovery evidence for PR #186 confirms the customer dashboard/assets remain fail-closed until the authenticated customer is resolved to a verified Kimia account. No Money/Gold/Coin/Currency value is substituted from Wallet/Ledger/Projection and unavailable values are not converted to zero.

Therefore:

- Financial balance authority remains Kimia.
- Customer balance display remains blocked when binding resolution is unavailable.
- Custody remains independent and GoldPlatform-owned.

## V2-00 impact

The blocker is no longer classified as an unknown business rule. The rule is recovered from Project Memory, while canonical implementation/document carry-forward remains to be verified.

Next evidence work:

1. recover the original ADR-024 source/history if present in historical branches/commits/files;
2. verify current `User -> Account -> kimia_id` implementation and schema against that decision;
3. verify uniqueness/immutability guards and tests;
4. only then classify the binding capability for canonical integration.

No financial rule, Kimia payload, migration, permission, API or product behavior is changed by this document.
