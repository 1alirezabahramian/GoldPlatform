# GoldPlatform V2 — Recovery PR Ledger Slice 07

- Stage: `V2-00`
- Scope: PRs `#151` through `#156`
- Purpose: extend the repository evidence ledger with immutable PR metadata and exact historical Head-SHA CI evidence.
- Safety: documentation only; no product behavior, financial rule, Kimia Write, migration, API, OpenAPI, permission or frontend behavior change.

## Result

All six PRs in this bounded slice are `CLOSED — MERGED`. Exact historical Head-SHA CI was found for each PR and every mapped run completed successfully.

| PR | Title / capability | Base SHA | Head SHA | Merge SHA | Exact-Head CI | Classification |
|---|---|---|---|---|---|---|
| #151 | Customer read resources consolidation | `6e1ff7e536328dac34976308205757bf8a3ade03` | `67391d11e7893db58c6a70ad97cac7196d14aec4` | `50720724c5315510537da5071499888c0446f264` | Backend RC1 Validation #220 — `EXECUTED — PASS` | `MERGED — CLOSURE PENDING` |
| #152 | Canonical project-state alignment | `50720724c5315510537da5071499888c0446f264` | `39a80210161df23cb135a085b45047a066c7d743` | `3bebc19b519b402a0d41af95a060316584ea75c7` | Backend RC1 Validation #222 — `EXECUTED — PASS` | `MERGED — CLOSURE PENDING` |
| #153 | Internal balance projection authority guard | `3bebc19b519b402a0d41af95a060316584ea75c7` | `7f2121d50b76b86bb1bfed1ef1a155a84523a28f` | `c38cc704635fabbd617865a2c950519de9e16dc3` | Backend RC1 Validation #224 — `EXECUTED — PASS` | `MERGED — CLOSURE PENDING` |
| #154 | Disable internal customer wallet mutations | `c38cc704635fabbd617865a2c950519de9e16dc3` | `42a398277a775d29198feb3d01918e855b65ba83` | `3199560986471a1620462d8f9c0bf42ca502dc12` | Backend RC1 Validation #226 — `EXECUTED — PASS` | `MERGED — CLOSURE PENDING` |
| #155 | Guard settlement completion from ledger-only evidence | `3199560986471a1620462d8f9c0bf42ca502dc12` | `0e82e74811d5cd968380b88231dba24975badc9b` | `c55292fad02b116e8c8aded7670ed22e20c7a589` | Backend RC1 Validation #230 — `EXECUTED — PASS` | `MERGED — CLOSURE PENDING` |
| #156 | Keep balance reservations workflow-only | `c55292fad02b116e8c8aded7670ed22e20c7a589` | `ac465fa6c7584b9b6e739a2daf24df2ef603d8a3` | `89b2f78f75e87e691876432e5c424998b616ff5b` | Backend RC1 Validation #232 — `EXECUTED — PASS` | `MERGED — CLOSURE PENDING` |

## Sequential recovery chain

The metadata exposes an important ordered sequence:

`PR #150 merge` → `#151 Customer Resources` → `#152 Project State Alignment` → `#153 Projection Guard` → `#154 Wallet Mutation Guard` → `#155 Settlement Ledger Guard` → `#156 Reservation Authority Guard`

For PRs #152 through #156, each recorded Base SHA is the previous slice's integrated canonical point. This is evidence of an intentionally staged recovery chain rather than unrelated historical branches.

## Capability meaning

### #151 — Customer read resources

Preserves customer ownership and pagination while replacing generic presentation with explicit API Resources. Decimal/quantity values remain strings at the API boundary and internal/operator/Kimia identifiers are excluded from customer responses.

### #152 — Project-state authority correction

Corrects documentation that previously described ledger-derived balances as final. Records Kimia as final authority for Money, Gold, Coin and Currency, GoldPlatform as authority for physical Custody, and internal Ledger/Journal/Projection as audit/workflow/reconciliation evidence only.

### #153 — Projection guard

Marks `BalanceProjectionService` as `audit_reconciliation_only` and explicitly not a customer balance source.

### #154 — Wallet mutation guard

Disables legacy internal `WalletService::deposit` / `withdraw` customer financial mutations. The service remains only as a compatibility shell and fails closed.

### #155 — Settlement ledger guard

Prevents a balanced internal Ledger from being treated as proof that a customer financial settlement completed. Legacy trade execution is blocked until verified Kimia result evidence exists.

### #156 — Reservation authority guard

Keeps reservation as idempotent workflow intent and removes customer balance sufficiency decisions derived from internal projections.

## Safety boundaries preserved across the slice

- No Kimia Write was enabled.
- No Action Code or financial formula was introduced.
- No customer final balance authority was assigned to Wallet/Ledger/Projection/Reservation.
- No migration was introduced by the financial-authority guard PRs in this slice.
- Historical data and compatibility shells were preserved instead of deleted.

## What this slice proves

- PR metadata for `#151..#156`: `VERIFIED — EXECUTED`.
- Merge state for all six: `CLOSED — MERGED`.
- Exact historical Head-SHA CI for all six: `EXECUTED — PASS`.
- Recovery sequencing: `VERIFIED` from recorded Base/Head/Merge metadata.

## What this slice does not prove

- It does not close every capability end-to-end.
- It does not authorize Kimia Write.
- It does not prove current production deployment or real Kimia credentials.
- It does not replace current canonical file/behavior inspection.
- It does not resolve authenticated customer-to-Kimia account mapping, pricing, quote, Weight750, Coin/Currency write, or production operations evidence.

## V2-00 status

`V2-00 — GATE NOT PASSED — CONTINUE STRICT EVIDENCE RECOVERY`

This slice narrows the wider PR/SHA/CI ledger gap but does not close the full repository ledger.
