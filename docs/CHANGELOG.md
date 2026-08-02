# Changelog

## Unreleased

### Added

- Fail-closed Kimia write gate protecting active, direct-client, and preserved legacy paths.
- `kimia:safety-status` runtime guard command.
- Privacy-safe `kimia:inspect-sync-state` command for local projection counts and AccountId
  presence checks.
- PowerShell shop-verification runner that writes all checks to one ignored text report.
- ADR-025 and the shop verification runbook.
- Read-only Kimia balance repository path for `GET /api/voucher/balance/{id}`.
- Kimia-compatible serialization for the optional `includePeaks` boolean query.
- Read-only `kimia:inspect-balance` evidence command.
- Mock tests for balance path, raw negative values, boolean literals, and identifier validation.
- Consolidated product phases and Kimia UI evidence documentation.
- ADR-024 defining the one-to-one GoldPlatform account to Kimia `AccountId` binding.
- Identity constraint tests for shared national codes, unique mobiles, one-to-one account
  binding, and immutable Kimia identifiers.

### Changed

- Added `KIMIA_WRITES_ENABLED=false` to the configuration contract.
- Account names are omitted from `kimia:inspect-balance` by default and require an explicit
  display option.
- Reconciled project principles with the accepted Kimia/GoldPlatform source-of-truth boundary.
- Replaced stale product and Kimia status statements with the 2026-08-02 checkpoint.
- Removed national-code uniqueness from registration validation.
- Prepared migrations to allow repeated national codes while retaining a normal lookup
  index, and enforce a unique nullable `users.account_id` after a duplicate-link preflight.
- Added Eloquent guards that keep synchronized Kimia identifiers and established account
  bindings immutable.
- Documented mobile/national-code editing and the deferred multi-account entry experience.

### Verification

- Previous canonical suite: `23 passed / 160 assertions / 0 failures`.
- New write-gate, safety-status, sync-state, Balance, identity, and full-suite execution are
  queued in one shop report and are not yet reported as passed.
- Current static checkpoint: 150 PHP files parsed, 75 PSR-4 declarations checked,
  PowerShell AST parsed, 24 changed-document links resolved, and Diff/secret scan passed.
- New balance tests: prepared but not yet executed in the shop Docker runtime.
- New identity tests and migrations: prepared but not yet executed in the shop Docker
  runtime.
- Static verification for this checkpoint: 150 PHP files parsed successfully; Diff and
  changed-file secret scan passed.
