# AP-03 — Safe Operational Queues

## Goal

Provide versioned, read-only and explicitly presented operational queues for Admin and Operator without exposing raw Eloquent models or internal payloads.

## Added endpoints

- `GET /api/v1/operator/orders/queue`
- `GET /api/v1/operator/deliveries/queue`
- `GET /api/v1/admin/audit-logs`
- `GET /api/v1/admin/outbox`

## Reused foundations

- AP-01 role and permission catalog
- AP-02 versioned response envelope
- existing Order, DeliveryRequest, AuditLog and OutboxMessage models
- existing rate limits and authentication

No duplicate domain service or financial model was introduced.

## Safe response rules

### Order queue
Allowed fields are limited to order identifier, type, asset classification, quantity/unit, status and operational dates.

### Delivery queue
Allowed fields are limited to public UUID, branch code, requested date, status and creation time.

The response excludes receiver identity, customer identity, metadata and internal foreign keys.

### Audit
The response excludes `before`, `after`, `metadata`, IP address and user agent. Only operational correlation fields are exposed.

### Outbox
The response excludes `payload`, aggregate identifier and last error. Only event type and dispatch timing/status fields are exposed.

## Pagination and filters

- default `per_page`: 25
- minimum: 1
- maximum: 50
- order status filter is limited to current queue states
- delivery status filter is limited to current queue states
- audit supports exact `action` and `request_id`
- outbox supports exact `event_type`

Unsupported queue statuses return HTTP 422 rather than silently expanding scope.

## Safety boundaries

- no migration
- no Ledger, Wallet or Settlement change
- no Kimia call or write
- no Branch/Tenant assumption
- no deletion of legacy routes
- no direct model serialization
- no financial calculation

## Tests

`AdminOperatorOperationalQueueContractTest` covers:

- versioned response envelope
- pagination contract
- absence of customer and receiver identity
- absence of Audit snapshots and metadata
- absence of Outbox payload
- Admin/Operator isolation
- rejection of unsupported status filters

## Remaining risks

- Legacy unversioned endpoints still expose raw model pagination and must be deprecated only after consumer migration.
- Branch/Tenant scoping is unresolved and therefore these reads remain global within the current single-company architecture.
- CI must pass before this stage is considered complete.
