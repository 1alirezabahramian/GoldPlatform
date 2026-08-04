# GoldPlatform — Project Recovery and Current State

> Status: In Progress
>
> Recovery date: 2026-08-05
>
> Recovery branch: `recovery/phase-0-current-state`
>
> Base: `main`
>
> Base SHA at recovery start: `31d55fac545201c7b436e940e48e9dcd89bd553d`

## 1. Current Base

The GitHub default branch is `main`.

The latest validated repository commit found at recovery start is:

- `31d55fac545201c7b436e940e48e9dcd89bd553d` — Merge Stage 02 financial kernel contracts.

Stage 00, Stage 01 and Stage 02 have merge commits on `main`:

- Stage 00: `1c8555c5c9c57dfd6157bd53555690995fa3f440`
- Stage 01: `8adac19d2f9acd75ebbcb39e486614bec4676582`
- Stage 02: `31d55fac545201c7b436e940e48e9dcd89bd553d`

No workflow run or combined commit status was returned for the current `main` SHA. Therefore the current base is **Merged — Closure Pending**, not Production Ready.

## 2. Financial and Custody Source of Truth

### Accepted

Kimia is the final source of truth for:

- Money
- Gold
- Coin
- Currency

GoldPlatform must not expose its journal, ledger, event store, idempotency registry or balance projection as the customer's final financial balance.

Internal financial components are permitted only for:

- Audit and trace
- Idempotency
- Intent/result recording
- Order lifecycle
- Settlement workflow
- Reconciliation
- Kimia voucher/record matching
- Detection of incomplete or duplicate operations

GoldPlatform is the source of truth for physical Custody/Amanat. Custody must remain separate from Kimia financial balances.

Any code that treats an internal projection as the final Money, Gold, Coin or Currency balance is an **Architecture Drift** candidate.

## 3. Customer Platform

### Merged

Customer Platform closure is preserved by merge commit:

- `5da4da919b0fbd277e3cb1f3cf92c27b93b3868c`
- PR #132 — merged.

### Duplicate / contaminated candidate

`work/customer-cp02-read-resources` / commit `59756b62c744d09a15c0b89c7de1b3de273656db` must not be merged directly.

Potential reusable slice, only after comparison with current `main`:

- `OrderResource.php`
- `CustodyResource.php`
- `DeliveryResource.php`
- limited `CustomerReadController.php` changes
- `CustomerReadResourcesTest.php`

Status: **Duplicate Candidate / Needs Comparison**.

### Frontend

- PR #140 — FE-02 Nuxt customer application shell — Draft, open, based on `feature/goldplatform-developer-mcp`.
- It is not part of the closed Customer Backend contract until rebased/rebuilt as a clean slice on current `main`, with frontend install, typecheck, build and browser E2E.

## 4. Business Engine

### Merged

- Stage 00 merged to `main`.
- Stage 01 Kimia read-only foundation merged to `main`.
- Stage 02 financial kernel merged to `main`.

### Stage 03

- PR #109 — `work/business-engine-stage03-trading-validation`
- Base: `work/business-engine-stage02-financial-kernel`
- Head SHA: `f77ba03ca27169d500a02c424cff8fa011e53119`
- State: Draft, open.

Stage 03 is stacked on an old Stage 02 branch although Stage 02 is now merged to `main`. It must be compared against current `main` as a complete dependency slice. Test-only commits must not be transferred independently.

The internal financial kernel must remain operational infrastructure and must not become the final financial balance source.

Status: **In Progress / Stacked Drift**.

## 5. Admin and Operator

### AP chain

AP-01 to AP-20 are preserved as open Draft PRs.

Key recovery facts:

- AP-01 PR #104 is not mergeable against its old base.
- AP-02 to AP-20 form a linear stacked chain.
- AP-06 contains the known new migration.
- Tests are mostly described as written but not executed.
- AP-20 creates a Nuxt frontend foundation that overlaps with the newer OP frontend chain.

No AP PR is approved for direct merge.

### New OP chain

A second Admin/Operator chain exists:

- PR #141 — OP-02 session bootstrap
- PR #142 — OP-03 application shell
- PR #143 — OP-04 operational dashboard
- PR #144 — OP-05 operator queues

