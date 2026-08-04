# OP-05 — Operator Queue Workspace

## Scope
- Versioned read-only order queue
- Versioned read-only delivery queue
- Status filtering and pagination
- Safe list/detail presentation in the operator frontend

## Endpoints
- `GET /api/v1/operator/orders/queue`
- `GET /api/v1/operator/deliveries/queue`

## Security boundaries
- Protected by Sanctum and `operator|admin` role middleware.
- No order approve/reject/cancel endpoints are introduced.
- Receiver identity, metadata, internal external asset identifiers, Kimia references, wallet and ledger data are not exposed.
- Frontend visibility does not replace backend authorization.

## Data rules
- Only active operational states are listed.
- `per_page` is clamped to 1..50.
- Unknown status filters are rejected.
- Decimal quantities remain strings.

## Testing status
Feature and frontend contract tests are included. They were not executed in the implementation environment.
