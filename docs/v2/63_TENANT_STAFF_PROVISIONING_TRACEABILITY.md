# Tenant Staff Provisioning — Traceability

## Scope

This document records the bounded Staff Management continuation on PR #201. It is not a new numbered V2 stage.

## Requirement

An explicit Tenant Owner may create `admin` or `operator` Staff for the same resolved Tenant.

The Tenant is derived only from verified Host resolution. Client-provided `tenant_id` is prohibited.

## Ground Truth / authority

- Platform Super Admin remains separate from Tenant Owner/Admin/Operator.
- Tenant Owner authority is represented by nullable `tenants.owner_user_id` and is valid only when the referenced User also belongs to that same Tenant.
- A normal `admin` role does not imply Tenant Owner authority.
- Existing Spatie roles and permissions remain canonical; no parallel permission system is introduced.
- Pilot Tenant owner backfill remains `BLOCKED BY GROUND TRUTH`; no existing User is auto-promoted.

## Runtime flow

`Verified Host -> TenantResolver -> authenticated Staff/Tenant match -> admin route guard -> TenantOwnerAuthority -> TenantStaffProvisioningService`

Provisioning then:

1. accepts `name`, `mobile`, `username`, and role (`admin` or `operator`);
2. rejects any client-supplied `tenant_id`;
3. requires the existing requested role to exist under the `web` guard;
4. generates a random 48-character temporary credential;
5. creates the Staff User directly in the resolved Tenant;
6. avoids Customer-only `UserObserver` wallet/default-account provisioning for Staff identities;
7. assigns the existing Spatie role;
8. sets `must_change_password = true` and leaves `password_changed_at = null`;
9. records `tenant.staff.provisioned` in `audit_logs` without recording the temporary password;
10. returns the temporary password only in the initial `201` response with `Cache-Control: no-store, private`.

## Idempotency / secret handling

The existing generic idempotency registry stores response bodies for replay. That behavior is unsafe for an endpoint returning a temporary credential.

For scope `staff.create`:

- request idempotency remains enforced;
- the sensitive response body is not persisted;
- a repeated completed request does not create a second Staff record;
- the temporary credential is not replayed;
- retry returns `409 IDEMPOTENT_SECRET_RESPONSE_NOT_REPLAYABLE`.

## Customer/Staff separation

Canonical `UserObserver` currently creates Customer wallet/default financial accounts for every ordinary User creation event. Staff provisioning intentionally creates the Staff identity without firing that Customer-only creation side effect.

No Staff wallet, RIAL default account, or GOLD18 default account is created by this flow.

This does not modify Customer registration behavior.

## API / OpenAPI

Runtime route:

`POST /api/admin/staff`

Required guards:

- `auth:sanctum`
- `throttle:admin`
- `tenant.resolve`
- `tenant.user-match`
- `role:admin`
- `idempotency:staff.create`
- explicit `TenantOwnerAuthority` inside the controller

Canonical contract:

`docs/api/backoffice-v1.openapi.yaml`

## Tests

`backend/tests/Feature/TenantStaffProvisioningTest.php` covers:

- explicit Owner creates Operator in the resolved Tenant;
- normal Admin is not Owner authority;
- client Tenant selection is rejected;
- Staff does not receive Customer wallet/default accounts;
- temporary password is hashed in the User record;
- audit entry is created without the credential;
- idempotency registry does not store or replay the temporary credential;
- repeated idempotent call does not create a duplicate Staff record.

`BackofficeIdentityOpenApiContractTest` guards the new runtime/OpenAPI contract.

## Status

Implementation status at document creation: `IMPLEMENTED — NOT TESTED` on the newest PR #201 head until exact-head GitHub Actions finish.

Previous Tenant Owner authority checkpoint `fc280df1d320b45154481c70ada4022f07e0aab5` passed Operational Readiness #159 and Backend RC1 Validation #543.

No merge is authorized by this document.
