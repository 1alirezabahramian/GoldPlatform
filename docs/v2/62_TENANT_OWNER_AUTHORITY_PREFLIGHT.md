# Tenant Owner Authority Preflight

Status: IMPLEMENTED — NOT TESTED (pending exact-head CI)

Scope: bounded continuation of PR #201. This is not a new numbered V2 stage.

## Ground truth

- Platform Super Admin remains separate from Tenant Admin/Operator (ADR-026).
- A normal tenant `admin` role does not imply Tenant Owner authority.
- Operator never implies Tenant Owner authority.
- Existing Spatie role/permission foundation remains canonical; no parallel permission system is introduced.
- Tenant authority must be host-resolved and tenant-scoped. Client-selectable tenant identity is not authority.

## Schema preflight result

Before this change, `tenants` had no explicit owner representation and `users` only carried `tenant_id`, roles and permissions. The pilot migration creates `khalifeh-coin` and backfills unbound users into that tenant, but contains no evidence identifying which existing user is the initial owner.

## Minimal representation

`tenants.owner_user_id -> users.id` is added as a nullable foreign key.

The pointer is intentionally nullable so existing tenants are not assigned an owner by guesswork. Runtime authority is fail-closed unless both conditions are true:

1. `tenant.owner_user_id == user.id`
2. `user.tenant_id == tenant.id`

A cross-tenant pointer therefore never grants authority at the application authority boundary.

## Backfill / bootstrap

No existing user is automatically promoted to owner.

Pilot owner backfill: **BLOCKED BY GROUND TRUTH** until an authoritative provisioning decision identifies the initial owner safely.

Initial owner bootstrap is a separate capability from day-to-day Staff Management. No static/shared credential, secret in Git, or `admin/admin` bootstrap is permitted.

## Staff Management gate

Staff provisioning may be implemented only after this authority foundation is green on exact-head CI. Tenant must come from verified Host -> TenantContext, not from a client-selectable tenant id.

Expected flow after the gate:

Tenant Owner -> create Admin/Operator -> same tenant -> existing roles/permissions -> random temporary credential -> `must_change_password=true` -> mandatory password rotation.

## Explicit non-changes

- no Platform Super Admin role change
- no new permission system
- no owner backfill
- no Jibit integration
- no Kimia Write
- no financial rule
- no production-domain routing change
