# Customer Frontend Core Pages

## Status

Implemented — CI pending

## Scope

This recovery slice adds the five core read-only customer pages directly on the canonical Customer Frontend foundation:

- Dashboard → `GET /api/v1/customer/dashboard`
- Assets → `GET /api/v1/customer/assets`
- Orders → `GET /api/v1/customer/orders`
- Custody → `GET /api/v1/customer/custodies`
- Profile → `GET /api/v1/customer/profile`

## Behavior

Each page uses the shared typed Customer API client and presents explicit Loading, Empty, Error and Unavailable states.

The Dashboard and Assets APIs currently return a controlled `503 KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED` response until the authenticated customer is resolved against Kimia. The frontend displays this as unavailable and never converts it to zero.

Orders and Custody use the canonical customer-scoped read contracts. Custody remains separate from Money, Gold, Coin and Currency.

## Safety boundaries

- Read-only endpoints only.
- No financial calculation or Rial/Toman conversion.
- No mock balance, fake dashboard or hard-coded financial value.
- No direct Kimia request or identifier.
- No automatic mutation retry.
- No Ledger, Wallet, Voucher or Settlement terminology in customer pages.
- No Backend, API, migration, permission or tenant architecture change.

## Test status

- Core route contract tests: WRITTEN — NOT EXECUTED locally
- Typecheck: NOT EXECUTED locally
- Production build: NOT EXECUTED locally
- Backend regression: NOT EXECUTED for this Head SHA
- GitHub Actions: PENDING
