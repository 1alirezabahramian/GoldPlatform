# ADR — Trading Validation Foundation

Status: Accepted
Date: 2026-08-04

## Context

Stage 03 requires a validation boundary before Quote, Order, Settlement or Ledger integration. The approved project sources require tenant isolation, traceability, idempotency and no guessed financial rules. No implemented Quote or Order aggregate existed on the Stage 02 branch when this work started.

## Decision

1. Trading validation is implemented as a composable pipeline of explicit rules.
2. Every validation context requires FinancialScope, TraceId, CorrelationId and IdempotencyKey.
3. All related trading entities must belong to the exact same FinancialScope.
4. Validation returns structured failures with stable codes and messages.
5. Rules may collect multiple failures; they do not silently mutate domain state.
6. Quote state, Order transition rules, price freeze duration, customer limits, wallet availability, commission, spread and Kimia mappings are not introduced without an approved source.
7. Financial posting and wallet mutation remain outside this validation foundation.

## First approved rule

`MatchingFinancialScopeRule` rejects cross-tenant, cross-company or cross-branch entity combinations by comparing the canonical FinancialScope key.

## Consequences

- Future Quote and Order aggregates can reuse the same validation boundary.
- Tenant isolation is enforced before financial side effects.
- Business-specific rules can be added independently when their source is approved.
- The validation engine does not invent trading or accounting policy.

## Explicitly not decided here

- Quote lifecycle and expiration states.
- Order lifecycle transitions beyond the already approved project-level status names.
- Freeze duration by customer group.
- Pricing formulas, spread or commission.
- Balance, credit and reservation policy.
- Kimia write payloads and voucher mappings.
