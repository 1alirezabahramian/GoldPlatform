# Phase 0 — Business Canonical Report

Date: 2026-08-05
Baseline: Stage 22 / RC2 merge `cada4441184e59d09f5ddac567d7b9b8d19b34ae`

## Executive decision

- Stage 00 — KEEP
- Stage 01 Kimia read-only — KEEP WITH CONTRACT REVIEW
- Stage 02 Financial Kernel — KEEP WITH ARCHITECTURE BOUNDARY FIX
- Stage 03 Trading — DONOR ONLY / REBUILD AS A COMPLETE SLICE

No Business branch is approved for direct merge into the RC2 recovery line.

## Verified timeline

| Stage | PR | Head SHA | Merge state | CI evidence | Decision |
|---|---:|---|---|---|---|
| Stage 00 | #88 | `eb44485143bc663c399e61bcb4800406db40e1ab` | merged | Business Engine Baseline run `30911802445`: PASS | KEEP |
| Stage 01 | #89 | `d5c2fb9f70abd1310a5b6db878c7002beb44991e` | merged | Business Engine Baseline run `30913749957`: PASS | KEEP WITH REVIEW |
| Stage 02 | #92 | `69e018c3ad9fdc88968def0ffacf0a069c218fdc` | merged to `main` as `31d55fac545201c7b436e940e48e9dcd89bd553d` | Business Engine Baseline run `30935684960`: PASS | KEEP WITH FIX |
| Stage 03 | #109 | `f77ba03ca27169d500a02c424cff8fa011e53119` | open / draft | Business Engine Baseline run `30950757603`: PASS | DONOR ONLY |

## Ground-truth boundary

Accepted and non-negotiable:

- Kimia is the final source of truth for Money, Gold, Coin and Currency balances.
- GoldPlatform is the source of truth for physical Custody/Amanat.
- Ledger, Journal, Event Store, Idempotency and Balance Projection are operational/audit/reconciliation infrastructure only.
- Any code or document that treats internal projections as the final customer balance is Architecture Drift and must be corrected before integration.

## Stage 00

Stage 00 is a valid recovery foundation. It fixed syntax and schema inconsistencies, added baseline CI, and did not introduce new financial rules or Kimia writes.

Decision: KEEP.

## Stage 01 — Kimia read-only

Strengths:

- explicit read-only scope;
- contract tests with HTTP Fake;
- account, account groups, coin, currency and balance reads;
- transparent error handling;
- no live write payload or action-code invention.

Required review before integration:

- preserve separate Read and Write paths;
- confirm `/api/account` uses the accepted `Type` parameter;
- do not convert read failures into empty successful results;
- keep retry policy limited to safe reads;
- keep credentials and Kimia identifiers out of customer-facing contracts.

Decision: KEEP WITH CONTRACT REVIEW.

## Stage 02 — Financial Kernel

Valuable components:

- exact decimal value objects;
- financial scope/tenant context;
- journal/event/idempotency contracts;
- trace and correlation identifiers;
- reversible and auditable operational records;
- tenant-scoped repositories.

Architecture correction required:

- `BalanceSnapshot`, posted/reserved/available projections and related repositories must never be presented as the final Money/Gold/Coin/Currency balance;
- balance projection must be explicitly documented as rebuildable operational projection sourced/reconciled against Kimia;
- Stage 02 can support intent, result, audit, idempotency, workflow and reconciliation only;
- Custody must remain outside these financial balance projections.

Decision: KEEP WITH ARCHITECTURE BOUNDARY FIX.

## Stage 03 — Trading

PR #109 is not a small change. It contains 53 commits, 51 changed files and introduces:

- new Quote and Order domain models;
- a second OrderStatus enum;
- quote/order repositories;
- idempotent submit/decision/terminal services;
- validation pipelines;
- new persistence tables `trading_quotes` and `trading_orders`;
- tenant/company/branch scope columns;
- multiple ADRs and tests.

Critical conflicts:

