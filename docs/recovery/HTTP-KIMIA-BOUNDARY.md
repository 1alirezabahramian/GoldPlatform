# HTTP / Kimia Boundary

Status: Accepted recovery guard

## Boundary

Controllers and route files must not call Kimia infrastructure directly.

Forbidden in HTTP entry points:

- `KimiaClient`
- Kimia client namespace imports
- Kimia repository namespace imports
- direct Laravel HTTP client calls

HTTP entry points must delegate to application/domain services. Kimia Read and Kimia Write remain separate infrastructure paths.

## Reason

This keeps financial authority, retry, idempotency, audit, tenant context, and reconciliation inside the backend service boundary instead of controllers or routes.

## Safety

- No Kimia Write enabled.
- No financial rule introduced.
- No migration or data deletion.
- No controller behavior changed by this guard.

## Test

`Tests\\Unit\\Architecture\\HttpKimiaBoundaryTest`

The test scans the real controller and route trees during CI and fails if direct Kimia or HTTP infrastructure access is introduced.
