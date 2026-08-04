# Backend Stages 10–11

## Stage 10 — Panel APIs

### Customer

- `GET /api/customer/overview`
- `GET /api/customer/orders`
- `GET /api/customer/custody`
- `GET /api/customer/deliveries`
- `POST /api/customer/custody/{custodyAsset}/delivery`

Customer responses expose simple concepts only: balances, orders, custody and delivery. Kimia identifiers and accounting vocabulary are not introduced into the customer contract.

### Operator

- order and delivery queues
- approve delivery
- mark ready
- complete delivery with receiver identity

### Admin

- audit log review
- outbox review
- customer trading-policy review and update

All panel routes require Sanctum authentication and explicit roles.

## Stage 11 — Audit, Idempotency and Outbox

- `X-Request-Id` correlation is generated or preserved on every request.
- financial or operational writes require `Idempotency-Key`.
- replay with the same request returns the stored response.
- reuse with a different request is rejected.
- audit logs are append-only application records with actor, subject, request, before and after snapshots.
- outbox messages are written in the same database transaction as domain changes.

## Boundaries

- No Kimia write payload or action mapping was added.
- Audit records must not contain credentials or secrets.
- Outbox publishing workers are a later infrastructure step; stage 11 establishes reliable persistence and inspection.
