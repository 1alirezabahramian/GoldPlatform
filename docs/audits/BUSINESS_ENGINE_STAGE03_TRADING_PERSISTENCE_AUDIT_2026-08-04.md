# Business Engine Stage 03 — Trading Persistence Audit

Date: 2026-08-04
Status: Implemented, CI pending

## Implemented

- Tenant-scoped `trading_quotes` schema.
- Tenant-scoped `trading_orders` schema.
- Database-backed quote repository.
- Database-backed order repository.
- Container bindings through `TradingServiceProvider`.
- Quote-to-order round-trip integration coverage.
- Cross-tenant reads rejected by scoped repository queries.
- Order persistence requires the referenced quote to exist in the same financial scope.
- Quote deletion is restricted while an order references it.

## Persistence boundary

The schema currently persists lifecycle and traceability only:

- identity,
- financial scope,
- trace,
- correlation,
- idempotency,
- lifecycle status,
- timestamps,
- rejection reason.

## Explicitly not persisted yet

No unapproved trading or financial fields were invented:

- price,
- pricing formula,
- quantity or weight,
- commission,
- spread,
- customer-group limits,
- wallet reservation,
- ledger posting reference,
- settlement reference,
- delivery reference,
- Kimia mapping or voucher reference.

These fields require approved domain rules and separate ADRs before schema changes.

## Remaining validation

- GitHub Actions result for the latest commit.
- MySQL integration run in the project execution environment.
- Real Redis multi-process lock test.
- Atomic persistence test spanning Quote use and Order creation as one application command.
