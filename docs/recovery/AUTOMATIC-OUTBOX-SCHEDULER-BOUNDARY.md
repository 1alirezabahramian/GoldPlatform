# Automatic Outbox Scheduler Boundary

Status: Accepted recovery guard

## Finding

`routes/console.php` scheduled `outbox:dispatch --fail-on-error` every minute. Although the approved handler map is currently empty and unknown events fail closed, the scheduler still consumes retry attempts and would automatically execute any future configured handler.

## Decision

Automatic outbox dispatch is disabled by default through `OUTBOX_DISPATCH_ENABLED=false`.

The scheduler is registered only when the flag is explicitly enabled in an approved deployment after handler destination, authority, idempotency, retry, audit, tenant safety and Kimia boundaries are verified.

## Preserved behavior

- The `outbox:dispatch` command remains available for controlled testing and future approved operations.
- Existing Outbox records and retry metadata are preserved.
- Admin Outbox access remains read-only.

## Safety

- No Kimia Write was enabled.
- No Settlement execution or replay was performed.
- No financial rule, migration or stored data was changed.
