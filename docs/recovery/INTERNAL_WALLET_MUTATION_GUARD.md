# Internal Wallet Mutation Guard

Status: Accepted — Recovery Safety Boundary

GoldPlatform must not mutate an independent customer balance for Money, Gold, Coin or Currency.

`App\Services\Wallet\WalletService` is retained only as a historical compatibility shell. Its `deposit` and `withdraw` methods must remain disabled until a future, explicitly approved non-customer-balance purpose is defined.

Authoritative customer financial balances come from Kimia. Internal Ledger, Reservation and Projection data remain limited to audit, trace, idempotency, workflow and reconciliation.

This change does not enable Kimia Write, define a financial rule, alter a migration, or delete historical wallet data.
