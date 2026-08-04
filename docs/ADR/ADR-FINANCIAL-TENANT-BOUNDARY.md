# ADR — Financial Tenant Boundary

## Status
Accepted by Alireza Bahramian on 2026-08-04.

## Decision
GoldPlatform financial data is scoped by the following hierarchy:

1. Tenant — the white-label brand boundary.
2. Company — the legal entity or operating business under a tenant.
3. Branch — the operational branch under a company.
4. Customer/System Account — the ledger account owner within that scope.

Every financial journal, ledger account, idempotency record, financial event and balance projection must be tenant-scoped. Company and branch scope are optional only when the business operation is genuinely tenant-wide.

## Invariants
- Cross-tenant reads or writes are forbidden.
- A company belongs to exactly one tenant.
- A branch belongs to exactly one company and therefore one tenant.
- Tenant scope must be part of repository lookup keys and concurrency lock keys.
- Kimia configuration and mapping are tenant-scoped.
- Custody remains a physical-asset domain and is not merged with financial balances.

## Deferred decisions
- Database identifier type for tenant/company/branch.
- Whether one database or separate databases are used per tenant.
- Default company and branch bootstrap rules.
- Cross-company settlement rules.

No database migration is created by this ADR because identifier strategy and tenancy storage model are still unresolved.
