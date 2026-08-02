# ADR-025 — Kimia Live Write Safety Gate

Status: Accepted (implementation prepared; shop-runtime verification pending)

Date: 2026-08-03

## Context

Project rules explicitly disable live Kimia writes until every write payload, posting time,
idempotency key, retry rule, audit event, failure path, and reconciliation behavior is
verified and approved. The repository nevertheless exposed `POST`, `PUT`, and `DELETE`
methods through both the active `KimiaService` and a preserved legacy `KimiaClient`.
`AccountRepository` could also obtain the active pending HTTP client directly.

Documentation alone was therefore not a sufficient safety boundary.

## Decision

- Kimia writes fail closed by default.
- `KIMIA_WRITES_ENABLED=false` is the documented default and is represented by
  `services.kimia.writes_enabled`.
- Only an explicitly recognized boolean `true` value can open the gate. Missing,
  malformed, or false values keep it closed.
- The gate protects:
  - direct `KimiaService::post/put/delete` calls;
  - non-read requests made through `KimiaService::client()`;
  - the preserved `App\Integrations\Kimia\Client\KimiaClient` write methods.
- `kimia:safety-status` fails when writes are enabled so operational verification stops
  before any live Kimia call.
- `GET` and `HEAD` remain available for approved read-only inspection.

Enabling the environment flag in the future is not, by itself, business authorization to
post a document. It may only happen in a separately approved and tested write milestone.

## Consequences

- Accidental write calls fail before an HTTP request is sent.
- Existing read and local projection synchronization paths remain available.
- Synchronization commands may update GoldPlatform's local database, but they do not
  modify Kimia.
- Automated tests cover the active service, its directly exposed pending client, and the
  preserved legacy client.
- No database migration is required for this decision.

## Exit Conditions for a Future Write Milestone

The gate must remain closed until all of the following are separately accepted:

1. Exact Swagger and runtime-confirmed request/response payload.
2. Correct account, product, currency, Action, sign, unit, and posting-time mapping.
3. Stable `RequestId`/idempotency contract.
4. Timeout and retry rules that cannot duplicate a financial document.
5. Audit log, reconciliation, and operator recovery procedure.
6. Sandbox or explicitly approved low-risk live test plan.
7. Automated and owner-run verification evidence.

## Scope Boundary

This ADR does not approve any Kimia account creation, account update, voucher posting,
record deletion, or financial mutation. It only enforces the already accepted read-only
stabilization boundary in executable code.
