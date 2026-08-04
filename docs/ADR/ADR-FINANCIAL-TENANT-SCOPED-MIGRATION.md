# ADR — Financial Tenant-scoped Migration

Status: Accepted
Date: 2026-08-04
Owner: Alireza Bahramian

## Context

GoldPlatform is a White-label financial platform. Every Journal, Financial Event, Idempotency Record and Balance Projection must be isolated by FinancialScope.

The first Financial Kernel contracts were intentionally created without tenancy to validate domain invariants. Tenant-aware contracts and adapters now exist and are the default bindings in FinancialServiceProvider.

## Decision

All new financial flows MUST use:

- TenantScopedJournalRepository
- TenantScopedFinancialEventStore
- TenantScopedIdempotencyRegistry
- TenantScopedBalanceProjectionRepository
- TenantScopedAtomicJournalPostingService

The following contracts/services are deprecated and MUST NOT be used by new code:

- JournalRepository
- FinancialEventStore
- IdempotencyRegistry
- BalanceProjectionRepository
- AtomicJournalPostingService

## Migration policy

1. Do not delete non-scoped code while legacy tests still depend on it.
2. Do not bind non-scoped repositories in the service container.
3. Migrate consumers one by one to require FinancialScope.
4. Add tenant-isolation tests before replacing each consumer.
5. Remove non-scoped contracts only after repository-wide search confirms zero production consumers.

## Current consumer inventory

Production path:

- TenantScopedAtomicJournalPostingService uses only tenant-scoped contracts.
- FinancialServiceProvider binds tenant-scoped repository contracts as defaults.

Legacy/test-only path:

- AtomicJournalPostingService uses the deprecated non-scoped contracts.
- AtomicJournalPostingServiceTest covers the legacy path until migration removal.
- In-memory non-scoped adapters remain for legacy tests only.

No controller, API route, command, wallet mutation or Kimia write path is currently allowed to start new work on the deprecated contracts.

## Consequences

Positive:

- Cross-tenant financial reads and writes require an explicit scope.
- Lock keys and idempotency are isolated by tenant/company/branch scope.
- The migration is reversible until persistence schema is finalized.

Trade-offs:

- Temporary duplication exists between scoped and non-scoped contracts.
- Legacy tests remain until the deletion gate is satisfied.

## Deletion gate

The deprecated path may be removed only when:

- all CI tests pass on the tenant-scoped path;
- no production consumer imports a deprecated contract;
- no service-container binding resolves a deprecated repository;
- persistence adapters and migrations are tenant-scoped;
- rollback and tenant-isolation integration tests pass.

## Not decided here

This ADR does not decide:

- bigint vs UUID vs ULID persistence identifiers;
- shared database vs database-per-tenant;
- accounting debit/credit direction;
- Kimia write mapping;
- financial decimal database scale.
