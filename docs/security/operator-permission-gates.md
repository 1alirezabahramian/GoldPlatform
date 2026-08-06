# Operator Permission Gates

Status: Implemented — CI Pending

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

The migration creates missing permissions and grants all of them additively to the existing `operator` and `admin` roles. Existing access is therefore preserved at deployment time. Existing unrelated permissions are not removed or replaced.

## Safety boundaries

- No financial rule or balance behavior changed.
- No Kimia read/write behavior changed.
- No tenant, company, or branch scope was invented.
- No route, payload, or customer-facing contract changed.
- Permission checks remain server-side.

## Rollback

Rollback revokes these permissions only from the default `operator` and `admin` roles. Permission records are preserved to avoid destructive removal where custom assignments may already exist.
