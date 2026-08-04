# GoldPlatform — AP-00 Admin & Operator Discovery and Architecture Contract

**Owner:** Alireza Bahramian  
**Phase:** Admin & Operator Platform  
**Base branch:** `feature/goldplatform-developer-mcp`  
**Work branch:** `work/admin-operator-ap00-discovery`  
**Date:** 2026-08-04

## 1. Purpose

This document records the verified starting point for the GoldPlatform Admin and Operator platform. It introduces no new financial rule, Kimia write mapping, Ledger rule, settlement rule, or custody rule.

The implementation must follow:

- NO GUESSING
- NO REINVENTING
- NO SILENT CHANGES
- Complex Backend — Simple Frontend

## 2. Verified Existing Foundation

The current backend already contains:

- Sanctum authentication
- Spatie `HasRoles` on `User`
- role-based route protection for `customer`, `operator`, and `admin`
- separate throttles for customer, operator, and admin routes
- audit logging
- outbox foundation
- idempotency middleware on write routes
- Order, Custody, Delivery, and Customer Trading Policy models/services
- operational health and observability foundations from previous stages
- versioned customer API and safe customer presenters/read models

These components must be reused rather than recreated.

## 3. Existing Admin Routes

Current prefix: `/api/admin`

- `GET /audit-logs`
- `GET /outbox`
- `GET /customer-policies`
- `PUT /customer-policies/{policy}`

Current route guard:

- `auth:sanctum`
- `throttle:admin`
- `role:admin`

## 4. Existing Operator Routes

Current prefix: `/api/operator`

- `GET /orders/queue`
- `GET /deliveries/queue`
- `POST /deliveries/{deliveryRequest}/approve`
- `POST /deliveries/{deliveryRequest}/ready`
- `POST /deliveries/{deliveryRequest}/deliver`

Current route guard:

- `auth:sanctum`
- `throttle:operator`
- `role:operator|admin`

Write routes already use idempotency middleware.

## 5. Verified Gaps

### 5.1 Authorization

Current routes use broad roles only. A verified permission seeder or detailed Admin/Operator permission matrix was not found in the inspected baseline.

The existing `DatabaseSeeder` only creates a test user and does not establish production Admin/Operator roles or permissions.

Therefore, no permission names are final until AP-01 defines and tests the permission contract.

### 5.2 API Safety and Contract

Current Admin and Operator controllers serialize Eloquent models and paginators directly.

Risks:

- accidental exposure of internal identifiers
- exposure of metadata or future sensitive columns
- unstable response shape
- inconsistent pagination and error format
- difficult frontend versioning
- weak White-label boundary

Admin/Operator APIs must adopt explicit presenters/resources and a stable response envelope, following the safe customer API pattern already present in the project.

### 5.3 Tenant and Branch Scope

No verified tenant or branch scoping contract was found in the inspected Admin/Operator routes and controllers.

White-label and branch access must not be simulated with guessed columns or filters. AP-01 may define interfaces and stop conditions, but database changes require an accepted tenant/branch architecture decision.

### 5.4 Order Operations

The current Operator order queue is read-only and filters these existing status strings:

- `pending`
- `approved`
- `executing`
- `settling`

No new approve/reject/cancel/settle operation is authorized by this document. Order write actions must reuse the accepted Order state machine and Business Engine services.

### 5.5 Delivery Operations

Delivery actions already reuse `DeliveryService` and are wrapped in database transactions, audit logging, outbox events, and idempotency middleware.

This path is canonical and must be extended rather than replaced.

### 5.6 Customer Trading Policy Administration

The current Admin controller can update policy fields including balance requirements, negative-balance permission, lock time, trade ceilings, credit limit, order limits, delivery limits, and active status.

These fields affect financial and trading behavior. AP-01 must not rename, reinterpret, or add defaults to them without an approved Business Rule source.

### 5.7 Audit and Outbox

Admin read endpoints exist, but filtering, access scope, redaction, retention, and export policies are not yet defined.

Raw outbox payload exposure must be reviewed before a production UI is built.

## 6. Canonical Architecture Boundary

```text
Admin / Operator Frontend
        ↓
Versioned Admin / Operator API
        ↓
Authentication + Role + Permission + Scope
        ↓
Request Validation
        ↓
Application Service / Existing Domain Service
        ↓
Ledger / Order / Delivery / Custody / Kimia Adapter
        ↓
Audit Log + Outbox + Stable API Result
```

Frontend code must not:

- calculate financial balances
- choose Kimia Action codes
- mutate wallet balances directly
- bypass Ledger or domain services
- infer settlement completion
- expose internal Kimia, voucher, record, model, or database identifiers unless explicitly approved

## 7. AP-01 Scope — Admin Foundation

AP-01 is limited to foundation work:

1. versioned Admin and Operator API namespaces
2. explicit API response envelope
3. safe presenters/resources
4. permission catalog and middleware contract
5. role-to-permission seed strategy
6. authorization tests
7. ownership/scope guard tests
8. audit requirements for sensitive operations
9. documentation and OpenAPI contract

AP-01 must not introduce:

- a new financial calculation
- a new wallet adjustment rule
- a Kimia write operation
- a new settlement workflow
- a new custody state
- a new delivery state
- a guessed branch or tenant database model

## 8. Initial Permission Domains — Names Not Yet Final

The following domains are candidates for AP-01 design, not accepted business permissions yet:

- admin dashboard read
- operator dashboard read
- order queue read
- order action execution
- delivery queue read
- delivery action execution
- custody read/action
- customer read/status management
- policy read/update
- audit read/export
- outbox read/retry
- system health read
- Kimia health/read/sync/write separation
- role and permission administration

Sensitive permissions must remain separate. In particular, Kimia write, financial adjustment, settlement approval, and role administration must never be bundled into a generic operator role.

## 9. Test Gate for AP-01

Minimum required tests:

- unauthenticated access denied
- customer role denied
- operator can access only operator capabilities
- admin inheritance is explicit and tested, not assumed
- missing permission returns 403
- direct model fields are not leaked
- pagination contract remains stable
- write endpoint idempotency remains enforced
- delivery actions still use existing service/state validation
- policy update retains audit and outbox behavior
- no Kimia write is enabled

## 10. Current Risks

- broad role-only authorization
- no verified production role/permission seeding
- direct Eloquent serialization
- possible exposure of outbox payloads and internal IDs
- no verified tenant/branch scope
- financial policy fields are editable through one broad admin role
- parallel active PRs may change the base branch; AP work must rebase and rerun tests before merge

## 11. Decision

The existing Admin and Operator controllers and routes are the starting implementation, not disposable prototypes.

AP-01 will harden and version this foundation incrementally. No duplicate Admin module, Operator module, Delivery service, Audit service, Outbox service, Order model, or policy model will be created.

## 12. Next Step

Proceed to AP-01 design and implementation on a fresh branch based on the latest accepted `feature/goldplatform-developer-mcp` state after active PR coordination.
