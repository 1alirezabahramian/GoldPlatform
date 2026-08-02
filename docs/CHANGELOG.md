# Changelog

## Unreleased

### Added

- Multi-tenancy impact audit covering current schema, Kimia projections, Auth, Catalog,
  Wallet/Ledger, integrations, queues, and Frontend boundaries.
- Accepted ADR-026 selecting shared-schema tenancy and recording all five owner decisions:
  tenant-scoped mobile uniqueness, one active Kimia connector per tenant in the first
  release, separate Platform Super Admin, and verified-domain tenant resolution with
  authenticated tenant cross-checking.
- Tenant root and verified-domain foundation: `tenants`, `tenant_domains`, normalized Host,
  fail-closed Resolver, request-scoped Context, inactive Middleware alias, and negative
  isolation tests.
- GitHub Actions Backend test workflow for Laravel 13 on PHP 8.4 with an in-memory SQLite
  database and Kimia writes disabled.
- CI safety and verification runbook.
- Framework-neutral Persian/RTL and White-label Design System foundation covering semantic
  tokens, exact financial presentation, panel shells, component states, accessibility,
  responsiveness, and Backend/Frontend boundaries.
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

- Multi-tenancy moved from an owner-decision stop condition to bounded implementation;
  all-table migration, unreviewed index replacement, and live credential movement remain
  prohibited.
- Clarified ADR-024 so mobile uniqueness applies inside one Tenant; the current global
  database constraint remains interim until the reviewed user table-group migration.
- Removed a duplicate `StoreOrderRequest::rules()` declaration that made the existing PHP
  file unloadable; its authorization and validation rules were otherwise preserved.
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

- Tenant root/domain code and two new-table migrations are prepared, but no Migration,
  seed/backfill, existing unique-index change, or production-route Middleware activation
  has been applied.
- CI workflow structure and local references are prepared; its first GitHub Actions run is
  still pending and must not be reported as passed yet.
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
