# Changelog

## Unreleased

### Added

- Read-only Kimia balance repository path for `GET /api/voucher/balance/{id}`.
- Kimia-compatible serialization for the optional `includePeaks` boolean query.
- Read-only `kimia:inspect-balance` evidence command.
- Mock tests for balance path, raw negative values, boolean literals, and identifier validation.
- Consolidated product phases and Kimia UI evidence documentation.

### Changed

- Reconciled project principles with the accepted Kimia/GoldPlatform source-of-truth boundary.
- Replaced stale product and Kimia status statements with the 2026-08-02 checkpoint.

### Verification

- Previous canonical suite: `23 passed / 160 assertions / 0 failures`.
- New balance tests: prepared but not yet executed in the shop Docker runtime.
