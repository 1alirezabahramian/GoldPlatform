# GoldPlatform — Phase 0 Forensic Inventory v1

> Status: In Progress
>
> Evidence date: 2026-08-05
>
> Repository: `1alirezabahramian/GoldPlatform`
>
> Default branch: `main`
>
> Recovery rule: Preserve → Inspect → Compare → Validate → Document → Integrate

## 1. Purpose

This inventory freezes the recovery method. No product feature may start until the current baseline, donor histories, duplicate candidates and architecture-drift paths are classified and tested.

This document is evidence-driven. Branch and PR names do not prove that a capability is canonical. Merge status alone does not prove current compatibility.

## 2. Non-negotiable Ground Truth

### Accepted

Kimia is the final source of truth for:

- Money
- Gold
- Coin
- Currency

GoldPlatform is the source of truth for:

- Physical Custody / Amanat

Internal ledger, journal, event, idempotency and projection components may support audit, trace, intent/result, order lifecycle, settlement workflow and reconciliation, but must not be exposed as the customer's final financial balance.

## 3. Protected Evidence — Do Not Delete

The following histories are preserved as evidence and donor candidates:

- `main`
- `feature/goldplatform-developer-mcp`
- `feature/local-agent-runner`
- Customer CP chain and closure
- Business Stage 00–03 chain
- Admin/Operator AP-01–AP-20 chain
- Operator OP chain
- Customer and Admin frontend candidates
- Recovery PRs #145, #146 and #147

No force-push, shared-history rebase, blind cherry-pick, broad revert or branch deletion is allowed during recovery.

## 4. Live Repository Facts

- Default branch: `main`
- Current recovery-start base: `main@31d55fac545201c7b436e940e48e9dcd89bd553d`
- Stage 00, Stage 01 and Stage 02 are merged on `main`.
- The historical product line contains merged Customer, Custody, Delivery, Settlement, Audit, Outbox, Security, Production and Frontend foundation work on a separate diverged history.
- Therefore neither `main` nor a historical feature branch alone is accepted as the complete product baseline.

## 5. Current Recovery PR Classification

### PR #145 — Phase 0 recovery documentation

- Head: `recovery/phase-0-current-state@dc8680a629568a9a37e2fbc828f1a50953ec4ea3`
- Base: `main`
- Draft, open
- Classification: `RECOVERY EVIDENCE`
- Integration status: do not merge until its claims are reconciled with this forensic inventory.

### PR #146 — permission reconstruction experiment

- Head: `reconstruct/permission-foundation-v1@bc5ec22fc4a4c8463535fb6e5b3cfc1805b067fc`
- Base: `main`
- Draft, open
- Classification: `EXPERIMENTAL DONOR / NOT CANONICAL`
- Reason: created before full baseline inventory and permission consolidation.
- Integration status: frozen; do not merge.

### PR #147 — baseline repair experiment

- Head: `recovery/baseline-repair-v1@05cda54f3ab1fe0c7f570a8b829ccf84cac9bd21`
- Base: `main`
- Draft, open
- Classification: `RECOVERY EXPERIMENT / REQUIRES FILE-BY-FILE REVIEW`
- Known scope includes User schema alignment, PSR-4 cleanup and financial-balance mutation guards.
- Integration status: frozen; no merge until every changed file is compared with the historical canonical product line and exact-head CI is recorded.

## 6. Capability Classification — First Pass

