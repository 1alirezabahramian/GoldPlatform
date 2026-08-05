# GoldPlatform — Recovery Rollback Baseline Decision

Date: 2026-08-05
Status: Accepted for Recovery Work — Not Production Ready

## Decision

The canonical recovery work branch starts from:

- Commit: `5dd0653cf49dc171c4310f02257da98784863bf4`
- Commit title: `Backend RC1 final validation gate and project library`
- Historical validation head: `889f3ea87025d15644f9515504936bc8e38826ee`
- Workflow: `Backend RC1 Validation` run `#31`
- Workflow result: `EXECUTED — PASS`

Two branches preserve this decision:

- Immutable evidence snapshot: `recovery/rc1-snapshot-2026-08-04`
- Recovery work branch: `recovery/canonical-from-rc1`

No existing branch, PR, commit or historical output is deleted or rewritten.

## Why this point

This commit is before the Customer, Business Engine, Admin/AP, Operator/OP and frontend work split into parallel development paths. It has stronger test evidence than older candidates and a smaller recovery surface than later RC2 / Customer / AP / OP histories.

Later work remains protected as donor evidence and may only be forward-ported slice-by-slice after comparison and tests.

## Important architectural correction

RC1 is not accepted without correction. Its historical Library and some code describe internal Ledger or Wallet projections as the customer's financial source of truth.

That description is superseded.

Accepted source of truth:

- Kimia: Money, Gold, Coin and Currency
- GoldPlatform: physical Custody / Amanat

Internal Ledger, Journal, Event Store, Idempotency and Projection may only support audit, trace, workflow, intent/result and reconciliation.

## Recovery gates before feature development

1. Correct Project Memory, Library, Project State and ADR classifications.
2. Remove customer-balance authority from Wallet and local balance projections.
3. Keep Kimia read-only integration and revalidate real confirmed query contracts.
4. Re-run Composer validation, PHP lint, migration fresh, rollback and PHPUnit.
5. Re-run MySQL, Redis, Docker, security and secret-scan gates.
6. Forward-port Custody and Delivery only after ownership, idempotency and lifecycle tests.
7. Forward-port Customer API contracts only after they consume Kimia-backed balances.
8. Rebuild Order and Settlement contracts without completing settlement from local Ledger alone.
9. Consolidate one permission catalog before any AP/OP slice.
10. Select frontend foundations only after install, typecheck, build and E2E.

## Donor classification

- Stages 14–22: donor candidates; not automatically retained.
- Customer CP chain and closure: high-value donor; not directly merged.
- Business Stage 00–03: donor candidates; Stage 03 remains dependency-closed.
- AP-01–AP-20 and OP chains: evidence and slice donors; no direct merge.
- Recovery PRs #145–#147: historical recovery evidence; not product baseline.

## Current status

`RECOVERY BASE SELECTED — RC1 SNAPSHOT PRESERVED — CANONICAL REPAIR IN PROGRESS`
