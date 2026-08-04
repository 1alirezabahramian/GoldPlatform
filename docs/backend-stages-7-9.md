# Backend Stages 7–9 — Custody, Delivery, Customer Rules

Status: Implemented pending CI verification

## Stage 7 — Custody / Amanat

Custody is a physical-asset domain and is intentionally separate from Money, Gold, Coin and Currency balances.

Implemented:
- Independent `custody_assets` table.
- Dynamic external product identity; no product ID is hard-coded.
- Quantity, weight, fineness, barcode and branch tracking.
- Idempotent receipt by UUID.
- Guarded lifecycle: in custody, reserved, delivery requested, ready, delivered, resold, converted to gold, converted to money, cancelled.
- Terminal records cannot be consumed twice.
- Resell/conversion requires an explicit financial reference; no Kimia write rule is guessed.

## Stage 8 — Delivery

Implemented:
- Independent and auditable `delivery_requests` table.
- Ownership validation.
- Requested → approved → ready → delivered lifecycle.
- Rejection requires a reason.
- Delivery requires operator, receiver name and verified receiver identifier.
- Double delivery is rejected.
- Delivered custody remains in history and is never silently deleted.

## Stage 9 — Customer Rules and Limits

Rules are configured per `user_group_id` through `customer_trading_policies`; group IDs are never hard-coded.

Supported settings:
- Available-balance requirement.
- Negative balance permission.
- Asset lock duration.
- Gold weight, coin quantity and money limits.
- Credit limit.
- Minimum and maximum order amount.
- Maximum delivery item count.

Confirmed project examples can be stored as configuration:
- Normal: balance required; 24-hour asset lock.
- VIP: optional negative balance; 60-minute asset lock; 50g gold, 10 coins and 1,000,000,000 Toman caps.
- Super VIP: limits remain nullable when unrestricted.

## Boundaries

- Ledger remains the financial source of truth.
- Custody is not a WalletAccount.
- No float is used for financial comparisons.
- No Kimia POST/PUT/DELETE behavior is introduced.
- Conversions and resales require a traceable financial reference.
- All changes are covered by Feature tests and GitHub Actions validation.
