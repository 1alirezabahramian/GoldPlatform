# FE-02 — Nuxt Customer Application Shell

## Status

Implemented — Awaiting dedicated frontend CI and merge.

## Scope

- Nuxt 4 + Vue 3 + strict TypeScript application.
- Persian RTL, mobile-first shell.
- Reuse of FE-01 design tokens.
- Runtime White-label brand and API base configuration.
- Typed Customer API boundary with `no-store` and no automatic mutation retry.
- Initial human-language dashboard shell.
- Dedicated GitHub Actions typecheck and production-build gate.

## Files

- `frontend/customer-app/package.json`
- `frontend/customer-app/nuxt.config.ts`
- `frontend/customer-app/tsconfig.json`
- `frontend/customer-app/app/app.vue`
- `frontend/customer-app/app/pages/index.vue`
- `frontend/customer-app/app/assets/main.css`
- `frontend/customer-app/app/composables/useCustomerApi.ts`
- `.github/workflows/customer-frontend.yml`
- `docs/adr/ADR-030-Customer-Frontend-Technology.md`

## Safety boundaries

- No financial calculations in frontend.
- No Kimia credentials, codes or direct calls.
- No Ledger, Wallet or Settlement mutation.
- No customer-facing dependency on internal identifiers.
- No financial or database migration.

## Validation required before closure

1. Customer Frontend Typecheck passes.
2. Customer Frontend production build passes.
3. Existing six repository gates pass.
4. PR is merged to `feature/goldplatform-developer-mcp`.

## Next stage

FE-03 should implement authentication shell and read-only Dashboard integration against the merged Customer API contract. OTP behavior must reuse the existing backend contract and must not invent provider behavior.
