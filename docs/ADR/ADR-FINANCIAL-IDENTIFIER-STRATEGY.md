# ADR — Financial Identifier Strategy

Status: Accepted
Date: 2026-08-04

## Context

GoldPlatform already uses numeric internal primary keys across existing Laravel tables, while the Financial Kernel uses UUID-based TraceId and CorrelationId value objects. The project memory does not authorize ULID or a replacement of all existing database keys.

## Decision

1. Existing relational entities keep the repository's current internal database key convention.
2. Financial documents and externally traceable operations receive a separate immutable UUID identifier.
3. TraceId and CorrelationId remain UUID-based.
4. IdempotencyKey remains caller-provided and is never derived from a database primary key.
5. Kimia AccountId, GroupId, ProductId, CoinId and CurrencyId remain external identifiers and must not be converted into GoldPlatform primary keys.
6. ULID is not introduced because no approved project source requires it.
7. FinancialScope identifiers remain opaque strings until the persisted Tenant, Company and Branch models are approved.

## Required schema pattern

Financial tables must use both:

- an internal relational primary key following the current repository convention;
- a unique UUID business identifier for traceability, APIs, audit and idempotent references.

Internal numeric keys must never be exposed as the only public financial identifier.

## Consequences

- Existing relations and Laravel conventions remain compatible.
- Public identifiers are not sequential or guessable.
- Migration to another internal key type remains possible without changing API identifiers.
- External Kimia IDs remain dynamic mappings rather than architecture constants.

## Explicitly not decided here

- Physical storage model for Tenant, Company and Branch.
- Kimia identifier mappings.
- Decimal scale per asset.
- Business-specific debit and credit direction.
