# Scheduled Kimia and Settlement Boundary

Status: Accepted recovery guard

## Finding

The canonical scheduler currently contains no automatic Kimia synchronization, Kimia write, voucher posting, settlement execution, or queued financial job registration.

Existing Kimia console commands are manual operational/read-oriented commands. They must not be added to the scheduler silently.

## Boundary

- No `kimia:*` command may be scheduled without an accepted operational decision and explicit deployment gate.
- No Settlement, Voucher, or Kimia Write job may be registered in the scheduler without verified ground truth, idempotency, audit, retry separation, and reconciliation controls.
- Automatic Outbox dispatch remains disabled by default and separately gated.
- Manual commands remain preserved for controlled inspection and approved synchronization.

## Test status

WRITTEN — CI PENDING on the pull request head SHA.
