# Canonical Frontend Gap Audit

Status: **Accepted recovery evidence — implementation not yet integrated**

Canonical branch inspected: `recovery/rc2-product-rebuild`
Canonical base before this audit: `3f8014147985bda8122eabe58e50f06eb1c1572f`

## Result

The canonical recovery branch currently contains the recovered backend and backend architecture guards, but it does not contain an executable Customer Frontend or Admin/Operator Frontend package.

Direct checks on the canonical branch found no root `package.json`, no `frontend/package.json`, and no `frontend-admin/package.json`.

This does not mean frontend work never existed. Historical pull requests contain preserved frontend evidence, but those pull requests were closed without merge and must not be treated as canonical implementation.

## Historical customer frontend evidence

PR #140 — `FE-02: add Nuxt customer application shell`

- State: CLOSED — NOT MERGED
- Historical base: `feature/goldplatform-developer-mcp`
- Historical head: `170fab67984267f7fc5361edf44a2f14f43e7677`
- Framework declared by the historical PR: Nuxt 4, Vue 3, strict TypeScript
- Historical files:
  - `.github/workflows/customer-frontend.yml`
  - `docs/adr/ADR-030-Customer-Frontend-Technology.md`
  - `docs/frontend/FE-02-NUXT-APPLICATION-SHELL.md`
  - `frontend/customer-app/app/app.vue`
  - `frontend/customer-app/app/assets/main.css`
  - `frontend/customer-app/app/composables/useCustomerApi.ts`
  - `frontend/customer-app/app/pages/index.vue`
  - `frontend/customer-app/nuxt.config.ts`
  - `frontend/customer-app/package.json`
  - `frontend/customer-app/tsconfig.json`

PR #138 preserves an earlier framework-neutral customer frontend foundation. It is historical evidence and must be compared before reconstructing FE-02.

## Historical Admin/Operator frontend evidence

PR #143 and its stack preserve Admin/Operator frontend work under `frontend-admin/`, including dashboard components, composables, pages, types and tests.

PR #143 and PR #144 were closed without merge. Their implementation is not canonical and must not be cherry-picked blindly because their backend contracts and base branches predate the current recovery hardening wave.

## Architecture constraints for recovery

Any reconstructed frontend must preserve these accepted rules:

1. Frontend is Persian, RTL, mobile-first and White-label capable.
2. Frontend contains no independent financial formulas, Rial/Toman conversion logic or Kimia identifiers.
3. Money, Gold, Coin and Currency balances are displayed only from Kimia-backed Customer API contracts.
4. Custody remains a separate GoldPlatform-owned domain.
5. Mutation requests have no automatic retry unless an approved idempotent contract explicitly permits it.
6. Sensitive responses use no-store semantics.
7. Internal Wallet, Ledger, Settlement, Voucher and Kimia references are not exposed to customers.
8. Admin/Operator UI cannot directly mutate balances or generate Kimia codes.

## Recovery decision

The next safe product step is not to merge the historical frontend stack. It is to reconstruct the smallest Customer Frontend foundation directly on the current canonical branch, comparing PR #138 and PR #140 file-by-file and adapting only verified reusable parts.

The first reconstruction PR should include only:

- executable customer app shell;
- RTL and Persian foundation;
- runtime White-label configuration;
- typed read-only Customer API client;
- no-store behavior;
- typecheck and production build workflow;
- no authentication, balance mutation, trading logic or Kimia direct access.

Admin/Operator frontend recovery remains a separate later slice after the Customer Frontend foundation is validated.

## Validation status

- Canonical frontend existence audit: **EXECUTED**
- Customer frontend build/typecheck on canonical: **NOT APPLICABLE — executable package absent**
- Admin/Operator frontend build/typecheck on canonical: **NOT APPLICABLE — executable package absent**
- Historical frontend tests: evidence only; not proof for canonical

## Safety

This audit changes documentation only. It introduces no frontend package, backend behavior, migration, API, permission, financial rule or Kimia Write.
