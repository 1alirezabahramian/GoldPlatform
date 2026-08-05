# HTTP Financial Model Mutation Boundary

Status: Accepted

## Boundary

Controllers and route closures must not directly create, update or delete financial records such as Wallet, WalletAccount, WalletTransaction, FinancialTransaction, LedgerEntry, BalanceReservation or Settlement.

HTTP layers must validate and authorize requests, then delegate approved mutations to application services that provide idempotency, audit, workflow and reconciliation controls.

This boundary does not make internal financial models the source of truth for customer Money, Gold, Coin or Currency balances. Kimia remains the final source of truth for those balances. Custody remains a separate GoldPlatform-owned domain.

## Safety

- No Kimia Write was enabled.
- No financial rule, Action Code or payload was introduced.
- No migration or production behavior was changed.
- The boundary is enforced by `HttpFinancialModelMutationBoundaryTest`.
