# Trading Engine v1

## Scope

This version completes the internal execution chain for an already-approved order:

`Order -> Trade -> FinancialTransaction -> Ledger -> Settlement -> Completed Order`

It does not guess or perform a Kimia write operation. A Kimia reference may only be attached when an external, separately verified adapter supplies it.

## Preconditions

- Order must be persisted and have status `approved`.
- Source and destination ledger account ids must be supplied explicitly.
- Ledger asset unit must be supplied explicitly.
- Source and destination accounts must be different.

## Guarantees

- Execution runs inside one database transaction.
- The Order row is locked before execution.
- A second execution of a completed Order returns the existing Trade.
- No hard-coded ledger account id is used.
- Order financial values are read from `gold_weight`, `gold_price`, and `total_price`.
- FinancialTransaction contains a balanced debit/credit pair for the supplied asset unit.
- Settlement completes only after Ledger balance validation.
- Order completes only after Settlement succeeds.
- A failure before commit rolls back Trade, FinancialTransaction, LedgerEntry, Settlement, and state changes together.

## State sequence

`approved -> executing -> settling -> completed`

Failures during `executing` or `settling` must be handled through the Order State Machine and require an explicit reason.

## Excluded pending business decisions

- Selection of customer/platform ledger accounts for each order type.
- Kimia write endpoint, payload, transaction/action code, and retry policy.
- Product-specific commission and price formulas not already approved in project rules.
- Balance reservation and release policy, which belongs to the Balance Projection phase.
