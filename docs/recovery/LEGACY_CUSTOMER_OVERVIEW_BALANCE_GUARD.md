# Legacy Customer Overview Balance Guard

- Status: Implemented — CI Pending
- Scope: `/api/customer/overview`

## Finding

The legacy customer overview manually returned `WalletAccount.balance`, `blocked_balance`, and derived `available_balance`. This bypassed the model serialization guard and exposed internal projections as customer financial balances.

## Decision

The endpoint now returns an empty `balances` collection with explicit Kimia authority/unavailable metadata until the customer account is resolved against Kimia. Non-financial counts for orders, custody, and deliveries remain available.

## Authority

Kimia remains the final source of truth for Money, Gold, Coin, and Currency. Internal Wallet/Ledger projections are limited to audit, reconciliation, idempotency, and workflow evidence.

## Non-goals

- No Kimia Write
- No financial rule or formula
- No migration or data deletion
- No change to custody authority
- No tenant or permission redesign

## Test status

WRITTEN — NOT EXECUTED
