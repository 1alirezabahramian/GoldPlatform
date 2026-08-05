# Balance Reservation Authority Guard

Status: Implemented — CI Pending

## Decision

`BalanceReservationService` records workflow intent only.

It is not allowed to decide whether a customer has sufficient Money, Gold, Coin or Currency by reading an internal Ledger, WalletAccount balance or BalanceProjection snapshot.

Kimia remains the final authority for those customer financial balances.

## Preserved behavior

- Positive reservation amounts are validated.
- Reservation idempotency is preserved.
- Active, released and consumed lifecycle states are preserved.
- Internal projection rebuild remains available for audit, trace and reconciliation views.

## Removed authority

The service no longer rejects a reservation using `Insufficient available balance` derived from the internal projection.

## Safety

- No Kimia Write.
- No financial formula or action code.
- No migration.
- No deletion of Wallet, Ledger, Reservation or historical records.
- No tenant or permission redesign.
