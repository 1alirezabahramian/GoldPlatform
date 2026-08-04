# AP-02 — Admin & Operator Safe Read Dashboards

## Status

Implemented as a guarded, read-only slice. Final PASS requires CI on the branch SHA.

## Goal

Provide versioned dashboards for Admin and Operator using existing operational data without creating financial formulas, Kimia write behavior, or duplicate services.

## Endpoints

- `GET /api/v1/admin/dashboard`
- `GET /api/v1/operator/dashboard`

Both endpoints require Sanctum authentication, role boundary, access permission, dashboard-specific permission, and the existing Admin/Operator rate limiter.

## Reused foundations

- Existing `Order` model and confirmed order statuses
- Existing `DeliveryRequest` model and confirmed delivery statuses
- Existing `AuditLog` and `OutboxMessage`
- Existing failed-jobs table when present
- AP-01 permission catalog and seeder
- Existing request context and request ID

No new operational service, entity, migration, ledger rule, wallet rule, settlement rule, or Kimia adapter was created.

## Admin snapshot

The Admin dashboard exposes only confirmed operational signals:

- total, actionable, and failed order counts
- total, actionable, and ready delivery counts
- failed job count when the table exists
- total outbox message count
- latest audit timestamp

Revenue, gold holdings, wallet totals, settlement value, and Kimia health are intentionally absent because their authoritative calculation or read contract is outside this slice.

## Operator snapshot

The Operator dashboard exposes:

- actionable order count
- actionable delivery count
- ready delivery count
- up to 10 oldest actionable orders with explicit safe fields
- up to 10 oldest actionable delivery requests with explicit safe fields

The response does not expose national code, receiver identifier, model metadata, Kimia identifiers, wallet balances, or raw model serialization.

## Response contract

```json
{
  "data": {},
  "meta": {
    "request_id": "...",
    "generated_at": "ISO-8601",
    "api_version": "v1"
  },
  "message": null
}
```

## Permissions

Added:

- `admin.dashboard.view`
- `operator.dashboard.view`

The Admin role receives both through the current AP-01 defaults. The Operator role receives only `operator.dashboard.view` in addition to its verified operational permissions.

## Files

- `backend/app/Support/AdminOperatorApiResponse.php`
- `backend/app/ReadModels/AdminOperatorDashboardReadModel.php`
- `backend/app/Http/Controllers/Api/V1/AdminDashboardController.php`
- `backend/app/Http/Controllers/Api/V1/OperatorDashboardController.php`
- `backend/app/Support/AdminOperatorPermissionCatalog.php`
- `backend/routes/api.php`
- `backend/tests/Feature/AdminOperatorDashboardContractTest.php`

## Tests added

- Admin versioned envelope and shape
- Operator task dashboard shape
- absence of selected sensitive fields
- Operator denial from Admin dashboard
- explicit permission boundary

## Remaining risks

- Legacy Admin/Operator endpoints still return model-backed responses and require a separate safe-read migration.
- Dashboard signals are global because Tenant and Branch scope are not yet confirmed.
- `outbox_messages` is a total count, not a pending count, because status semantics were not assumed.
- Kimia health is not shown until an approved read-only health contract is connected.
- No claim is made about CI PASS until GitHub Actions completes on the final SHA.

## Next stage

AP-03 should migrate legacy Admin/Operator list endpoints to versioned presenters and safe pagination, beginning with order queue, delivery queue, audit log, and outbox views.
