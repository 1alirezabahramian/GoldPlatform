# AP-01 — Admin & Operator Permission Foundation

Date: 2026-08-04
Owner: Alireza Bahramian
Base branch: `feature/goldplatform-developer-mcp`
Implementation branch: `work/admin-operator-ap01-foundation`

## Goal

Add a minimal, explicit authorization foundation for the existing Admin and Operator APIs without creating new financial rules, Kimia mappings, Ledger behavior, or duplicate services.

## Reused Existing Components

- Existing `AdminPanelController`
- Existing `OperatorPanelController`
- Existing Spatie Permission installation and `HasRoles` user trait
- Existing role middleware
- Existing rate limits
- Existing idempotency middleware
- Existing Audit, Outbox, DeliveryService, Order and Delivery models

## Implemented

1. Added `AdminOperatorPermissionCatalog` as the only code-level source for verified management permissions.
2. Added `AdminOperatorPermissionSeeder`.
3. Registered the Spatie permission middleware alias.
4. Added granular permission middleware to every existing Admin and Operator route.
5. Added feature tests for role defaults, allowed operator access, admin isolation and permission denial.

## Verified Permission Scope

The catalog intentionally contains only permissions mapped to routes that already exist:

- `admin.access`
- `operator.access`
- `audit.view`
- `outbox.view`
- `customer-policies.view`
- `customer-policies.update`
- `orders.queue.view`
- `deliveries.queue.view`
- `deliveries.approve`
- `deliveries.ready`
- `deliveries.complete`

No future-sensitive permission such as wallet adjustment, settlement approval, Kimia write, role assignment, tenant administration or pricing update was added.

## Default Roles

### Admin

Receives all permissions in the verified AP-01 catalog.

### Operator

Receives only:

- operator access
- order queue view
- delivery queue view
- approve delivery
- mark delivery ready
- complete delivery

The Operator role does not receive Audit, Outbox or Customer Policy permissions.

## Safety Boundaries

- No financial calculation changed.
- No Wallet or Ledger mutation path changed.
- No Kimia read or write behavior changed.
- No migration was added.
- No new business state or workflow was invented.
- Existing service calls, transactions, Audit and Outbox behavior remain unchanged.
- Legacy route paths remain available; only authorization was hardened.

## Tests

Added:

`tests/Feature/AdminOperatorPermissionFoundationTest.php`

Coverage:

- Operator default permissions are limited.
- Authorized Operator can view the order queue.
- Operator cannot access Admin Audit.
- Role membership alone is insufficient when the required permission is absent.

## Validation Status

Code and tests are committed. Final PASS must be based on GitHub Actions or an equivalent valid Laravel test execution for the exact branch SHA.

## Remaining Risks

1. Existing controllers still serialize Eloquent models directly.
2. Admin and Operator APIs are not yet versioned.
3. No safe presenter/resource contract exists for management responses.
4. No Tenant or Branch authorization boundary is confirmed.
5. Existing policy update fields are financially sensitive and require a later approval workflow review.
6. Existing raw Outbox response requires a sensitive-data review.

## Next Stage

AP-02 should introduce safe read contracts and dashboards without adding new financial rules:

- Admin operational summary
- Operator work queue summary
- Safe presenters instead of direct model serialization
- Health, failed jobs, orders, deliveries, Audit and Kimia safety indicators from existing sources
