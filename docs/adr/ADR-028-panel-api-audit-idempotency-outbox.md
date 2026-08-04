# ADR-028 — Panel API boundaries, Audit, Idempotency and Outbox

- Status: Accepted
- Date: 2026-08-04

## Decision

1. Customer, Operator and Admin APIs are separated by route prefix and role.
2. Customer contracts use simple product language and never expose raw accounting concepts.
3. Every request receives a correlation UUID.
4. Mutating order, delivery and policy APIs require an idempotency key.
5. Operational changes append an audit record.
6. Integration events are persisted through a transactional outbox.
7. Kimia write operations remain disabled until real API evidence confirms payloads and actions.

## Consequences

- replayed requests cannot duplicate supported operations;
- operator actions are attributable and traceable;
- event delivery can later be retried without coupling domain commits to external systems;
- roles and permissions become part of the public API security contract.
