# GoldPlatform Changelog

## 2026-08-04 — Business Engine Stage 00

### Added

- GitHub Actions workflow `Business Engine Baseline`
- Automated Composer validation
- Automated PHP syntax validation
- Automated `migrate:fresh` safety check on SQLite
- Automated execution of the existing Laravel test suite

### Fixed

- Invalid method placement in legacy Kimia `AccountService`
- Invalid method placement in legacy Kimia `CustomerService`
- Duplicate `rules()` method in `StoreOrderRequest`
- Registration fields that did not match the actual `users` schema
- Outdated wallet test that expected hard-coded Coin/Currency accounts

### Verified

- Composer validation: PASS
- PHP syntax: PASS
- Fresh migrations: PASS
- Existing tests: PASS

### Not Changed

- Financial formulas
- Kimia Action Codes
- Kimia Product/Account/Currency/Coin IDs
- Wallet financial rules
- Ledger rules
- Order state machine
- Live Kimia write behavior

### Remaining Risks

- Multiple legacy Kimia implementations and PSR-4 warnings
- No live Kimia read-only contract verification in CI
- Financial engines are not yet production-ready
