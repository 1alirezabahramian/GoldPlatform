# GoldPlatform V2 — Runtime Reconciliation Evidence & Handoff Checkpoint

Status: V2-00 evidence checkpoint; documentation only
Date: 2026-08-07

## Purpose

Record the strongest currently verified evidence for the historical runtime relationship between `accounts`, `users.account_id`, and `external_accounts`, and define the next safe continuation point.

This document does not perform a migration, link a user, backfill data, alter Kimia configuration, or enable Kimia Write.

## Verified current V2 implementation state

The V2 branch now contains a tested read-only reconciliation capability:

- `App\Services\Kimia\CustomerAccountReconciliationService`
- `kimia:inspect-account-reconciliation`
- `KimiaInspectAccountReconciliationTest`

The capability performs only:

`SELECT -> classify -> report`

It does not create, update, delete, link, backfill, or write to Kimia.

The correction commit `f9cb043cbbc013354f09ac40199bcfb83e4eb6b0` passed:

- Operational Readiness #25 — EXECUTED — PASS
- Backend RC1 Validation #411 — EXECUTED — PASS

The follow-up evidence-workflow documentation head `1ba5a6b646b9159c11762dbf27f3a06945d224aa` passed:

- Operational Readiness #26 — EXECUTED — PASS
- Backend RC1 Validation #412 — EXECUTED — PASS

## Historical real-runtime evidence recovered from File Library

Historical owner-run shop Docker evidence proves all of the following occurred in the real GoldPlatform database/runtime:

1. `accounts` table migration ran.
2. `users.account_id` migration ran.
3. `external_accounts` creation and enhancement migrations ran.
4. The GoldPlatform runtime connected successfully to Kimia in read-only mode.
5. `external_accounts` contained real Kimia retail-account projections, including AccountId 350.
6. A historical stabilization checkpoint separately recorded `Account::count() = 0`.
7. Project Memory explicitly recorded that the active Kimia account sync writes `external_accounts`, while `users.account_id` targets `accounts`.

Therefore the `accounts` versus `external_accounts` destination drift is not merely a schema/code theory. It existed in real runtime evidence.

## Evidence boundary

Historical runtime evidence is not accepted as proof of the current shop database state.

The historical Project Memory explicitly required a fresh post-stabilization read-only sync/run. Therefore:

- Historical runtime existence = VERIFIED
- Current shop reconciliation result = NOT YET VERIFIED
- Customer financial binding = NOT YET VERIFIED
- Automatic linking = NOT APPROVED
- Kimia Write = BLOCKED / DISABLED

## Current architecture interpretation

The safe recovery candidate remains:

`Resolved Tenant -> active Kimia Connector/Book -> external account projection -> explicit verified reconciliation -> Account -> User`

`Account` remains the current stable binding candidate under ADR-024, subject to tenant/connector scoping from ADR-026.

No link may be inferred from mobile, name, national code, shop name, or account code.

## Next safe runtime step

The next real-runtime evidence action, when access to the current shop runtime is appropriate, is to run the already-tested read-only reconciliation command against the current database state and capture only sanitized summary/classification output.

That future runtime step must not mutate data and must not expose customer PII or credentials.

Until then, customer financial reads remain fail-closed.

## Handoff checkpoint

This is a safe conversation-transfer checkpoint because:

- the historical tenant/connector gap has been audited;
- the customer-Kimia binding recovery candidate is documented;
- migration preflight rules are documented;
- read-only reconciliation semantics are documented;
- the diagnostic command is implemented and green on exact-SHA CI;
- historical real-runtime drift evidence has been recovered;
- the next unresolved item is clearly bounded to fresh current-runtime read-only evidence.

Stage remains:

`V2-00 — GATE NOT PASSED`

`V2-01 — NOT STARTED`
