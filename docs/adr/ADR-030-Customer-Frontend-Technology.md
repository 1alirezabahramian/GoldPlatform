# ADR-030 — Customer Frontend Technology

## Status

Accepted — 2026-08-05

## Decision

The GoldPlatform customer application uses Nuxt 4 with Vue 3 and strict TypeScript.

## Reasons

- First-class TypeScript and file-based application structure.
- Strong RTL and Persian support through normal web standards.
- Server rendering and deployment flexibility without moving financial logic to the browser.
- Reusable runtime configuration for White-label brand names and API endpoints.
- A clear boundary between the customer UI and `/api/v1/customer`.

## Architecture constraints

- Complex financial, Ledger and Kimia behavior remains in Backend.
- The frontend never calculates authoritative balances, fees, prices or settlement outcomes.
- Money and weight values remain strings at the API boundary.
- Coin and Currency remain dynamic.
- Custody remains separate from financial assets.
- Internal Kimia identifiers and accounting terms are not shown to customers.
- Customer responses use `cache: no-store`; mutation retries are not automatic.
- Idempotency keys are supplied only for supported mutation contracts.

## Operational impact

- Node.js 22 or newer is required for the customer application pipeline.
- Pull requests touching the customer app must pass dedicated TypeScript and production-build checks.
- Deployment packaging will be decided with Infrastructure; this ADR does not change the current production topology.

## Alternatives considered

React/Next and plain Vue/Vite were not selected. Nuxt provides the required application structure, SSR capability and Vue-based maintainability with less custom platform plumbing.

## Financial and Kimia impact

None. This decision adds no financial rule, migration, Kimia write operation, Ledger posting or settlement behavior.
