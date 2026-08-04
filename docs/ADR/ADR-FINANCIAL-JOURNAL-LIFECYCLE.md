# ADR — Financial Journal Lifecycle

## Status
Accepted for Stage 02 domain contract.

## Context
GoldPlatform requires traceable and auditable financial operations. A posted journal must not be edited in place, and correction must be represented by a separate reversal journal.

## Decision
The domain lifecycle is:

- `draft`
- `posted`
- `reversed`

Rules locked by this ADR:

1. Only a draft journal can be posted.
2. Only a posted journal can be reversed.
3. Lifecycle transitions return new immutable domain objects.
4. Posting does not modify journal lines.
5. Reversal creates a new balanced journal with opposite sides.
6. The reversal uses a new TraceId and IdempotencyKey.
7. The original and reversal journals preserve the same CorrelationId.
8. The original journal is marked reversed by reference to the reversal TraceId.

## Not Decided Here

- Database schema and migration
- Persistence repository
- posting timestamp and actor storage
- tenant boundary
- authorization policy
- Kimia posting timing
- wallet balance projection
- chart of accounts
- business-specific debit and credit direction

## Consequences

- Posted journals cannot be silently edited in the domain model.
- Corrections remain traceable as separate financial documents.
- Persistence must later enforce the same immutability and uniqueness guarantees.
