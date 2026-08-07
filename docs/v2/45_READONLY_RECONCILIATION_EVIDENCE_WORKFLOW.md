# V2-00 — Read-only Customer/Kimia Reconciliation Evidence Workflow

Status: EVIDENCE WORKFLOW DEFINED — NO RUNTIME DATA MUTATION

## Purpose

Define how the new `kimia:inspect-account-reconciliation` diagnostic command can be used as evidence without confusing CI fixture data with real runtime/shop data.

## Safety boundary

The command and its service are read-only. The evidence workflow MUST remain:

`SELECT -> classify -> sanitize -> record evidence`

It MUST NOT perform create/update/delete/link/backfill, replace unique constraints, activate tenant routing, or execute any Kimia Write.

## Two different evidence classes

### A. CI / repository evidence

GitHub Backend RC1 Validation uses `migrate:fresh` and automated test fixtures. Its role is only to prove that:

- the command resolves and executes;
- classifications are deterministic for controlled fixtures;
- conflict cases remain fail-safe/report-only;
- database snapshots remain unchanged by inspection;
- the broader backend regression suite stays green on the exact SHA.

A green CI run MUST NOT be interpreted as evidence of the real shop database reconciliation state.

### B. Real runtime/shop evidence

Real reconciliation evidence requires execution against the actual GoldPlatform runtime database that contains the real `accounts`, `external_accounts`, and `users.account_id` state.

The accepted real-runtime evidence is the sanitized output of:

`php artisan kimia:inspect-account-reconciliation --json`

executed in the existing application runtime, with no additional sync/link/backfill operation bundled into the same step.

The output must be treated as operational evidence, not as authorization to repair anything automatically.

## Required evidence fields

Record at minimum:

- execution timestamp;
- application/repository SHA if available in runtime deployment evidence;
- database/environment identity without secrets;
- total local accounts;
- total Kimia external-account projections;
- `matched_linked` count;
- `matched_unlinked` count;
- `account_only_linked` count;
- `account_only_unlinked` count;
- `external_only` count;
- `duplicate_user_binding` count;
- `orphaned_user_bindings` count.

Individual row evidence may include only the technical identifiers emitted by the command. Do not add name, mobile, national code, credentials, raw Kimia payloads, or other PII to the V2 repository.

## Interpretation rules

- `matched_linked` is a structural match only. It is NOT final tenant/connector ownership proof until the tenant-scoped connector runtime exists and is validated.
- `matched_unlinked` is NOT an auto-link instruction.
- `external_only` and `account_only_*` are investigation categories, not repair instructions.
- `duplicate_user_binding > 0` is a hard conflict for one-to-one binding enforcement.
- `orphaned_user_bindings > 0` is a hard referential/data-integrity conflict.
- mobile, national code, name, shop name, and `account_code` are never matching keys for automatic reconciliation.

## Stop conditions

Stop before any mutation if real evidence shows:

1. duplicate user bindings;
2. orphaned user bindings;
3. conflicting AccountId ownership;
4. unexpected provider/identifier shape;
5. runtime schema drift from the verified canonical migrations;
6. uncertainty about which tenant/connector owns the Kimia account space.

## Traceability

Requirement: authenticate a customer to one verified Kimia AccountId without inventing balances.

Evidence chain:

ADR-024 binding contract
-> ADR-026 tenant/connector scope
-> current `accounts` / `external_accounts` / `users.account_id` schema
-> read-only reconciliation specification
-> `CustomerAccountReconciliationService`
-> `kimia:inspect-account-reconciliation`
-> automated no-mutation tests
-> exact-SHA Backend CI
-> sanitized real-runtime evidence (still pending)
-> later explicit owner-reviewed migration/linking work.

## Current classification

- Reconciliation command implementation: `IMPLEMENTED — TEST RESULT PENDING ON CURRENT EXACT SHA` until Backend RC1 Validation completes successfully.
- CI fixture evidence: validates implementation only.
- Real shop reconciliation evidence: `NOT YET CAPTURED IN V2`.
- Automatic repair/linking: `NOT IMPLEMENTED`.
- Tenant-aware final customer balance resolver: `NOT IMPLEMENTED`.

No V2 gate is passed by this document alone.
