# V2-00 — Exit Criteria and Non-Blocking Carry-Forward

Date: 2026-08-07

## Purpose

Prevent Stage V2-00 (Complete Source Recovery & Knowledge Reconstruction) from expanding into runtime, deployment, product delivery, or release closure work.

This document fixes the exit boundary for V2-00. It does not create a new stage.

## Required for V2-00 closure

V2-00 may close only when the recovery/knowledge baseline itself is evidenced:

1. Current canonical repository/branch baseline is identified.
2. Recovery PR evidence through the audited recovery range is classified.
3. Current branch inventory is captured with exact branch -> head SHA evidence.
4. Core source-of-truth boundaries are documented and conflict-safe:
   - Kimia = final authority for Money/Gold/Coin/Currency.
   - GoldPlatform = final authority for physical Custody/Amanat.
   - internal ledger/projection/reservation structures are not customer final balance authority.
5. Major recovered capabilities are classified with source/code/test/CI evidence or explicitly marked UNKNOWN/BLOCKED/SUPERSEDED/HISTORICAL ONLY.
6. Known contradictions, duplicate candidates, and historical-only artifacts are documented instead of silently normalized.
7. Unresolved work is assigned to a carry-forward category without being misreported as complete.
8. The final V2-00 documentation head has exact-SHA CI evidence.

## Explicitly NOT required to close V2-00

The following are important project work, but they are not source-recovery exit criteria and must not hold V2-00 open indefinitely:

- Current shop/runtime database migration proof.
- Real production deployment destination proof.
- TLS/DNS/WAF/secret-store destination configuration.
- Real monitoring/alert delivery proof.
- Real backup/restore execution on the final destination.
- Real-device Android/iPhone/Windows visual verification.
- Full product UI implementation or release acceptance.
- Enabling Kimia Write.
- Resolving financial rules that still require Ground Truth.
- PR #196 merge/deployment while its security dependency remains externally blocked.
- CommonMark patched stable release availability.

These remain mandatory where relevant before their own implementation/release claims, but they are carry-forward work rather than V2-00 closure blockers.

## No Stage Proliferation Rule

`NO STAGE PROLIFERATION`

A new numbered V2 stage must not be created merely because a gap is discovered.

A new stage requires all of the following:

- a distinct product/architecture outcome that cannot fit an existing authorized scope;
- explicit measurable exit criteria;
- a clear relationship to the delivery roadmap;
- no duplication with an existing stage or backlog item.

Otherwise the item remains in the current implementation/release backlog.

## Current evidence already satisfying closure requirements

- Recovery PR ledger has been audited through PR #194.
- Historical demo PRs #191..#194 are classified `SUPERSEDED — TECHNICAL PREVIEW ONLY — NOT PRODUCT EVIDENCE`.
- Production Operations recovery PR #197 reached `TESTED — NOT MERGED` on exact SHA.
- Operational Health core is present on canonical and classified `REUSE AS-IS`; the old Admin health implementation is not to be blindly reconstructed.
- V2 Evidence Harvest run #7 completed successfully on exact SHA `df0e9581a5e553935eaf542a4b8b478cc2c8d457`.
- Its immutable Artifact captured 142/142 current branches with exact head SHAs and zero evidence errors.

## Remaining V2-00 closure work

The remaining work must now be limited to:

1. Final capability-to-evidence consolidation for the recovery baseline.
2. Final contradiction/carry-forward ledger normalization.
3. Exact-SHA CI on the final V2-00 documentation head.
4. PR #195 closure decision only after the above is evidenced.

Anything outside those four items is not allowed to extend V2-00.

## Status

`V2-00 — CLOSURE CANDIDATE — FINAL CONSOLIDATION REQUIRED`

This status intentionally replaces open-ended recovery wording. It does not claim merge, product completion, production readiness, or release readiness.
