# Stage 24 — Observability & Alerting

Date: 2026-08-04
Branch: `work/stage-24-observability-alerting`
Status: In progress

## Goal

Create a safe, framework-native observability foundation without introducing a duplicate monitoring service or changing financial/Kimia behavior.

## Implemented scope

- Reused the existing request correlation middleware and `request_id` context.
- Added configurable HTTP request observability.
- Added `Server-Timing` response metadata for application latency.
- Added structured logs for:
  - successful requests when explicitly enabled;
  - slow requests;
  - HTTP 5xx responses;
  - uncaught request exceptions before rethrowing.
- Added feature tests for timing metadata, opt-in successful request logging and full disablement.

## Configuration

- `OBSERVABILITY_REQUEST_LOGGING_ENABLED=true`
- `OBSERVABILITY_SLOW_REQUEST_THRESHOLD_MS=1000`
- `OBSERVABILITY_LOG_SUCCESSFUL_REQUESTS=false`

Successful-request logging is disabled by default to control log volume. Slow and failed requests remain observable while request logging is enabled.

## Security and domain boundaries

- No request payload, headers, credentials, tokens or personal data are written to the new structured context.
- No financial rule, Ledger behavior, order lifecycle or Kimia contract changed.
- No external monitoring vendor is selected in this stage.

## CI incident and infrastructure correction

The first Stage 24 production-operations run failed before application startup while Docker created the shared `app-storage` volume for PHP, queue worker and scheduler concurrently. The daemon reported that `storage/framework/cache/data` already existed during copy-up.

This was an infrastructure race, not an Observability, financial or Kimia failure. The production Compose contract now:

- disables automatic image copy-up for the shared storage volume with `nocopy`;
- runs a one-shot `storage-init` service as root;
- creates the required Laravel storage directories deterministically;
- assigns the shared storage tree to `www-data`;
- starts PHP, queue worker and scheduler only after initialization succeeds.

## Validation gate

The branch must pass the existing Backend RC1, RC2, security, performance, compose, backup/restore and production-operations workflows on the final SHA before Stage 24 can be declared complete.

## Remaining Stage 24 work

- CI confirmation of the middleware tests, storage initialization correction and full regression suite.
- Define alert routing and ownership without storing secrets in Git.
- Add queue failure and scheduler heartbeat observability using existing Laravel infrastructure.
- Document production log ingestion and retention requirements.
