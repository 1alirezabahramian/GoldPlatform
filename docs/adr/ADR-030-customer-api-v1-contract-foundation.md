# ADR-030 — Customer API v1 Contract Foundation

- Status: Accepted for implementation; completion requires green CI
- Date: 2026-08-04
- Owner: Alireza Bahramian
- Branch: `work/customer-panel-contract-foundation`

## Context

Backend RC1 already exposes Legacy customer endpoints, but their responses mix hand-written arrays and raw Eloquent pagination. Some responses can expose internal fields or Kimia-linked identifiers. The real customer frontend needs a stable, versioned and testable contract without changing financial rules or the RC2 production-candidate workflow.

## Decision

1. Customer API v1 is introduced incrementally under `/api/v1/customer`.
2. Legacy endpoints remain unchanged during migration.
3. Responses use a stable success envelope with `data`, `meta` and `message`.
4. `meta` contains `request_id`, ISO-8601 `generated_at` and `api_version`.
5. Customer responses do not expose `user_id`, `account_id`, `external_asset_id`, Kimia IDs or accounting implementation details.
6. Balance values are decimal strings and are produced from one Ledger-derived projection snapshot.
7. Coin and Currency remain dynamic.
8. Customer ownership comes only from the authenticated Sanctum user.
9. This foundation is Read-only. It does not add pricing, commission, settlement, conversion, Kimia write or financial migrations.
10. Rial/Toman display normalization is intentionally not guessed here and must be finalized with a dedicated tested contract.

## Initial endpoint

- `GET /api/v1/customer/dashboard`

The endpoint returns owned active asset accounts and customer-owned counts for active orders, custodies and delivery requests.

## Security consequences

- Customer role is mandatory.
- Internal and Kimia-linked identifiers are excluded by contract tests.
- Existing rate limiting, request correlation, no-store API headers and Sanctum controls remain active.
- The endpoint never accepts `user_id` from the client.

## Compatibility

- No Legacy route is removed or renamed.
- No RC2 workflow, Production Compose or Kimia configuration is changed.
- Further customer endpoints must reuse this envelope or explicitly supersede this ADR.

## Completion gate

- Migration fresh passes.
- `CustomerApiV1ContractTest` passes.
- Full regression passes.
- OpenAPI parses successfully.
- Route and security checks pass.
- PR CI is green.
