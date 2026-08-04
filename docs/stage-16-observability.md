# Stage 16 — Monitoring and Observability Baseline

Implemented:

- request correlation ID added to structured log context;
- operational health snapshot for database, Redis, storage, failed jobs, outbox and Kimia safety state;
- CLI health command with JSON output and non-zero exit on degraded state;
- slow query detection with configurable threshold and no binding values in logs;
- production-like CI execution of the operational health command.

## Health semantics

- database or Redis connection failure: degraded;
- unwritable storage: degraded;
- any failed queue job: degraded;
- unsafe Kimia state (`read_only=false` or write enabled): degraded;
- pending outbox count: observable metric, not automatically treated as failure because an approved operational threshold has not yet been defined.

## Security

Health output does not include credentials, payloads, SQL bindings or upstream response bodies.

## Boundary

This stage provides application-side observability and machine-readable health. External metric collection, dashboards, paging and alert delivery require deployment-specific infrastructure and are not claimed as operational until connected in the target environment.
