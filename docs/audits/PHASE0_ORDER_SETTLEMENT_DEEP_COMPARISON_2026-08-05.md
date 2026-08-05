# GoldPlatform — Order & Settlement Deep Comparison

> Date: 2026-08-05
>
> Status: Evidence recorded — no integration
>
> Compared refs: `main`, `feature/goldplatform-developer-mcp`, `work/business-engine-stage03-trading-validation`

## 1. Main Order model

`main` contains a simple Eloquent `Order` with fields such as `type`, `status`, `gold_weight`, `gold_price`, `commission` and `total_price`.

Classification: `LEGACY PERSISTENCE MODEL — INSUFFICIENT CONTRACT`.

It does not define a formal lifecycle, quote linkage, idempotency identity, trace/correlation data, tenant scope or settlement workflow.

## 2. Historical product Order model

The historical product model adds:

- dynamic asset identity (`asset_type`, `external_asset_id`, `asset_quantity`, `asset_unit`);
- timestamped lifecycle fields;
- enum-backed status;
- settlement and balance-reservation relations;
- optimistic state version.

Historical status enum:

`pending → approved → executing → settling → completed`

Terminal alternatives:

`rejected`, `expired`, `cancelled`, `failed`.

Classification: `HIGH-VALUE PERSISTENCE DONOR — CONTRACT NOT YET CANONICAL`.

The dynamic asset fields and transition timestamps are useful. However the model is coupled to legacy balance reservations and a historical state machine that must be reconciled with Stage 03.

## 3. Stage 03 domain Order

Stage 03 defines an immutable domain aggregate created only from a used Quote and requires:

- FinancialScope;
- TraceId;
- CorrelationId;
- IdempotencyKey;
- Quote linkage.

Lifecycle:

`draft → submitted → approved`

or terminal outcomes:

`rejected`, `expired`, `cancelled`.

Classification: `STRONG DOMAIN DONOR — PRE-EXECUTION LIFECYCLE ONLY`.

Stage 03 is stronger for quote-to-order intent, idempotency, traceability and approval/rejection. It does not by itself model execution, settlement, completion or failure.

## 4. Contract conflict

The two richer contracts cover different layers:

- Stage 03: quote consumption, draft/submission, approval decision;
- historical product: execution, settlement and terminal processing.

Treating either as a complete replacement would lose valid lifecycle stages.

Canonical interpretation candidate:

1. `draft`
2. `submitted`
3. `approved`
4. `executing`
5. `settling`
6. `completed`

Terminal alternatives, subject to transition rules:

- `rejected`
- `expired`
- `cancelled`
- `failed`

This is an architectural inference from existing code, not yet an accepted product contract. It must be validated against accepted Customer API status documents before implementation.

## 5. Persistence boundary required

The final design should not keep two independent state machines.

Required structure:

- one canonical domain lifecycle;
- one persistence model/table contract;
- explicit mapper between domain aggregate and Eloquent record;
- one transition service or state machine;
- API presenters that expose customer-safe status names;
- versioned transition writes with idempotency and audit.

The historical Eloquent model is a persistence donor. The Stage 03 aggregate is a domain donor. Neither should be copied unchanged.

## 6. Historical SettlementService

Positive properties:

- idempotent pending creation;
- row locking;
- explicit `pending → processing → completed/failed` lifecycle;
- financial transaction linkage;
- Kimia reference field;
- failure reason and timestamps;
- repeat completion/failure handling.

Critical conflicts:

- `completeWithLedger()` accepts local ledger balance as a completion prerequisite;
- it can call `complete()` with a nullable Kimia reference;
- it does not prove that a successful Kimia Write occurred;
- it is coupled to the legacy LedgerService and historical FinancialTransaction model;
- no explicit Intent/Result record or post-write Kimia balance refresh is enforced.

Classification: `WORKFLOW DONOR — COMPLETION CONTRACT UNSAFE`.

## 7. Canonical settlement rule

Settlement may retain local workflow states, idempotency, locks, failure details and trace data.

For operations that require Kimia Write, completion must not be based only on a balanced local ledger. Completion requires a verified Kimia result under an accepted write contract, followed by a Kimia read refresh where applicable.

Until Kimia Write Ground Truth is approved:

- create/prepare/validate settlement intent may exist;
- write execution remains disabled;
- final financial completion remains blocked;
- no local balance projection may substitute for Kimia result.

## 8. Keep / adapt / supersede

### Keep as donors

- Stage 03 Quote-to-Order identity and idempotency;
- Stage 03 trace/correlation and tenant scope;
- historical dynamic asset identity;
- historical execution/settlement timestamps;
- historical row-locking and idempotent settlement creation;
- explicit rejection/failure reasons;
- state versioning.

### Adapt

- merge pre-execution and execution lifecycle into one contract;
- map domain Order to persistence record explicitly;
- separate settlement Intent, Kimia Result and Reconciliation states;
- require non-null verified Kimia reference/result for Kimia-backed completion;
- ensure Coin and Currency IDs remain dynamic.

### Supersede

- simple status string usage without state contract;
- completion based solely on local ledger balance;
- settlement completion with no verified Kimia result for operations requiring Kimia Write;
- order availability checks based on legacy wallet balances.

## 9. Required tests before reconstruction

- complete transition matrix;
- invalid transition tests;
- idempotent submit/approve/reject/cancel/expire;
- optimistic version/concurrency tests;
- tenant and company isolation;
- dynamic Coin/Currency asset identity;
- customer API status compatibility;
- settlement Intent/Result/Reconciliation tests;
- Kimia Write deny-by-default tests;
- no completion without verified Kimia result;
- post-write Kimia read refresh contract;
- no legacy wallet balance authorization.

## 10. Current conclusion

Order and Settlement are recoverable, but neither existing line is canonical alone.

Status:

- `main Order`: `INSUFFICIENT / SUPERSEDE`;
- historical Order persistence: `HIGH-VALUE DONOR`;
- Stage 03 Order domain: `HIGH-VALUE DONOR`;
- historical Settlement workflow: `DONOR — COMPLETION CONTRACT MUST BE REBUILT`.

No Stage 03 or historical Settlement file should be merged directly.
