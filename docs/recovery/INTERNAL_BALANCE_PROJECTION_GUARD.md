# Internal Balance Projection Guard

- Status: Implemented — CI Pending
- Canonical base: `recovery/rc2-product-rebuild`
- Kimia remains the final source of truth for Money, Gold, Coin and Currency.
- `BalanceProjectionService` is restricted by contract to audit, traceability, reservations, workflow support and reconciliation.
- Values stored in `wallet_accounts.balance` and `wallet_accounts.blocked_balance` are internal rebuildable projections and must not be presented as authoritative customer balances.
- Custody remains owned by GoldPlatform and is not merged with financial balances.
- No Kimia Write, financial rule, migration, permission or tenant architecture change is introduced by this guard.

## Validation

An architecture test asserts that the projection purpose is `audit_reconciliation_only` and that it is not a customer balance source.
