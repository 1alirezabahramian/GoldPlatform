# Phase 0 — Canonical Capability Matrix

**Date:** 2026-08-05  
**Recovery baseline:** Stage 22 / RC2 (`cada4441184e59d09f5ddac567d7b9b8d19b34ae`)  
**Status:** Canonical decisions recorded — integration not started

## Decision legend

- **KEEP** — accepted baseline capability
- **KEEP WITH FIX** — valuable and retained only after a bounded correction
- **VERIFIED DONOR** — tested source material; rebuild as a small clean slice
- **DONOR ONLY** — useful ideas/code, but no direct merge
- **REBUILD** — recreate on the RC2-derived canonical branch
- **DUPLICATE CONFLICT** — competing implementation exists; select one contract first
- **BLOCKED BY GROUND TRUTH** — no implementation until confirmed evidence exists

## Cross-track matrix

| Domain | Capability | Current source | Evidence | Conflict / risk | Canonical decision | Integration priority |
|---|---|---|---|---|---|---:|
| Core | RC2 backend and production baseline | Stage 22 / RC2 | Six release gates passed on final candidate | Target production environment not deployed | KEEP | 1 |
| Customer | Versioned Customer API `/api/v1/customer/*` | CP-01 to CP-18 / Final Regression | PR #126 merged; six gates reported passed | Some semantic contracts require runtime review | KEEP WITH FIX | 4 |
| Customer | Customer success/error envelope | CP chain | Final Regression and OpenAPI guards | Must remain single canonical envelope | KEEP | 4 |
| Customer | Dashboard and read resources | CP chain | Merged closure evidence | Internal projections must not be treated as final Kimia balance | KEEP WITH FIX | 5 |
| Customer | Money/Gold/Coin/Currency assets | CP-04 | CI evidence in Customer chain | Any use of Ledger projection as final balance is Architecture Drift | REBUILD DATA SOURCE AGAINST KIMIA | 3 |
| Customer | Custody and Delivery reads/actions | CP-06 | Exact head passed six gates in prior audit | Must preserve ownership and existing DeliveryService rules | VERIFIED DONOR | 6 |
| Customer | Profile read | CP-07 | Exact head passed six gates in prior audit | Authentication blockers remain outside scope | VERIFIED DONOR | 6 |
| Customer | Activity timeline | CP-08 | CI green | Implementation is current-record feed, not true transition history | REBUILD CONTRACT | 8 |
| Customer | Pagination/status/sort/date/no-store/request-id | CP-12 to CP-18 | Merged Final Regression | Duplicate stage numbering and stacked history | KEEP WITH CONTRACT REGRESSION | 5 |
| Customer | Customer frontend | FE chain | No complete build/E2E evidence recorded | API/auth/session contract dependency | DONOR ONLY / REBUILD | 15 |
| Business | Baseline correction and CI | Stage 00 | Business Engine Baseline PASS | None material | KEEP | 2 |
| Kimia | Read-only client/repositories | Stage 01 | Business Engine Baseline PASS | Must use accepted `Type`; no silent empty-success; read/write separation | KEEP WITH FIX | 2 |
| Kimia | Money/Gold/Coin/Currency source of truth | Accepted architecture contract | Confirmed project ground truth | Any competing internal balance | KEEP — NON-NEGOTIABLE | 1 |
| Kimia | Live Write | None accepted | No final payload/action mapping evidence | Financial and operational risk | BLOCKED BY GROUND TRUTH | 18 |
| Financial | Exact Decimal and units | Stage 02 | Business Engine Baseline PASS | Rial/Toman conversion must be centralized | KEEP WITH FIX | 2 |
| Financial | Trace, correlation, idempotency | Stage 02 | Business Engine Baseline PASS | Must remain tenant/company safe | KEEP | 3 |
| Financial | Journal and Event Store | Stage 02 | Business Engine Baseline PASS | Must not become final balance source | KEEP AS NON-AUTHORITATIVE | 3 |
| Financial | Balance Projection | Stage 02 | Tests exist and baseline passed | Architecture Drift if exposed as final customer balance | KEEP ONLY AS REBUILDABLE PROJECTION | 3 |
| Custody | Physical Amanat source of truth | RC2 / existing custody domain | RC2 gates passed | Must remain separate from financial balance | KEEP — NON-NEGOTIABLE | 1 |
| Trading | Existing RC2 Order/Trade/Settlement | RC2 | RC2 full gates | Needs reconciliation with future Quote contract | KEEP UNTIL ACCEPTED REPLACEMENT | 3 |
| Trading | Stage 03 Quote model | PR #109 | Business Engine Baseline PASS only | New parallel persistence | DONOR ONLY | 9 |
| Trading | Stage 03 validation pipeline | PR #109 | Unit/feature code and baseline PASS | Must bind to one accepted Order lifecycle | VERIFIED DONOR | 9 |
| Trading | Stage 03 submit/decision/idempotency services | PR #109 | Tests present; baseline PASS | Duplicate Order state machine and repositories | DONOR ONLY / REBUILD | 10 |
| Trading | `trading_quotes` / `trading_orders` migration | PR #109 | Not validated on canonical RC2 integration SHA | Duplicate persistence and unaccepted tenant storage model | REJECT DIRECT MERGE | 10 |
| Settlement | Settlement completion | RC2 plus historical donors | RC2 baseline exists | Completion must require confirmed Kimia result and re-read | REBUILD COMPLETION CONTRACT | 12 |
| Admin/Operator | Backoffice API foundation/bootstrap | OP-01 | Merged PR #139 | Must compare with AP route/envelope foundation | KEEP WITH CONTRACT REVIEW | 7 |
| Admin/Operator | Permission catalog | AP and OP chains | Mostly written, not executed | Conflicting names and destructive `syncPermissions` risk | REBUILD ONE CANONICAL CATALOG | 7 |
| Admin/Operator | Dashboard and queues | AP/OP donors | Tests mostly not executed | Duplicate routes/read models | DONOR ONLY / REBUILD | 11 |
| Admin/Operator | Users/groups/roles/orders/custody/delivery/settlement reads | AP chain | Written, mostly not executed | Contract and permission duplication | DONOR ONLY | 11 |
| Admin/Operator | Safe delivery actions | AP chain | Written, not fully executed | Must reuse DeliveryService and permission boundary | DONOR ONLY / REBUILD | 13 |
| Admin/Operator | Settlement capability contract | AP chain | Written, not fully executed | No ungrounded settlement execution | DONOR ONLY | 13 |
| Admin/Operator | AP-06 migration | AP chain | Not executed on canonical base | Migration/permission/tenant risk | REBUILD / VALIDATE | 14 |
| Admin/Operator | Admin/Operator frontend | AP-20 and OP-03..05 | Install/build/typecheck not executed | Two competing frontend foundations | DONOR ONLY / SELECT ONE | 16 |
| Infrastructure | Production operations validation | Stage 23 / PR #98 | Seven workflows PASS on exact head | Open PR; rebuild triggers on canonical branch | VERIFIED DONOR | 5 |
| Infrastructure | HTTP observability | Stage 24 / PR #101 | Seven workflows PASS on exact head | Inherits Stage 23 files; log leakage review needed | VERIFIED DONOR | 6 |
| Infrastructure | External alerting/provider integration | Not implemented | No target environment evidence | Provider, secrets and retention unknown | BLOCKED BY DEPLOYMENT DECISION | 19 |
| Frontend | Customer RTL/mobile-first UI | FE donors | Build/E2E incomplete | Must not contain financial calculations | REBUILD | 15 |
| Frontend | Admin/Operator RTL UI | AP/OP donors | Build/typecheck/E2E incomplete | Duplicate foundations and permission navigation | REBUILD | 16 |
| White-label | Theme/logo/domain discovery | AP donors | Written, not fully executed | Tenant architecture not yet accepted as final storage model | DONOR ONLY / DESIGN REVIEW | 17 |
| Tenant Safety | Tenant/company/branch boundary | Stage 02/03 and AP donors | Partial tests/contracts | Competing assumptions; no final architecture decision | NEEDS DECISION BEFORE NEW PERSISTENCE | 9 |

