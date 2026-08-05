# GoldPlatform — Main vs Historical Product Path Matrix

> Status: First Pass
>
> Evidence date: 2026-08-05
>
> Comparison: `main` → `feature/goldplatform-developer-mcp`
>
> Base SHA: `31d55fac545201c7b436e940e48e9dcd89bd553d`
>
> Merge base: `0d618bf67963ba0022880a730db2dafb3d77d3fa`

## 1. Live comparison facts

- Status: `diverged`
- Historical product line ahead of `main`: 446 commits
- Historical product line behind `main`: 123 commits
- Direct merge is prohibited.

## 2. First-pass domain classification

| Domain | Evidence from compare | Classification | Recovery action |
|---|---|---|---|
| CI / Production workflows | Multiple workflows exist only on historical product line | `HEALTHY DONOR — VERIFY` | Re-run on canonical candidate; do not copy all blindly. |
| Kimia integration | Historical line removes several parallel clients and introduces `App\Integrations\Kimia` | `HIGH-VALUE DONOR / CANONICAL CANDIDATE` | Compare with Stage 01 on `main`; select one read path only. |
| Auth / User | Historical line changes User, Factory, Observer and email/mobile migrations | `CONTRACT CONFLICT` | Reconcile schema, model, factory, registration and API before transfer. |
| Customer API | Versioned controllers, middleware, presenters, OpenAPI and tests exist on historical line | `HIGH-VALUE DONOR` | Preserve CP closure; reconstruct dependency-closed slices. |
| Custody | `CustodyAsset`, enum, service, migration and tests exist on historical line | `HIGH-VALUE DONOR` | Candidate for canonical recovery after ownership/idempotency tests. |
| Delivery | `DeliveryRequest`, enum, service, migration and tests exist on historical line | `HIGH-VALUE DONOR` | Candidate after transition, ownership and audit verification. |
| Order | Historical line adds enum, migration and state machine while `main` has a simpler model | `CONTRACT CONFLICT` | No transfer until canonical lifecycle and persistence mapping are accepted. |
| Settlement | Historical line adds model, enum, service and migration | `CONFLICTED DONOR` | Recover only with canonical Order/Trade lifecycle. |
| Financial / Ledger | Historical line expands Ledger and projection/reservation paths | `MIXED: OPERATIONAL + ARCHITECTURE DRIFT RISK` | Keep audit/workflow functions; reject customer-final-balance semantics. |
| Wallet / Balance projection | Historical line adds projections and reservations | `ARCHITECTURE DRIFT RISK` | May be retained only as rebuildable operational projection sourced from Kimia, never final balance. |
| Audit / Idempotency / Outbox | Complete models, middleware, services and migrations exist | `HIGH-VALUE DONOR` | Recover as operational infrastructure after dependency review. |
| Permission / Backoffice | Foundation routes and OpenAPI exist, while AP/OP add parallel contracts | `PARALLEL CONFLICT` | Build one additive catalog and one route/response contract. |
| Frontend | Foundations live outside this direct file comparison or on later branches | `NOT YET SELECTED` | Compare AP-20 vs OP-03+ and FE-02 separately with real build gates. |
| Documentation | Large accepted/historical mix, including duplicate ADR numbers and two Project State files | `DOCUMENTATION CONFLICT` | Classify Accepted, Superseded, Historical or Needs Decision. |

## 3. Immediate critical findings

### Kimia

The historical product line removes multiple legacy Kimia paths and introduces a consolidated integration tree under:

- `backend/app/Integrations/Kimia/Client`
- `backend/app/Integrations/Kimia/Repositories`
- `backend/app/Integrations/Kimia/Services`
- `backend/app/Integrations/Kimia/Write`

This is a strong canonical candidate, but it must be compared against the Stage 01 implementation merged on `main` before selection.

### User/Auth

The historical line contains both mobile-first changes and an additional email-auth migration. Therefore neither current `main` nor historical User code is accepted without a full schema-to-model-to-factory comparison.

### Financial source of truth

The historical line contains `BalanceProjectionService`, `BalanceReservationService` and wallet asset identity. These are not automatically rejected, but any description or behavior making them the final customer balance is superseded. They are acceptable only as rebuildable operational projections sourced from Kimia.

### Order

The historical line contains a complete Eloquent Order state machine, while Stage 03 introduces a separate domain aggregate. This confirms the risk of two competing state machines. Order remains blocked from integration until one canonical lifecycle is selected.

### Custody and Delivery

The historical line contains complete model/service/migration/test slices for physical Custody and Delivery. These are currently the strongest high-value donor candidates because GoldPlatform is the source of truth for physical Custody.

## 4. Fixed next comparisons

1. Auth/User path matrix.
2. Kimia path matrix.
3. Wallet/Ledger/Projection path matrix.
4. Order/Trade/Settlement path matrix.
5. Custody/Delivery path matrix.
6. Route/Permission/OpenAPI matrix.
7. Frontend build comparison.

Current status: `PATH MATRIX FIRST PASS COMPLETE — AUTH/USER DEEP COMPARISON NEXT`.
