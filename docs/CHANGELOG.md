# GoldPlatform Changelog

## 2026-08-04 — Customer Panel & API Contract Phase Closure

- Completed and merged CP-01 through CP-18 on `feature/goldplatform-developer-mcp`.
- Added versioned Customer API contracts under `/api/v1/customer/*`.
- Added Customer API OpenAPI 3.1 specification and regression gates.
- Standardized success/error envelopes, pagination, status filtering, sorting and ISO date filtering.
- Added `X-Request-ID` traceability and private no-store response protection.
- Added ownership, authorization, validation and contract regression tests.
- Final Regression PR #126 passed all six standard CI gates and merged as `a0c6baea327945371251e39e1e8fd89273e4ec2e`.
- Phase Closure remains documentation-only: no migration, financial rule, Ledger/Wallet change or Kimia write operation was introduced.

### Remaining out-of-scope risks

- Live Kimia voucher write payload and posting behavior require separate evidence and approval.
- Authentication/OTP blockers documented in project state remain outside this phase.
- Real customer UI implementation and end-to-end browser testing are the next product-delivery track.
