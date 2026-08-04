# ADR — Trading Order Lifecycle

Status: Accepted
Date: 2026-08-04

## Context

GoldPlatform requires a tenant-scoped order lifecycle connected to a consumed quote. The order contract must remain independent from pricing formulas, wallet mutation, ledger posting, settlement and Kimia write behavior until those rules are approved from authoritative project sources.

## Decision

1. An Order is created only from a Quote in `used` state.
2. The Order inherits the Quote `FinancialScope` and `CorrelationId`.
3. The Order receives its own immutable UUID, TraceId and IdempotencyKey.
4. The implemented lifecycle is:

   `draft -> submitted -> approved`

   with terminal alternatives:

   `rejected`, `expired`, `cancelled`.

5. Rejection requires a non-empty reason.
6. Only draft orders can be submitted.
7. Only submitted orders can be approved, rejected or expired.
8. Draft or submitted orders can be cancelled.
9. Repository access always requires `FinancialScope`; cross-tenant reads and writes are forbidden.
10. `settled` and `delivered` are reserved statuses from the approved master lifecycle, but no transition into them is implemented in this stage.

## Explicitly excluded

- Price or commission formulas
- Customer-group limits
- Wallet reservation or mutation
- Ledger posting
- Settlement execution
- Delivery execution
- Kimia write mappings
- Permission policy details

## Consequences

- Order state transitions are explicit and testable.
- A Quote cannot silently produce an Order before it is consumed.
- Settlement and Delivery engines can add their transitions later without redefining earlier states.
