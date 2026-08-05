# ADR-031 — Backoffice Permission Foundation

- Status: Draft
- Date: 2026-08-05

## Context

Historical AP and OP branches contain conflicting permission names and a seeder using destructive synchronization. Current `main` requires Spatie Permission but does not enable `HasRoles` on the User model.

## Decision

Introduce only the verified foundation permissions:

- `admin.access`
- `operator.access`

Seed them additively with `givePermissionTo`, never `syncPermissions`.

Operational permissions remain outside this ADR until their naming and route ownership are consolidated.

## Consequences

- Existing role permissions remain intact.
- Existing direct user permissions remain intact.
- Re-running the seeder is safe.
- Admin/Operator session bootstrap can depend on stable access permissions.
- Operational routes cannot yet rely on unaccepted permission names.

## Exclusions

- Kimia Write
- Balance mutation
- Financial rule changes
- Customer asset source-of-truth changes
