# Customer Kimia Source State

Status: Implemented — CI Pending

## Verified canonical behavior

- `GET /api/v1/customer/dashboard` returns HTTP 503 with `KIMIA_FINANCIAL_BALANCE_SOURCE_REQUIRED` until the customer account is resolved against Kimia.
- `GET /api/v1/customer/assets` and its Money, Gold, Coin, and Currency subresources return the same fail-closed state.
- No customer financial balance may be read from internal Wallet, Ledger, Journal, or Projection data.
- Custody remains outside this financial source state and continues to be owned by GoldPlatform.

## UX implementation

- Dashboard explains that Money, Gold, Coin, and Currency are Kimia-backed.
- No balance, valuation, or placeholder number is displayed.
- Coin and Currency are described as dynamic catalogs, without hard-coded identifiers.
- The live `/dashboard` and `/assets` read contracts remain connected so the UI will continue to reflect the Backend state.
- Unavailable information is not converted to zero.

## Blocker

Real financial cards remain blocked until the canonical Backend can resolve the authenticated customer to a verified Kimia account and return accepted customer-safe balance resources.

## Validation

- Contract tests: WRITTEN — NOT EXECUTED
- Typecheck: NOT EXECUTED
- Production build: NOT EXECUTED
- Browser E2E: NOT EXECUTED
