# Trade → Ledger → Settlement Integration

## Scope

This stage connects approved orders to the existing Trade, FinancialTransaction, Ledger, and Settlement models without introducing new financial formulas or Kimia write behavior.

## Execution boundary

1. The order must already be in `approved` status.
2. Wallet account identifiers must be supplied explicitly by the caller.
3. Trade quantity and unit price are taken from `gold_weight` and `gold_price` on the current Order model.
4. A FinancialTransaction is created for the Trade.
5. LedgerService writes a balanced debit/credit pair.
6. Settlement is created with a unique idempotency key.
7. Settlement moves through `pending → processing → completed` only after Ledger balance validation.
8. FinancialTransaction and Order are marked completed in the same database transaction.
9. Re-executing an already executed order returns the existing Trade and does not duplicate Ledger or Settlement records.

## Explicit non-scope

- No Kimia write endpoint is called.
- No AccountId, ProductId, Action code, wallet account, price formula, commission rule, or customer credit rule is inferred.
- Wallet balances are not updated directly.
- Coin, Currency, Amanat, and physical delivery flows are not covered by this integration.

## Evidence required

The full Laravel test suite must pass before this integration is considered healthy.
