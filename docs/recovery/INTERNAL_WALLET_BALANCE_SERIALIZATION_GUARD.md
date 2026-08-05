# Internal Wallet Balance Serialization Guard

Status: Accepted

## Decision

`WalletAccount.balance`, `WalletAccount.blocked_balance`, and the derived `available_balance` accessor are internal projection fields. They are hidden from automatic model serialization and must not be exposed by API or Admin responses as customer financial balances.

Kimia remains the final source of truth for customer Money, Gold, Coin, and Currency balances.

## Preserved behavior

- Existing columns and historical data remain unchanged.
- Internal Ledger, reservation, audit, and reconciliation workflows remain available.
- Internal code may still read the projection fields where explicitly required for audit/reconciliation.

## Safety

- No Kimia Write.
- No migration.
- No financial formula or Action Code.
- No deletion of Wallet, Ledger, Reservation, or historical records.
