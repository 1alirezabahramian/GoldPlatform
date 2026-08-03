# ADR-024 — Canonical Kimia Integration Path

- **Status:** Accepted
- **Date:** 2026-08-03
- **Scope:** Backend Kimia integration structure

## Context

The backend had two parallel Kimia access paths:

1. `App\Services\KimiaService` with repositories under `App\Repositories\Kimia`
2. `App\Integrations\Kimia` containing the client, DTOs, mappers, repositories, adapters, and services

The account synchronization command still used the first path while the second path was intended to become the structured integration boundary. Keeping both paths active risks inconsistent query serialization, error handling, mapping, logging, and tests.

## Decision

`App\Integrations\Kimia` is the canonical integration boundary for new and migrated Kimia behavior.

The account synchronization execution path now uses:

```text
SyncKimiaAccountsCommand
  → KimiaAccountRepository
  → KimiaClient
  → Kimia API
```

Account payloads are mapped to `AccountDTO`, but the original raw API row is retained for audit evidence and sync hashing.

The exact Kimia query names remain endpoint-specific:

- `GET /api/account` uses `Type`
- `GET /api/account/groups` uses `accountType`

No financial voucher write path is enabled by this decision.

## Migration Rules

- Existing legacy Kimia classes must not be deleted until all consumers are identified and migrated.
- New controllers, commands, and domain services must not call `App\Services\KimiaService` directly.
- Raw Kimia transport codes and payloads must remain inside the integration boundary.
- Every migrated path requires automated tests before legacy removal.
- Live Kimia write operations remain blocked until payload, idempotency, retry, failure, and audit behavior are verified.

## Consequences

### Positive

- One intended location for Kimia transport and mapping behavior
- DTO-based boundary without losing raw API evidence
- Easier prevention of raw Kimia concepts leaking into the domain and frontend
- Account sync tests now exercise the canonical repository

### Remaining Work

- Migrate remaining consumers of `App\Services\KimiaService` and `App\Repositories\Kimia`
- Consolidate group and voucher repositories under the canonical integration path
- Standardize retry, timeout, exception, logging, and `X-Book-Id` behavior
- Remove legacy classes only after full-suite tests pass on the shop Docker runtime
