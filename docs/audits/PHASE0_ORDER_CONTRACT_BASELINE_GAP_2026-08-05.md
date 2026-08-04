# Phase 0 — Order Contract and Baseline Gap Audit

> Status: Accepted Recovery Evidence
>
> Date: 2026-08-05
>
> Recovery branch: `recovery/phase-0-current-state`
>
> Compared base: `main@31d55fac545201c7b436e940e48e9dcd89bd553d`

## Result

The current GitHub default branch `main` is not the same integrated product baseline that contains the closed Customer Platform and the historical production-oriented backend line.

The Stage 00–02 merge commits exist on `main`, but Customer Closure commit `5da4da919b0fbd277e3cb1f3cf92c27b93b3868c` is on a diverged history:

- ahead of `main`: 439 commits
- behind `main`: 123 commits
- merge base: `0d618bf67963ba0022880a730db2dafb3d77d3fa`

Therefore Customer Closure is historically valid, but its implementation is not currently present on `main`.

## Current main Order implementation

`main` contains:

- `App\Models\Order`
- migration `2026_07_15_140347_create_orders_table.php`

The current table contract is:

- `user_id`
- `type`: `buy | sell`
- `status`: `pending | paid | processing | completed | cancelled`
- `gold_weight`
- `gold_price`
- `commission`
- `total_price`
- `description`

Default status is `pending`.

No current `App\Enums\OrderStatus` file was found on `main`.

No current `CustomerOrderStatusController` was found on `main`.

## Customer Closure Order contract

The Customer Closure history contains a different and richer product line, including:

- `App\Enums\OrderStatus`
- `OrderStateMachine`
- Customer Order Status Controller
- Customer API/OpenAPI contracts
- order state migrations
- settlement, custody, delivery, outbox and idempotency infrastructure

This history cannot be treated as already integrated into `main`.

## Stage 03 Order contract

Stage 03 introduces a separate domain aggregate:

- `App\Domain\Trading\Order\Order`

Its lifecycle is:

- `DRAFT`
- `SUBMITTED`
- `APPROVED`
- `REJECTED`
- `EXPIRED`
- `CANCELLED`

It also depends on Quote state, FinancialScope, TraceId, CorrelationId and IdempotencyKey.

This is incompatible with directly persisting into the current `main` orders table without an explicit migration and mapping contract.

## Conflict classification

### Current main Order

Status: **Historical / Insufficient Baseline**

It is a simple Eloquent record and does not represent the accepted Customer Platform, Stage 03 dependency graph or complete Order lifecycle.

### Customer Closure implementation

Status: **Accepted Historical Product Baseline — Not Integrated into main**

The closure remains valid as evidence, but its implementation is absent from current `main`.

### Stage 03 Order aggregate

Status: **Useful Candidate — Blocked by Baseline Reconstruction**

It must not be copied directly onto current `main` before the canonical product baseline is reconstructed.

## Architecture risk

Reconstructing Stage 03 directly on current `main` would create or expose:

1. two incompatible Order status vocabularies;
2. a domain aggregate with no safe persistence mapping;
3. API status responses inconsistent with historical Customer Closure;
4. migration conflicts with historical order migrations;
5. duplicate State Machine implementations;
6. possible loss of Customer, Settlement and Delivery behavior already developed on the diverged product line.

## Recovery decision

Do not begin Stage 03 implementation on current `main` yet.

First reconstruct the canonical integrated baseline by comparing:

1. current `main`;
2. Customer Closure merge commit `5da4da919b0fbd277e3cb1f3cf92c27b93b3868c`;
3. latest `feature/goldplatform-developer-mcp` head;
4. Stage 00–02 merge commits;
5. open production/RC branches only as evidence.

The reconstruction must preserve all valid output and must not use a broad merge, force push, shared-history rebase or blind cherry-pick.

## Required next action

Create a baseline capability matrix for these domains:

- Authentication
- Kimia Read
- Customer API
- Order lifecycle
- Trade
- Financial Kernel
- Settlement
- Custody
- Delivery
- Audit / Outbox / Idempotency
- Permissions
- Production workflows
- Frontends

For every capability classify:

- present on `main`
- present on Customer Closure history
- present on developer-mcp history
- duplicate
- conflicting
- missing
- required clean integration slice

Only after this matrix is complete can a safe canonical base be selected or reconstructed.

## Test status

- Source inspection: **EXECUTED — PASS**
- Git history comparison: **EXECUTED — PASS**
- PHPUnit: **NOT EXECUTED**
- Migration fresh/rollback: **NOT EXECUTED**
- API contract tests: **NOT EXECUTED**
- CI exact SHA: **NOT EXECUTED / NOT RETURNED**
