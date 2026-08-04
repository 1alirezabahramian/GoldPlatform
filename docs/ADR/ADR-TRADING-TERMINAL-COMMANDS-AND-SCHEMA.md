# ADR — Trading Terminal Commands and Persistence Schema

## Status
Accepted for Stage 03 foundation.

## Decision

Order expiration and cancellation are executed through an idempotent, tenant-scoped command service protected by the existing concurrency and atomic-operation contracts.

Trading persistence is introduced through two new tables:

- `trading_quotes`
- `trading_orders`

Both tables store explicit tenant, company and branch scope columns plus a deterministic SHA-256 scope hash.

## Constraints

- Expiration is allowed only through the existing Order aggregate transition.
- Cancellation is allowed only through the existing Order aggregate transition.
- Repeated identical commands return the persisted result.
- Reuse of the same idempotency key for another request is rejected.
- Orders reference quotes with a restrictive foreign key.
- One order per quote is enforced inside each financial scope.

## Explicitly excluded

- Price or quantity columns
- Pricing formulas
- Commission and spread
- Wallet reservation
- Ledger posting
- Settlement and delivery transitions
- Kimia write mappings

These remain blocked until their business rules and mappings are confirmed by authoritative project sources.
