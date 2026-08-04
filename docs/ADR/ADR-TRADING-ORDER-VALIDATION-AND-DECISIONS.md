# ADR — Trading Order Validation and Idempotent Decisions

Status: Accepted
Date: 2026-08-04

## Context

GoldPlatform requires submitted orders to be validated before approval or rejection. The current approved sources do not authorize wallet mutation, ledger posting, credit checks, commission calculation, settlement or Kimia write behavior at this stage.

## Decision

1. Order decision processing is tenant-scoped.
2. Order, quote and command scope must match exactly.
3. Order must reference the supplied quote.
4. Order and quote must preserve the same CorrelationId.
5. Only Submitted orders may enter approval or rejection.
6. Approval and rejection commands are protected by scope-aware idempotency and concurrency locks.
7. Replaying the same command returns the previously persisted order.
8. Reusing an idempotency key for a different operation or request hash is rejected.
9. Rejection requires a non-empty reason.
10. Decision execution is wrapped in the existing atomic operation contract.

## Explicitly excluded

- wallet balance or reservation changes;
- ledger posting;
- settlement triggers;
- customer group and credit rules;
- commission or pricing formulas;
- Kimia write mappings;
- operator permission policy, pending a separately approved permission matrix.

## Consequences

Order lifecycle decisions are deterministic, traceable and replay-safe without inventing financial behavior. Financial side effects can be connected later behind explicit approved policies.
