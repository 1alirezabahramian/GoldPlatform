# ADR-024 — Canonical Kimia Integration Path

- **Status:** Accepted
- **Date:** 2026-08-03
- **Scope:** Backend Kimia integration structure

## Context

The backend had two parallel Kimia access paths:

1. `App\Services\KimiaService` with repositories under `App\Repositories\Kimia`
2. `App\Integrations\Kimia` containing the client, DTOs, mappers, repositories, adapters, and services

Keeping both paths active risked inconsistent query serialization, error handling, mapping, logging, and tests.

## Decision

`App\Integrations\Kimia` is the canonical integration boundary.

The migrated execution paths are:

```text
SyncKimiaAccountsCommand
  → KimiaAccountRepository
  → KimiaClient
  → Kimia API
```

```text
KimiaSyncGroups / KimiaController
  → KimiaAccountRepository
  → KimiaClient
  → Kimia API
```

```text
KimiaInspectTransactions
  → VoucherRepository
  → KimiaClient
  → Kimia API
```

Account payloads are mapped to `AccountDTO`, while the original raw API row is retained for audit evidence and sync hashing.

The exact Kimia query names remain endpoint-specific:

- `GET /api/account` uses `Type`
- `GET /api/account/groups` uses `accountType`

## Client Safety Contract

The canonical `KimiaClient` now provides:

- one consistent error contract for `GET`, `POST`, `PUT`, and `DELETE`
- rejection of incomplete Kimia configuration
- logs limited to HTTP method, relative URI, and status
- no credentials, request payloads, national identifiers, or upstream response bodies in exception messages or logs
- no automatic retry on write requests

Automatic write retry is intentionally disabled because a timed-out financial request may have reached Kimia even when GoldPlatform did not receive the response. Retry policy for financial writes requires verified idempotency and `RequestId` behavior before implementation.

## Migration Rules

- New controllers, commands, and domain services must not call legacy Kimia paths.
- Raw Kimia transport codes and payloads must remain inside the integration boundary.
- Every migrated path requires automated tests before legacy removal.
- Live Kimia write operations remain blocked until payload, idempotency, retry, failure, posting-time, and audit behavior are verified.
- Architecture tests must reject reintroduction of legacy Kimia imports.

## Completed Migration

- Account synchronization migrated to `KimiaAccountRepository`.
- Account-group synchronization and API reads migrated to `KimiaAccountRepository`.
- Read-only voucher transaction inspection migrated to canonical `VoucherRepository`.
- `App\Services\KimiaService` removed.
- Legacy account and voucher repositories removed.
- Architecture tests prevent legacy imports from returning.
- Full Laravel suite and live read-only Kimia checks passed after migration.

## Consequences

### Positive

- One Kimia transport and mapping boundary
- DTO-based boundary without losing raw API evidence
- Consistent safe errors and logs
- Reduced risk of duplicate behavior and silent divergence
- Raw Kimia concepts remain out of the domain and frontend

### Remaining Work

- Verify and standardize optional `X-Book-Id` behavior from official evidence.
- Define correlation and audit identifiers for future financial writes.
- Verify complete voucher-write payload and `RequestId` idempotency semantics.
- Keep live voucher writes disabled until those stop conditions are resolved.
