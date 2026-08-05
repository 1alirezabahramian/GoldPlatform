# Phase 0 — Admin / Operator Canonical Report

**Date:** 2026-08-05  
**Recovery baseline:** Stage 22 / RC2 (`cada4441184e59d09f5ddac567d7b9b8d19b34ae`)  
**Status:** In Progress — Canonical decision recorded

## Executive decision

Admin / Operator output after RC2 split into two competing chains:

1. `AP-01` through `AP-20`
2. `OP-01` through `OP-05`

Neither chain may be merged directly into the RC2 recovery line.

Canonical decisions:

- `OP-01`: **KEEP WITH CONTRACT REVIEW**
- `OP-02` through `OP-05`: **DONOR ONLY — REBUILD ON CURRENT BASE**
- `AP-01` through `AP-20`: **DONOR ONLY — SLICE-BY-SLICE REVIEW REQUIRED**
- `AP-06` migration: **REBUILD / MIGRATION VALIDATION REQUIRED**
- `AP-20` frontend: **DONOR ONLY — INSTALL / BUILD / TYPECHECK NOT EXECUTED**

## Evidence

### OP chain

`OP-01` was merged through PR #139 with merge commit `4647d3bf3c6ceef809c8dcd7a12b88fc5d12ef2b`. It adds versioned Admin and Operator bootstrap contracts without migration, financial rule, Wallet/Ledger/Settlement mutation, or Kimia write.

`OP-02` through `OP-05` remain open Draft PRs. Their own PR descriptions state that tests were written but not executed. `OP-05` head is `37935f54d357341aba87808146e77d56d5df2d8a` and is stacked on `OP-04`, not on RC2 or the current canonical recovery branch.

### AP chain

`AP-01` is open, Draft, and `mergeable=false`. Its base SHA is `558a1cd6b029011ce98fb46c9a52b42e52aa9027`, which is not the RC2 recovery baseline. The entire AP chain is stacked linearly above that foundation.

`AP-20` remains open and Draft. Its own description states dependencies were not installed and Nuxt build/typecheck were not executed.

## Conflict matrix

### Permission conflict

The AP and OP chains introduced parallel permission naming and ownership models. The final catalog must be rebuilt on the current base and must resolve at least these known conflicts:

- `audit.view` vs `audit-logs.view`
- `orders.queue.view` vs `orders.view`
- `deliveries.complete` vs `deliveries.deliver`
- `kimia.read` vs `kimia.view`

No destructive synchronization is allowed. Any seeder using `syncPermissions` must be treated as unsafe until tested against existing direct and role-based permissions.

### Route and response conflict

Both chains introduce Admin/Operator bootstrap, dashboard, queue, and read contracts. These are duplicate candidates. Canonical routes and envelopes must be selected once, then only missing behavior may be rebuilt.

### Frontend conflict

Two frontend directions exist:

- AP-20 `frontend-admin/`
- OP-03 through OP-05 Nuxt application shell and operational pages

Neither has complete executed evidence for dependency install, build, typecheck, lint, unit, or browser E2E. They are design/code donors, not canonical frontend baselines.

### Migration risk

AP-06 is the only AP stage that introduces a new migration. It must not be transferred until all of the following pass on the RC2-derived recovery line:

- `migrate:fresh`
- rollback
- MySQL integration
- permission regression
- idempotency and audit checks
- tenant/company safety review

## Capability decisions

### Keep / rebuild candidates

The following capabilities have clear product value and may be rebuilt as isolated slices after conflict resolution:

- Permission foundation
- Session-aware bootstrap
- Safe Admin and Operator dashboards
- Operational queues
- User and customer-group read models
- Roles and permissions read models
- Orders, Custody, Delivery, Settlement reads
- Kimia read-only overview
- System health read
- Product and pricing read
- Branch projection
- White-label discovery
- Notification discovery
- Reports catalog
- Safe delivery actions
- Settlement capability contract
- Frontend application shell

### Explicit exclusions

The recovery line must not inherit:

- direct AP or OP stack merges
- permission seeding with destructive synchronization
- duplicate route groups or response envelopes
- unexecuted AP-06 migration
- unbuilt frontend code treated as complete
- Admin UI financial logic
- Kimia write, balance mutation, or settlement execution without Ground Truth

## Test status

- AP chain tests: **WRITTEN — NOT EXECUTED** for the majority of stages
- OP-02 through OP-05 tests: **WRITTEN — NOT EXECUTED**
- AP-20 frontend install/build/typecheck: **WRITTEN OR CONFIGURED — NOT EXECUTED**
- Canonical AP/OP regression on RC2 recovery base: **NOT EXECUTED**

## Integration order

1. Preserve all AP and OP branches and PRs.
2. Select one canonical permission catalog.
3. Select one canonical Backoffice API envelope and route group.
4. Rebuild session bootstrap on the RC2-derived base.
5. Rebuild dashboard and queues as independent read-only slices.
6. Validate AP-06 migration separately before any adoption.
7. Select one frontend foundation only after install/build/typecheck comparison.
8. Run backend regression, frontend build/typecheck, contract tests, permission tests, and browser E2E.

## Final classification

| Area | Decision |
|---|---|
| OP-01 foundation | KEEP WITH CONTRACT REVIEW |
| OP-02 to OP-05 | DONOR ONLY |
| AP-01 to AP-20 | DONOR ONLY |
| AP-06 migration | REBUILD / VALIDATE |
| AP-20 frontend | DONOR ONLY |
| Permission catalog | REBUILD ONE CANONICAL CATALOG |
| Routes / response contracts | CONSOLIDATE |

## Next action

Complete the Infrastructure Canonical Report for Stage 23, Stage 24, production operations, CI, observability, health, backup, and recovery. No AP or OP code is integrated before that report and the final cross-track capability matrix are complete.
