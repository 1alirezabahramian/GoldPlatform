# Operator Permission Gates

Status: **Merged — EXECUTED — PASS**

- PR: `#181`
- Validated Head SHA: `016d6f878e69e425badce36d92799949b87fd180`
- Canonical merge SHA: `fe0aad5c4920a650da2d9ba0755ab7883e5bf4a2`
- Operational Readiness #11: **EXECUTED — PASS**
- Backend RC1 Validation #302: **EXECUTED — PASS**

## Decision

Operator routes no longer rely on the `operator|admin` role alone. Each queue and delivery transition requires an explicit permission.

## Permissions

- `operator.access`
- `orders.queue.view`
- `deliveries.queue.view`
- `deliveries.approve`
- `deliveries.ready`
- `deliveries.complete`

## Compatibility

The migration creates missing permissions and grants all of them additively to the existing `operator` and `admin` roles. Existing access is preserved at deployment time, and unrelated permissions are not removed or replaced.

## Validation evidence

- Migration fresh: **PASS**
- Unit tests: **PASS**
- Feature tests: **PASS**
- Permission tests: **PASS**
- Full regression: **PASS**
- Exact PR Head SHA validation completed before merge.

## Safety boundaries

- No financial rule or balance behavior changed.
- No Kimia read/write behavior changed.
- No tenant, company or branch scope was invented.
- No route path, payload or customer-facing contract changed.
- Permission checks remain server-side.

## Rollback

Rollback revokes these permissions only from the default `operator` and `admin` roles. Permission records are preserved to avoid destructive removal where custom assignments may already exist.
