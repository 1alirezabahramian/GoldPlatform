# Customer Frontend Foundation

## Status

Implemented — CI pending

## Scope

- Executable Nuxt 4 / Vue 3 package.
- Strict TypeScript.
- Persian RTL and mobile-first shell.
- Runtime White-label configuration.
- Typed Customer API client.
- Loading, Empty, Error and Unavailable states.
- No-store for sensitive reads and mutations.
- No automatic mutation retry.
- Dedicated frontend CI for install, contract test, typecheck, production build and secret scan.

## Safety boundaries

This foundation contains no dashboard balance values, mock financial data, pricing formula, Rial/Toman conversion, Kimia direct access, Kimia identifiers, Ledger/Wallet terms, authentication behavior or transaction workflow.

Unknown or unavailable financial values must not be converted to zero.

## Historical comparison

PR #140 was used only as historical evidence. Reusable choices were compared against the current canonical architecture. No historical commit was merged or cherry-picked.

## Test status

- Contract test: WRITTEN — NOT EXECUTED locally
- Typecheck: NOT EXECUTED locally
- Production build: NOT EXECUTED locally
- Frontend secret scan: NOT EXECUTED locally
- GitHub Actions: PENDING

Local execution was not claimed because the available execution environment did not have GitHub CLI/network checkout access. The authoritative validation is the PR CI on the exact Head SHA.
