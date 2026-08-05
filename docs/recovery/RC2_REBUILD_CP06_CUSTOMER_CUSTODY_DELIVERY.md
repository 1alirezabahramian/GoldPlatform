# RC2 Rebuild — CP-06 Customer Custody & Delivery

## Status

Implemented — CI pending

## Baseline

- RC2 merge commit: `cada4441184e59d09f5ddac567d7b9b8d19b34ae`
- Working branch: `recovery/rc2-product-rebuild`

## Scope

- `GET /api/v1/customer/custodies/{reference}`
- `GET /api/v1/customer/deliveries/{reference}`
- `POST /api/v1/customer/custodies/{reference}/delivery-request`

## Safety boundaries

- Public UUID reference only
- Ownership enforced by authenticated `user_id`
- Idempotency middleware for delivery request
- Safe presenter response
- No Kimia write
- No financial balance mutation
- No Wallet/Ledger/Settlement change
- No migration

## Tests

- Contract tests restored from the previously verified CP-06 slice
- CI status must be recorded against the exact head SHA before merge

## Recovery classification

`KEEP — REBUILT CLEANLY FROM VERIFIED RC2`