## Duplicate conflicts to resolve before integration

1. RC2 Order lifecycle vs Stage 03 Order lifecycle.
2. Existing order/trade/settlement persistence vs `trading_orders` and `trading_quotes`.
3. AP permission catalog vs OP permission/bootstrap contract.
4. AP routes/envelopes vs OP routes/envelopes.
5. AP-20 frontend vs OP frontend shell/pages.
6. Customer activity "timeline" wording vs actual current-record feed.
7. Internal balance projection vs Kimia authoritative balances.

## Canonical integration sequence

1. Preserve RC2 as immutable recovery baseline.
2. Correct and lock Kimia source-of-truth documentation and architecture guards.
3. Correct Stage 02 balance projection boundary and decimal/unit contracts.
4. Revalidate Stage 01 Kimia reads against accepted behavior.
5. Rebuild Stage 23 production-operations slice and run all seven gates.
6. Rebuild Stage 24 observability slice and run security/log-leakage plus all gates.
7. Consolidate Customer API contract onto the RC2-derived branch; replace financial asset sourcing with Kimia-backed reads.
8. Select one Backoffice envelope and one permission catalog; rebuild OP-01 foundation.
9. Decide the single Quote → Order lifecycle without creating parallel Order persistence.
10. Rebuild compatible Stage 03 validation/idempotency components as one complete slice.
11. Rebuild Admin/Operator read-only dashboard and queues.
12. Rebuild settlement completion around Kimia Intent/Result/Re-read/Reconciliation.
13. Validate delivery and settlement sensitive actions with permission and IDOR tests.
14. Validate any required migration independently with fresh/rollback/MySQL/concurrency.
15. Build Customer frontend against locked OpenAPI and session/auth contract.
16. Select and build one Admin/Operator frontend foundation.
17. Complete White-label and tenant architecture decision before tenant-specific persistence expansion.
18. Enable Kimia Write only after real Ground Truth, explicit approval and isolated tests.
19. Run final full regression, browser E2E, production compose, backup/restore and target-environment deployment checks.

## Test truth summary

| Track | Current truth |
|---|---|
| RC2 | EXECUTED — PASS |
| Customer Final Regression | EXECUTED — PASS according to merged closure evidence |
| Stage 00–02 | EXECUTED — PASS for Business Engine Baseline |
| Stage 03 | EXECUTED — PASS only for Business Engine Baseline; full RC2 integration gates NOT EXECUTED |
| AP chain | WRITTEN — NOT EXECUTED for majority |
| OP-02..05 | WRITTEN — NOT EXECUTED |
| AP/OP frontend | NOT EXECUTED for install/build/typecheck/E2E |
| Stage 23 | EXECUTED — PASS on exact donor SHA; NOT MERGED |
| Stage 24 | EXECUTED — PASS on exact donor SHA; NOT MERGED |
| Canonical integrated recovery branch | NOT EXECUTED — integration has not started |

## Immediate next action

Create the **Recovery Integration Plan** with explicit slice boundaries, donor files, excluded files, required tests and stop conditions. No product code is integrated before that plan is recorded.
