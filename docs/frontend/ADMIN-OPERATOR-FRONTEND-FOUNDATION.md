# Admin & Operator Frontend Foundation

## Status

Implemented — CI pending

## Scope

- Standalone Nuxt 4 / Vue 3 / strict TypeScript application under `frontend-admin`.
- Persian RTL and responsive shell.
- Separate Admin and Operator read-only pages.
- Runtime White-label brand configuration.
- Cookie-based API reads with `cache: no-store` and `retry: 0`.
- Dedicated Contract Test, Typecheck, Production Build and Secret Scan workflow.

## Canonical read contracts

Admin reads only:

- `GET /api/admin/audit-logs`
- `GET /api/admin/outbox`

Operator reads only:

- `GET /api/operator/orders/queue`
- `GET /api/operator/deliveries/queue`

## Safety boundaries

- No financial calculations, valuation or Rial/Toman conversion.
- No direct Kimia request, credential, identifier or payload.
- No balance mutation, settlement execution or sensitive workflow action.
- Navigation is not authorization; Backend role and permission middleware remain authoritative.
- No login or OTP behavior is invented because a canonical backoffice session-bootstrap contract is not established in this slice.
- Historical PRs #137 and #143 were evidence only and were not cherry-picked.

## Phase 4 validation

This slice is complete only after exact-Head-SHA success for:

- dependency install;
- contract tests;
- strict typecheck;
- production build;
- frontend secret scan;
- Backend RC1 regression.

Production Ready is not claimed by this foundation alone.
