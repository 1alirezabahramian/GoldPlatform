# Backoffice Permission Foundation — Reconstruction V1

> Status: Implemented — Not Tested
>
> Branch: `reconstruct/permission-foundation-v1`
>
> Base: `main`

## Scope

This slice establishes the minimum safe Admin/Operator permission foundation on the clean reconstruction parent.

Included:

- canonical foundation permissions `admin.access` and `operator.access`;
- Spatie `HasRoles` integration on `User`;
- middleware aliases for role and permission checks;
- additive, repeatable seeding;
- regression tests proving existing role and direct-user permissions are preserved.

Excluded:

- operational permission names still under consolidation;
- Kimia Write permissions;
- balance adjustment or financial mutation permissions;
- routes, dashboards, queues or frontend navigation;
- destructive permission synchronization.

## Safety decision

The historical AP-01 seeder used `syncPermissions`, which can remove permissions already assigned to a role. This reconstruction uses `givePermissionTo` and creates only verified foundation permissions.

The following conflicting names remain unresolved and are deliberately not introduced by this slice:

- `audit.view` / `audit-logs.view`
- `orders.queue.view` / `orders.view`
- `deliveries.complete` / `deliveries.deliver`
- `kimia.read` / `kimia.view`

## Test status

- Tests written: yes
- Tests executed locally: no
- CI: pending exact-head workflow run

Required result wording until CI completes:

- `WRITTEN — NOT EXECUTED`

## Files

- `backend/app/Support/BackofficePermissionCatalog.php`
- `backend/database/seeders/BackofficePermissionSeeder.php`
- `backend/app/Models/User.php`
- `backend/bootstrap/app.php`
- `backend/tests/Feature/BackofficePermissionFoundationTest.php`

## Next dependency

After this slice passes CI, the next safe slice is session-aware Admin/Operator bootstrap. Operational permission expansion must wait for the canonical permission-name evidence table.
