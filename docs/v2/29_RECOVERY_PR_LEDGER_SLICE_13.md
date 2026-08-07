# Recovery PR Ledger Slice 13 — PRs #191..#194

## Status

VERIFIED — HISTORICAL DEMO PR METADATA + EXACT-HEAD BACKEND CI

## Purpose

Classify the final recovery-era PRs #191..#194 without allowing static visual previews to become product evidence.

## Classification rule

All static HTML / GitHub Pages demos in this sequence are:

`SUPERSEDED — TECHNICAL PREVIEW ONLY — NOT PRODUCT EVIDENCE`

A green CI result for these PRs proves only that the isolated demo safety checks and repository regression gate passed. It does not prove production frontend behavior, Backend integration, authentication, Kimia integration, real financial data, or customer transaction capability.

## PR ledger

| PR | Scope | Base SHA | Head SHA | Merge SHA | Exact-head CI | Product classification |
|---|---|---|---|---|---|---|
| #191 | Safe three-role visual preview with fictional data; no Backend/API/Kimia/auth | `158de0f98360ac3270b70f878506507c857935d5` | `f29e31974720d7926b7ae632e04de23978510ea0` | `621581b8da9abf01c06986c0c41e49c3c793906a` | Backend RC1 Validation #323 — PASS | SUPERSEDED — TECHNICAL PREVIEW ONLY |
| #192 | GitHub Pages deployment branch fix only | `621581b8da9abf01c06986c0c41e49c3c793906a` | `cb134051ba79d7c52277838d5218be94ff3746f6` | `7a580d598e2fd51fe9bda18d755dc86c0640ff5a` | Backend RC1 Validation #325 — PASS | SUPERSEDED — TECHNICAL PREVIEW ONLY |
| #193 | Expanded navigable Customer/Operator/Admin static demo; fictional data; disabled actions | `7a580d598e2fd51fe9bda18d755dc86c0640ff5a` | `3cfea029ebb8c24328ab3d20464024a102ed84c3` | `f9c4ea8a10fe11e4abfb5c0c857f9cd051bcde32` | Backend RC1 Validation #327 — PASS | SUPERSEDED — TECHNICAL PREVIEW ONLY |
| #194 | Premium customer financial experience demo with fictional balances and mock workflows | `f9c4ea8a10fe11e4abfb5c0c857f9cd051bcde32` | `bd09f3c6e8438b6505044735aef874cd9d0d6669` | `cd92a1144bdfbe043bae1871aab9d623ce8bad64` | Backend RC1 Validation #329 — PASS | SUPERSEDED — TECHNICAL PREVIEW ONLY |

## Evidence interpretation

### PR #191
- Explicitly isolated under `demo-preview/`.
- Fictional data only.
- Operational actions disabled.
- No Backend, Kimia, authentication, production data, API request, or external fetch.

### PR #192
- Workflow-only alignment for GitHub Pages deployment from the canonical recovery branch.
- No application or domain behavior change.

### PR #193
- Expanded the public static demo into multiple navigable role views.
- Continued to use fictional data only.
- No Backend/API/authentication/Kimia/production data connection.
- Write/approve/settle actions remained disabled.

### PR #194
- Added richer customer-facing mock flows including mock balances, deposits, withdrawals, transfer, support and delivery/custody views.
- Explicitly remained disconnected from Backend/API/Kimia and real customer data.
- Explicitly stated that real product balances must come only from Kimia and Custody remains separate.

## Safety conclusion

These PRs may be used only as historical visual/interaction evidence. They must not be used to claim that any production Customer, Operator or Admin capability is implemented, integrated, financially correct, authenticated, or release-ready.

## Recovery ledger consequence

The audited contiguous Recovery PR sequence is now classified through PR #194.

This closes the PR-number sequence immediately preceding V2-00 PR #195, but does **not** close the V2-00 gate. Remaining gate work includes broader branch/SHA inventory, full capability-to-file/test/CI closure, broader sanitized Kimia evidence, authenticated customer-to-Kimia account resolution, database/applied-migration evidence, real frontend visual/device verification, production/restore/monitoring evidence, Harvester artifact verification, and documentation namespace/carry-forward normalization.