This chain overlaps with AP permissions, dashboards, queues and frontend work. It must be compared slice-by-slice; it must not be merged as a second full platform.

Status: **Duplicate Candidate / Parallel Development Conflict**.

## 6. Permission Conflict

Known naming conflicts requiring one accepted catalog:

- `audit.view` vs `audit-logs.view`
- `orders.queue.view` vs `orders.view`
- `deliveries.complete` vs `deliveries.deliver`
- `kimia.read` vs `kimia.view`

Any seeder using destructive permission synchronization must be tested against existing roles and direct permissions before integration.

Status: **Needs Decision after evidence-based catalog comparison**.

## 7. Migration Risk

Known high-risk migration slice:

- AP-06: `customer_policy_change_requests`.

Required validation before integration:

- migrate:fresh
- forward migration
- rollback
- re-run migration
- MySQL integration
- existing data compatibility
- permission and API contract regression

Current status: **WRITTEN — NOT EXECUTED** unless proven by CI on the exact head SHA.

## 8. API Contract Conflict

Parallel versioned routes and response contracts exist across AP and OP chains for:

- Admin dashboard
- Operator dashboard
- Order queues
- Delivery queues
- Audit/outbox reads
- User/customer-group/order/custody/delivery/settlement reads

Before integration, each route must be classified as:

- Existing and canonical
- Duplicate Candidate
- Contract-compatible extension
- Breaking conflict

No controller may call Kimia Client directly.

## 9. Frontend Conflict

Current frontend candidates include:

- Customer FE-01 / FE-02 branches
- AP-20 `frontend-admin/`
- OP-03 onward Admin/Operator Nuxt shell

Required decision must be based on current code comparison, not roadmap numbering.

Mandatory validation:

- dependency install
- typecheck
- lint
- production build
- API contract tests
- responsive RTL checks
- permission navigation tests
- browser E2E

Current status: **WRITTEN — NOT EXECUTED** for branches that explicitly state dependencies were not installed.

## 10. Documentation Conflict

Documents must be classified as Accepted, Draft, Superseded, Historical or Needs Decision.

Accepted architecture rule:

- Kimia is the source of truth for Money, Gold, Coin and Currency.
- GoldPlatform is the source of truth for physical Custody/Amanat.

Any document describing GoldPlatform Wallet or internal balance projection as the final financial source must be marked **Superseded**.

## 11. CI and Test Truth

Test status must remain separate:

- WRITTEN — NOT EXECUTED
- EXECUTED — PASS
- EXECUTED — FAIL
- NOT APPLICABLE

At recovery start, no workflow run or combined status was returned for `main` SHA `31d55fac...`.

Therefore:

- Stage 02 source branch may have historical green CI.
- Current merged `main` SHA is not yet proven by an exact-SHA CI run.
- GoldPlatform is not Production Ready.

## 12. Integration Plan

1. Run exact-SHA CI and full regression on current `main`.
2. Compare Customer Resources refactor against current `main`; integrate only a clean, contract-compatible slice if still useful.
3. Reconstruct Stage 03 dependency graph against current `main`; transfer a complete production-and-test slice only.
4. Build one canonical permission catalog from current main + AP + OP evidence.
5. Test AP-06 migration independently.
6. Consolidate duplicate Admin/Operator routes and read models.
7. Select one Admin/Operator frontend foundation after code and build comparison.
8. Run backend, frontend, integration and browser E2E gates.
9. Update Project Memory, Project State, CHANGELOG, ADRs, OpenAPI, frontend docs and test reports together.
10. Merge only clean PRs with green CI on their exact head SHA.

## 13. Real Backlog

- Exact-SHA main regression and CI proof
- Customer Resource refactor comparison
- Stage 03 clean reconstruction
- Permission catalog consolidation
- AP-06 migration validation
- Admin/Operator API consolidation
- Frontend foundation selection and build proof
- Kimia read validation
- Order approval/rejection/settlement completion
- Kimia Write only after verified contract and owner approval
- Demo and release preparation

## 14. Immediate Next Step

Create a complete evidence table for:

- open PRs and their base/head SHA
- mergeability and conflicts
- exact-SHA workflow results
- changed files and migrations
- duplicate routes, permission names and frontend directories

Then run the current `main` regression before integrating any product feature.
