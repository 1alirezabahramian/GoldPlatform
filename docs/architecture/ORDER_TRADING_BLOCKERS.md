# Order and Trading Blockers

Status: **Blocked for production execution**

Date: 2026-08-03

This document records confirmed code-level blockers. It does not define new financial or customer rules.

## Public order endpoint

The current `POST /api/orders` route is outside `auth:sanctum`.

The controller:

- accepts a generic `Request` instead of a dedicated validated FormRequest;
- passes the complete request payload to the service;
- allows `user_id` to be supplied by the caller;
- returns the raw Order model.

No production order flow should rely on this endpoint until authentication, authorization, validation, resource serialization, idempotency, and audit behavior are approved and implemented.

## Order service mismatch

The current order service calculates totals directly from caller-supplied `gold_weight`, `gold_price`, and `commission` values.

This code is not accepted as the GoldPlatform pricing engine. It lacks an approved price snapshot, server-side price authority, product context, customer group limits, freeze expiry, and audit evidence.

## Trade service mismatch

The current trade service:

- reads `quantity` and `unit_price` from Order although those fields are not defined by the current Order model/table;
- transfers ledger value between hard-coded wallet account IDs `1` and `2`;
- marks the order completed directly;
- does not provide an approved idempotency key, replay protection, execution audit, or Kimia write reconciliation.

The hard-coded account IDs are sample behavior and must never become a financial rule.

## Required decision and implementation sequence

1. Approve the canonical Order lifecycle and statuses.
2. Approve authenticated actor and authorization rules.
3. Define server-owned pricing and price-snapshot contract.
4. Define Ledger account resolution without hard-coded IDs.
5. Define idempotency, audit, failure recovery, and Kimia reconciliation.
6. Replace or remove the current prototype services only after tests cover the approved contract.

Until then, the existing Order and Trade services are prototype code and must not be treated as production-ready.
