# Phase 0 — Customer Closure Canonical Review

**Date:** 2026-08-05  
**Recovery baseline:** Stage 22 / RC2 (`cada4441184e59d09f5ddac567d7b9b8d19b34ae`)  
**Customer closure merge:** `5da4da919b0fbd277e3cb1f3cf92c27b93b3868c`

## Executive decision

Customer Closure is a valid historical closure and must be preserved. It is not itself a clean recovery slice because it is 118 commits ahead of RC2 and contains CP-06 through CP-18 as one accumulated product line.

Canonical classification:

- Customer Closure documentation: **KEEP — HISTORICAL / ACCEPTED**
- Customer runtime code after RC2: **KEEP WITH CONTRACT REVIEW**
- Direct merge of the entire closure line into the RC2 recovery branch: **NOT APPROVED**
- CP-06 custody/delivery and CP-07 profile: **HIGH-VALUE REBUILD CANDIDATES**
- CP-08 activity timeline: **DONOR ONLY — SEMANTIC CONTRACT DRIFT**
- CP-09 through CP-18 contract hardening: **REBUILD SELECTIVELY AFTER RUNTIME CONTRACT VALIDATION**

## Verified GitHub facts

PR #132 is merged and records the formal Customer Panel closure after final regression. The closure PR itself adds documentation only and no runtime code, migration, financial rule, or Kimia read/write.

The closure merge is 118 commits ahead of RC2. The accumulated diff includes:

- Customer custody and delivery controller;
- Customer profile controller;
- Customer activity controller/read model;
- customer bootstrap and pagination/filter/sort contracts;
- no-store and trace/request-id middleware behavior;
- OpenAPI expansion;
- architecture and regression tests;
- phase closure documentation.

## Recovery decisions

### CP-06 — Custody and delivery

Decision: **REBUILD ON RC2**

Reasons:

- clear customer value;
- ownership boundary is local to GoldPlatform Custody;
- no need for Kimia write;
- can be isolated without importing later Customer stages.

Required tests:

- ownership and IDOR;
- idempotent delivery request;
- delivery state transitions;
- response redaction;
- HTTP contract and OpenAPI regression.

### CP-07 — Customer profile

Decision: **REBUILD ON RC2**

Reasons:

- small isolated read slice;
- no migration or financial rule;
- safe field allow-list can be tested directly.

Required tests:

- authenticated self-only access;
- sensitive-field exclusion;
- stable response envelope;
- HTTP contract regression.

### CP-08 — Activity timeline

Decision: **DONOR ONLY / REBUILD CONTRACT**

The implementation derives activity from current records and `updated_at`; it does not represent a true transition/event history. It must either be renamed to a current activity feed or rebuilt on accepted audit/event history.

### CP-09 to CP-18

Decision: **SELECTIVE REBUILD**

Valuable elements include bootstrap, error envelope, pagination, filters, sort, date range, no-store, request/trace headers, OpenAPI and readiness gates. These are not accepted as one bulk import. Each must be rebuilt only after CP-06 and CP-07 runtime contracts are stable on RC2.

## Explicit exclusions

The recovery line must not:

- merge the full 118-commit Customer closure chain;
- import CP-08 under a false timeline contract;
- expose Kimia identifiers or internal model fields;
- use internal Wallet/Ledger balances as final customer balances;
- mix Customer recovery with FE, OP, Admin or Business changes.

## Integration order

1. Keep RC2 as baseline.
2. Keep PR #132 closure documents as accepted historical evidence.
3. Rebuild CP-06 as an isolated slice.
4. Run backend regression and HTTP/OpenAPI contract tests.
5. Rebuild CP-07 as a second isolated slice.
6. Rebuild only missing CP-09 to CP-18 contract hardening.
7. Revisit CP-08 after choosing a true event-history source or renaming the feature.

## Test status

- Customer Closure historical final regression: **EXECUTED — PASS** on the historical closure line.
- Customer recovery validation on RC2-derived canonical SHA: **NOT EXECUTED**.
- CP-06 clean recovery slice: **NOT YET REBUILT IN THIS REVIEW**.
- CP-07 clean recovery slice: **NOT YET REBUILT IN THIS REVIEW**.

## Final status

Customer recovery status: **IN PROGRESS**

Safe conclusion:

- Preserve the closure.
- Do not merge the closure line.
- Rebuild CP-06 and CP-07 first.
- Treat CP-08 as donor-only until its meaning is corrected.
