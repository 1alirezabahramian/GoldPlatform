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

Internal financial components are permitted only for audit, trace, idempotency, intent/result, order lifecycle, settlement workflow, reconciliation, Kimia voucher/record matching and incomplete-operation detection.

GoldPlatform is the source of truth for physical Custody/Amanat. Custody must remain separate from Kimia financial balances.

Any code that treats an internal projection as the final Money, Gold, Coin or Currency balance is an **Architecture Drift** candidate.

## 3. Customer Platform

Customer Platform closure is preserved by merge commit:

- `5da4da919b0fbd277e3cb1f3cf92c27b93b3868c`
- PR #132 — merged.

`work/customer-cp02-read-resources` / commit `59756b62c744d09a15c0b89c7de1b3de273656db` must not be merged directly.

Potential reusable slice, only after comparison with current `main`:

- `OrderResource.php`
- `CustodyResource.php`
- `DeliveryResource.php`
- limited `CustomerReadController.php` changes
- `CustomerReadResourcesTest.php`

Status: **Duplicate Candidate / Needs Comparison**.

PR #140 — FE-02 Nuxt customer application shell — remains Draft and is based on the historical `feature/goldplatform-developer-mcp` line. It is not part of the closed Customer Backend contract until rebuilt as a clean slice on current `main`, with install, typecheck, build and browser E2E.

## 4. Business Engine

### Merged

- Stage 00 merged to `main`.
- Stage 01 Kimia read-only foundation merged to `main`.
- Stage 02 financial kernel merged to `main`.

### Stage 03 live evidence

PR #109:

- Base: `work/business-engine-stage02-financial-kernel`
- Head: `work/business-engine-stage03-trading-validation`
- Head SHA: `f77ba03ca27169d500a02c424cff8fa011e53119`
- Draft, open, mergeable relative to its historical base
- 53 commits, 51 changed files

Comparison against current `main`:

- Status: `diverged`
- Ahead by: 53 commits
- Behind by: 3 commits
- Merge base: Stage 02 head `69e018c3...`

The production dependency slice contains Quote and Order aggregates, repositories, state enums, validation engines, idempotent create/submit/decision/terminal services, a trading migration, database adapters, tests and ADRs. The Test-only commits are not independently useful.

Required integration unit:

1. Domain lifecycle contracts
2. Persistence contracts and migration
3. Idempotent application services
4. Validation pipeline
5. Database adapters
6. Full matching tests and ADRs

Status: **In Progress / Stacked Drift / Clean Reconstruction Required**.

## 5. Admin and Operator

### AP chain

AP-01 to AP-20 are preserved as open Draft PRs. No AP PR is approved for direct merge.

PR #104 — AP-01 live evidence:

- Draft and open
- `mergeable=false`
- Historical base: `feature/goldplatform-developer-mcp@558a1cd6...`
- Head: `work/admin-operator-ap01-foundation@338550ae...`
- 8 PR commits and 7 PR changed files

However comparison of its branch against current `main` proves broad branch contamination:

- Status: `diverged`
- Ahead by: 346 commits
- Behind by: 123 commits
- Merge base: `0d618bf...`

The branch-level diff includes hundreds of unrelated backend, customer, Kimia, infrastructure, migration, documentation and Agent changes. Therefore AP-01 cannot be retargeted or merged as a normal permission feature.

### OP chain

PR #141 — OP-02 live evidence:

- Draft and open
- Historical base: `feature/goldplatform-developer-mcp@4647d3bf...`
- Head: `work/admin-operator-op02-session-bootstrap@49b44b13...`
- PR description states tests were written but not executed

Comparison against current `main`:

- Status: `diverged`
- Ahead by: 454 commits
- Behind by: 123 commits
- Merge base: `0d618bf...`

Its branch-level diff includes the entire historical product line in addition to `BackofficeSessionBootstrap`, bootstrap controllers, routes, tests, OpenAPI and documents. It must not be directly retargeted or merged.

### Consolidation decision

AP and OP branches are evidence sources, not integration branches.

Canonical recovery method:

1. Identify one capability.
2. Compare its exact files with current `main`.
3. Classify existing equivalents.
4. Rebuild only the missing capability on a clean branch from `main`.
5. Run permission, route, contract and regression tests.

Status: **Duplicate Candidate / Parallel Development Conflict / Slice Reconstruction Required**.

## 6. Permission Conflict

Known naming conflicts requiring one accepted catalog:

- `audit.view` vs `audit-logs.view`
- `orders.queue.view` vs `orders.view`
- `deliveries.complete` vs `deliveries.deliver`
- `kimia.read` vs `kimia.view`

A destructive permission synchronization operation is prohibited until role, direct-permission and existing-database behavior is proven.

Status: **Needs Evidence-Based Consolidation**.

## 7. Migration Risk

Known migration slices:

- Stage 03: `2026_08_04_220000_create_tenant_scoped_trading_tables.php`
- AP-06: `customer_policy_change_requests`

Required validation before integration:

- migrate:fresh
- forward migration
- rollback
- re-run migration
- MySQL integration
- existing data compatibility
- contract and permission regression

Current status: **WRITTEN — NOT EXECUTED** unless proven by CI on the exact reconstructed head SHA.

## 8. API Contract Conflict

Parallel versioned routes and response contracts exist across AP and OP chains for dashboards, queues, audit/outbox, users, customer groups, orders, custody, delivery, settlement and bootstrap.

Each route must be classified as canonical, duplicate, compatible extension or breaking conflict. No Controller may directly call Kimia Client.

## 9. Frontend Conflict

Current candidates include Customer FE branches, AP-20 `frontend-admin/`, and OP-03 onward Admin/Operator Nuxt shell.

No frontend foundation is accepted until dependency install, typecheck, lint, production build, contract tests, RTL/responsive checks, permission navigation and browser E2E are executed.

## 10. Documentation Classification

Accepted:

- Kimia is final source for Money, Gold, Coin and Currency.
- GoldPlatform is final source for physical Custody/Amanat.

Any document describing GoldPlatform Wallet or internal balance projection as the final financial source must be marked **Superseded**.

Historical branch reports remain evidence and must not override current GitHub state.

## 11. CI and Test Truth

- Current `main@31d55fac...`: exact-SHA workflow proof not returned.
- PR #109 tests: historical branch evidence only until reconstructed on `main`.
- PR #141 explicitly: **WRITTEN — NOT EXECUTED**.
- AP branches that state tests were not run: **WRITTEN — NOT EXECUTED**.
- No current claim of Production Ready is valid.

## 12. Integration Plan

1. Establish exact-SHA regression proof for current `main`.
2. Compare the Customer Resources refactor with current `main`.
3. Reconstruct Stage 03 as a complete production-plus-test slice on a clean branch.
4. Build a canonical permission catalog from current `main`, AP and OP evidence.
5. Validate AP-06 migration independently.
6. Consolidate duplicate routes/read models.
7. Select one frontend foundation after code and build comparison.
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

The safest next technical action is to reconstruct Stage 03 from current `main` as a clean, complete slice. Before writing that slice, inspect the current `main` for existing Order/Quote models, migrations and state machines to prevent duplicate domain models.