| Capability | Current evidence source | Status | Recovery action |
|---|---|---|---|
| Kimia read-only foundation | Stage 01 on `main` | `KEEP — VERIFY` | Re-run exact-SHA contracts and compare with historical product client/repositories. |
| Financial kernel contracts | Stage 02 on `main` | `KEEP — VERIFY` | Confirm audit/workflow-only role; reject final-balance use. |
| Customer API CP-01–CP-18 | merged historical product line | `HEALTHY DONOR — VERIFY` | Preserve closure; reconstruct only missing slices on canonical base. |
| Customer frontend FE-01/FE-02 | historical branches | `DONOR — NOT BUILT` | Select only after install, typecheck, build and E2E. |
| Custody/Amanat | historical product line | `HIGH-VALUE DONOR` | Preserve independent ownership and lifecycle. |
| Delivery | historical product line | `HIGH-VALUE DONOR` | Verify ownership, idempotency, audit and transition contract. |
| Settlement | historical product line and Stage 03 dependencies | `CONFLICTED DONOR` | Build one canonical lifecycle; no Kimia write activation. |
| Order state machine | simple `main`, historical product, Stage 03 | `CONTRACT CONFLICT` | Stop integration until one accepted state contract and mapper exist. |
| Stage 03 trading validation | PR #109 | `STACKED DONOR` | Never transfer test-only commits; reconstruct as a complete dependency slice. |
| Permission system | AP, OP and PR #146 | `PARALLEL CONFLICT` | Produce one additive, non-destructive catalog after route inventory. |
| Admin/Operator read APIs | AP/OP chains | `SLICE DONORS` | Compare route-by-route and response-by-response. |
| Admin/Operator frontend | AP-20 and OP-03+ | `PARALLEL CONFLICT` | Keep one foundation only after real build and API compatibility proof. |
| Legacy Wallet balance mutation | `main` and historical files | `ARCHITECTURE DRIFT` | Preserve for evidence; prevent use as final Money/Gold/Coin/Currency balance. |
| Ledger/Journal/Outbox/Idempotency | multiple histories | `KEEP IF OPERATIONAL` | Retain only for audit, trace, workflow, intent/result and reconciliation. |
| Agent/MCP work | PR #1, #43, #79 | `SEPARATE TOOLING TRACK` | Must not determine product architecture or release baseline. |

## 7. Known High-Risk Conflicts

### Financial source-of-truth conflict

Historical descriptions exist where ledger or balance projection is called the financial source of truth. Under the accepted architecture these descriptions are superseded. Kimia is final for Money, Gold, Coin and Currency.

### Order conflict

At least three order lifecycle contracts exist. No migration, enum, controller or frontend status display may be accepted until the canonical contract is selected from actual code and accepted documentation.

### Permission conflict

Known naming conflicts include:

- `audit.view` / `audit-logs.view`
- `orders.queue.view` / `orders.view`
- `deliveries.complete` / `deliveries.deliver`
- `kimia.read` / `kimia.view`

Any destructive `syncPermissions` behavior is prohibited until existing role and direct-permission preservation is proven.

### Frontend conflict

Multiple Nuxt foundations exist. No frontend is canonical before dependency install, typecheck, lint, production build, API contract tests, RTL/responsive/accessibility checks and browser E2E.

## 8. Fixed Recovery Sequence

This order is now frozen. It may change only when a documented GitHub contradiction, test failure or accepted Ground Truth requires it.

1. Freeze and inventory all active PRs and donor histories.
2. Inventory `main` files by domain: Auth, Kimia, Financial, Trading, Custody, Delivery, Admin/Operator, Frontend, Docs and Tests.
3. Inventory the historical product line by the same domains.
4. Produce a path-level duplicate/conflict matrix.
5. Run exact-SHA baseline gates on the candidate parent commit.
6. Create a canonical recovery branch only after steps 1–5 are documented.
7. Transfer one complete, dependency-closed slice at a time.
8. Run focused tests plus full regression after every slice.
9. Update Project Memory, Project State, CHANGELOG, ADR, OpenAPI and test evidence in the same slice.
10. Merge only after green CI on the exact head SHA.

## 9. Immediate Work Queue

- [x] Confirm live default branch.
- [x] Capture live PR state for recovery, Customer, Business, AP, OP and frontend tracks.
- [x] Freeze PR #146 and PR #147 as non-canonical experiments.
- [ ] List all active branches and map each to its PR/head SHA.
- [ ] Build complete file inventory for `main`.
- [ ] Build complete file inventory for `feature/goldplatform-developer-mcp`.
- [ ] Compare Auth/User schema across both histories.
- [ ] Compare Kimia clients, repositories and read contracts.
- [ ] Compare Wallet/Ledger/Projection paths against accepted source-of-truth rule.
- [ ] Compare all Order enums, migrations, services and APIs.
- [ ] Compare Custody, Delivery and Settlement implementations.
- [ ] Compare AP and OP route/permission/response contracts.
- [ ] Compare frontend foundations and execute build gates.
- [ ] Select candidate canonical parent SHA.
- [ ] Create canonical reconstruction branch.

## 10. Current Conclusion

The project is not proven destroyed. The live repository still contains preserved merged and unmerged histories. The confirmed problem is baseline fragmentation, parallel contracts and unverified integration.

Recovery status: `IN PROGRESS — EVIDENCE PRESERVED — NO CANONICAL PRODUCT BRANCH SELECTED`.
