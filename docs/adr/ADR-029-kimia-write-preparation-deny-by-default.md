# ADR-029 — Kimia Write Preparation Is Deny-by-Default

- Status: Accepted
- Date: 2026-08-04
- Stage: 14

## Context

GoldPlatform has confirmed read-only Kimia contracts, but real write endpoints, payload fields, action mappings, account mappings and reversal behavior are not yet fully confirmed from production evidence.

## Decision

Kimia write execution remains disabled.

A write operation may only be prepared when all of the following are explicit in `config/kimia_write.php`:

- approved flag;
- HTTP method;
- URI;
- required payload fields;
- optional compensation operation.

The production registry is intentionally empty. No example ProductId, CoinId, CurrencyId, account number, endpoint or Action code is hard-coded.

Preparation requires an idempotency key and creates a deterministic payload hash. Audit context excludes the payload itself.

## Consequences

- Unknown operations fail closed before HTTP dispatch.
- Payloads cannot be silently invented or normalized beyond deterministic key ordering.
- Actual execution needs a later owner-approved implementation and a production evidence package.
- Retry for write operations is not enabled automatically.
- Compensation is a named approved operation, not an assumed rollback request.

## Exit criteria for real write enablement

1. real API request and response evidence;
2. owner approval of action and account mappings;
3. idempotency behavior confirmed;
4. compensation/reversal behavior confirmed;
5. secure environment validation;
6. dedicated integration tests and controlled rollout.
