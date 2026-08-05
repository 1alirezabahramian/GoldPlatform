# ADR-030 — Customer Frontend Technology

## Status

Accepted — 2026-08-06

## Decision

The GoldPlatform customer application uses Nuxt 4, Vue 3 and strict TypeScript as an independent package under `frontend/customer-app`.

## Constraints

- Persian, RTL and mobile-first.
- Runtime configuration supports White-label brand name, tagline and Customer API base.
- Money, Gold, Coin and Currency remain Kimia-backed through the Backend Customer API.
- Custody remains a separate GoldPlatform-owned domain.
- Money and weight stay strings at the API boundary; the frontend performs no authoritative financial calculation or Rial/Toman conversion.
- Sensitive reads use `cache: no-store`.
- Mutations use no automatic retry.
- Internal Kimia, Voucher, Ledger, Wallet and Settlement identifiers or terminology are not customer-facing.
- No Kimia endpoint, credential or code is embedded in the frontend.

## Evidence and preservation

Historical PR #140 was inspected file-by-file as evidence. It was closed without merge and was not cherry-picked. The accepted foundation is reconstructed directly on canonical recovery commit `71467f247a6a3cdcda858b393781ed9b2c9f4e03`.

## Operational impact

Node.js 22 is required. Pull requests touching the package must pass dependency installation, contract tests, strict typecheck, production build and frontend secret scan.

## Financial impact

None. This ADR creates no financial rule, balance source, migration, Kimia Write, Ledger posting or settlement behavior.
