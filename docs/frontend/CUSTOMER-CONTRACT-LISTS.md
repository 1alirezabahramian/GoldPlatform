# Customer Contract-Driven Lists

Status: Implemented — CI Pending

## Scope

This stage maps the accepted canonical Customer API resources into typed, customer-facing lists for orders, physical custody, and delivery requests.

## Ground truth

- `OrderResource`
- `CustodyResource`
- `DeliveryResource`
- Customer pagination, status, sort, and date-filter contracts

## Implemented

- Typed response models preserving decimal values as strings.
- Professional responsive cards for orders, custody, and deliveries.
- Explicit loading, empty, unavailable, error, and ready states.
- Safe Persian status labels with fallback to the contract value.
- No unavailable value is converted to zero.
- No financial calculation, Rial/Toman conversion, Weight750 calculation, Kimia identifier, Wallet, Ledger, or Voucher terminology.
- Physical custody remains visually and semantically separate from financial assets.

## Boundaries

Dashboard and Kimia-backed asset cards are not implemented in this stage because their exact canonical response schemas require separate mapping. No API route, backend contract, permission, tenant architecture, or financial rule changed.

## Validation

- Contract tests: WRITTEN — NOT EXECUTED
- Typecheck: NOT EXECUTED
- Production build: NOT EXECUTED
- Browser E2E: NOT EXECUTED
- Visual screenshot audit: NOT EXECUTED
