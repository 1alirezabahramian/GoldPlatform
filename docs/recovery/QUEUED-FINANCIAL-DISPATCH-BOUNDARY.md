# Queued Financial Dispatch Boundary

Status: Accepted recovery guard

## Finding

The canonical application currently has no approved queued Kimia, Settlement, Voucher, Wallet, Ledger, Balance, or Outbox worker class. Sensitive financial execution must not be introduced through `ShouldQueue`, route-level dispatch, queued Artisan commands, or hidden listeners without approved ground truth and deployment controls.

## Guard

- Sensitive queued classes are rejected by an architecture test.
- API, web, and console routes may not dispatch jobs directly.
- Kimia Write and Settlement execution remain disabled.
- A future queue integration requires explicit authority, idempotency, audit, retry policy, tenant isolation, and CI evidence.

## Scope

This change adds only an architecture test and documentation. It does not modify production behavior, migrations, financial rules, permissions, or tenant design.
