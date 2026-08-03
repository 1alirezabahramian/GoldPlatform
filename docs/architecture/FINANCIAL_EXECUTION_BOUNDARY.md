# Financial Execution Boundary

## Current status

The HTTP order creation path may create a validated pending order only.

The following operations are not approved for HTTP execution yet:

- Trade execution
- Ledger posting
- FinancialTransaction completion
- Kimia voucher write
- Settlement completion

## Reason

The existing experimental `TradeService` still depends on unresolved items:

- order fields that do not exist in the current order schema
- hard-coded wallet account IDs
- undefined account resolution rules
- missing idempotency contract
- missing balancing and audit contract
- missing failure-recovery behavior

## Enforcement

An architecture test scans routes and HTTP controllers and fails if `TradeService` becomes reachable from the HTTP layer.

This boundary may only be relaxed after the relevant business rules and Kimia write contract are approved and tested.
