# Customer API Contract — CP-01 / CP-02

## Status

Implemented on `work/customer-panel-contract-foundation`; final PASS requires green CI for the current head SHA.

## Scope

This stage establishes additive, read-only, versioned customer endpoints without changing financial rules, Kimia write behavior, RC2 workflows, settlement, delivery rules, or wallet rules.

## Versioned endpoints

- `GET /api/v1/customer/dashboard`
- `GET /api/v1/customer/orders`
- `GET /api/v1/customer/custodies`
- `GET /api/v1/customer/deliveries`

Legacy routes remain unchanged.

## Response envelope

Successful responses contain:

- `data`
- `meta.request_id`
- `meta.generated_at`
- `meta.api_version`
- `message`

Paginated responses additionally contain `meta.pagination` with current page, page size, total, last page, and `has_more`.

## Data exposure rules

Customer v1 responses must not expose:

- `user_id`
- database account identifiers
- Kimia/external asset or product identifiers
- internal metadata
- receiver identity values
- operator/admin identifiers

Custody and delivery resources use their existing UUID values as public references.

## Order public reference blocker

The current `orders` table/model has no approved public UUID/reference field. CP-02 therefore does not expose the internal database ID and does not create an order detail route. Adding an order public reference requires a separately reviewed migration and compatibility plan.

## Financial safety

- Decimal values are serialized as strings.
- Frontend performs no financial calculation.
- IRR/IRT display conversion is not inferred in this stage.
- No price, fee, settlement, anti-scalping, wallet, or Kimia write rule is changed.

## Tests

- Authentication and customer-role enforcement
- Stable envelope
- Ownership isolation
- Internal identifier suppression
- Pagination contract
- Empty list behavior

## Completion gate

This work is complete only after the current PR head passes GitHub Actions and any failures are reviewed from actual logs.
