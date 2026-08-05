# GoldPlatform Project State

## Current Reconstruction Slice

- Slice: Backoffice Permission Foundation V1
- Branch: `reconstruct/permission-foundation-v1`
- Base: `main`
- Status: Implemented — Not Tested

## Scope

- Minimal canonical permissions: `admin.access`, `operator.access`
- Non-destructive idempotent seeding
- `HasRoles` integration on `User`
- Middleware aliases for role and permission checks
- Tests preserving existing role and direct-user permissions

## Explicitly Not Included

- Operational permission-name consolidation
- Kimia Write permissions
- Balance mutation permissions
- Admin/Operator routes, dashboard or frontend

## Test Truth

- Test written: yes
- Test executed: no
- CI exact-head result: pending

## Architecture Ground Truth

- Kimia is the source of truth for Money, Gold, Coin and Currency.
- GoldPlatform is the source of truth for physical Custody/Amanat.
- Internal ledger, journal, event, idempotency and projection components are operational infrastructure, not final customer financial balances.
