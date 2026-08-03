# ADR-027 — Financial Decimal Model Contract

- **Status:** Accepted
- **Date:** 2026-08-03
- **Scope:** Order, Trade, and Ledger model precision

## Context

GoldPlatform stores money and weight in database `decimal` columns. Returning these values without explicit Eloquent casts leaves model output behavior implicit and risks accidental conversion to binary floating-point values in later services, resources, or tests.

The existing database precisions are:

- `orders.gold_weight`: 3 decimal places
- `orders.gold_price`, `orders.commission`, `orders.total_price`: 0 decimal places
- `trades.quantity`: 6 decimal places
- `trades.unit_price`, `trades.total_amount`: 2 decimal places
- `ledger_entries.amount`: 6 decimal places

This ADR does not approve or change any financial formula, currency rule, pricing rule, or Kimia behavior.

## Decision

The related Eloquent models must use `decimal:n` casts matching their existing database columns.

Decimal model values are treated as exact strings. Application code must not convert them to `float` for financial or weight calculations.

## Consequences

- Model precision is explicit and testable.
- Negative ledger values remain supported.
- No database migration is required.
- Any future precision change requires a separately reviewed migration and business decision.
