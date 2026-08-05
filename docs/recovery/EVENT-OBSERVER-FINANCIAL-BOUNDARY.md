# Event / Observer Financial Boundary

Status: Accepted

## Boundary

Events, listeners and model observers must not execute Kimia transport, settlement, ledger mutation, wallet mutation or sensitive outbox dispatch directly.

After-commit hooks and model event mappings must not be used to hide sensitive financial execution outside an explicit, reviewed application workflow.

Events may carry facts and listeners may perform non-financial side effects, but Kimia and financial mutations require an explicit service boundary, idempotency, audit and approved ground truth.

## Safety

- No Kimia Write was enabled.
- No financial rule, Action Code or payload was introduced.
- No migration or production behavior was changed.
- The boundary is enforced by `EventObserverFinancialBoundaryTest`.