1. **Second Order model/state machine**
   - Stage 03 defines `draft`, `submitted`, `approved`, `rejected`, `expired`, `settled`, `delivered`, `cancelled`.
   - RC2 already has an operational Order lifecycle with states such as pending/approved/executing/settling/completed/rejected/expired/cancelled/failed.
   - These cannot coexist without a single accepted transition contract.

2. **Duplicate persistence**
   - Stage 03 creates `trading_orders` and `trading_quotes` alongside existing operational order/trade/settlement persistence.
   - Direct merge would create parallel sources for order lifecycle.

3. **Tenant architecture assumption**
   - Stage 03 persists `tenant_id`, `company_id`, `branch_id` and scope hashes.
   - White-label and tenant architecture are not yet accepted as a finished storage model.
   - This must not be imposed through a trading migration before the tenant boundary is accepted.

4. **Migration risk**
   - The new migration is additive but unproven against the canonical RC2 schema and rollback path.
   - It requires MySQL migrate:fresh, rollback, concurrency and duplicate-idempotency tests on the selected canonical branch.

5. **CI limitation**
   - The Stage 03 head has one green `Business Engine Baseline` workflow.
   - This is not equivalent to all RC2 gates, MySQL/Redis concurrency, production compose, security, performance and backup/restore on the canonical integration SHA.

Decision: DONOR ONLY / REBUILD AS A COMPLETE SLICE.

## Canonical capability decisions

| Capability | Canonical source | Decision |
|---|---|---|
| Kimia read client/repository | Stage 01 | KEEP WITH REVIEW |
| Exact decimal | Stage 02 | KEEP |
| Trace/correlation/idempotency concepts | Stage 02 | KEEP |
| Journal/Event Store | Stage 02 | KEEP AS NON-AUTHORITATIVE |
| Balance projection | Stage 02 | KEEP ONLY AS REBUILDABLE NON-AUTHORITATIVE PROJECTION |
| Existing RC2 Order/Trade/Settlement persistence | RC2 | CANONICAL UNTIL REPLACED BY ACCEPTED CONTRACT |
| Stage 03 Quote model | Stage 03 | DONOR |
| Stage 03 validation pipeline | Stage 03 | DONOR |
| Stage 03 idempotent submit/decision services | Stage 03 | DONOR |
| Stage 03 trading migration | Stage 03 | REJECT FOR DIRECT MERGE; REBUILD AFTER CONTRACT |
| Stage 03 OrderStatus | Stage 03 | DUPLICATE CONFLICT |

## Integration order

1. Keep RC2 order/trade/settlement as the active canonical implementation.
2. Correct Stage 02 documentation and interfaces so internal balances are explicitly non-authoritative.
3. Revalidate Stage 01 Kimia reads against accepted API behavior.
4. Extract a single accepted Quote → Order contract from Stage 03 without adding a second Order source.
5. Reuse only compatible validation and idempotency components.
6. Add persistence only after proving no duplicate Order/Trade/Settlement model exists.
7. Execute MySQL, Redis, concurrency, migration rollback and full RC2 regression on the exact integration SHA.

## Test status

- Stage 00: EXECUTED — PASS (Business Engine Baseline)
- Stage 01: EXECUTED — PASS (Business Engine Baseline)
- Stage 02: EXECUTED — PASS (Business Engine Baseline)
- Stage 03: EXECUTED — PASS only for Business Engine Baseline
- Stage 03 full RC2 six-gate validation: NOT EXECUTED / not evidenced on the Stage 03 head
- Stage 03 canonical MySQL rollback/concurrency validation: NOT EXECUTED / not evidenced

## Final status

Business recovery status: **IN PROGRESS**

Safe conclusion:

- Preserve Stage 00–03.
- Do not merge Stage 03.
- Keep Stage 01 and Stage 02 only with explicit Kimia source-of-truth corrections.
- Rebuild Quote/Submit/Decision as a controlled slice on RC2 after selecting one Order lifecycle and one persistence source.
