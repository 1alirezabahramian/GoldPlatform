# ADR — Financial Infrastructure Adapters

## Status
Accepted for Stage 02 infrastructure integration.

## Context
The Financial Kernel domain contracts must be connected to Laravel infrastructure without coupling domain objects to Eloquent, database schemas, Redis, or framework facades.

The final financial persistence schema is still blocked by the unresolved white-label tenant boundary. Creating journal, event, idempotency, or projection tables before that decision would risk a destructive redesign.

## Decision
- `AtomicFinancialOperation` is implemented with Laravel `DB::transaction`.
- `ConcurrencyGuard` is implemented with Laravel atomic cache locks.
- Bindings are isolated in `FinancialServiceProvider`.
- No financial table or migration is introduced by this decision.
- Domain services continue depending only on contracts.

## Consequences
- All database-backed posting work can be rolled back as one unit.
- The same concurrency contract can use Redis in production and a compatible cache store in tests.
- Infrastructure adapters can be replaced without changing the Financial Kernel.
- A cache backend supporting atomic locks is mandatory in production.

## Explicitly unresolved
- Tenant/company/branch boundary
- Final financial tables and indexes
- Persistent lock naming scope across tenants
- Database implementations of JournalRepository, FinancialEventStore, IdempotencyRegistry, and BalanceProjectionRepository
- Business-specific debit/credit projection rules
- Kimia write timing
