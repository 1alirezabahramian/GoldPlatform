# Phase 05 — Cross-platform PWA Foundation

Status: IMPLEMENTED — CI PENDING

## Scope

- Installable customer web application for supported browsers on Android, iPhone/iPad and desktop environments.
- Web app manifest, standalone display metadata, theme metadata and scalable icon.
- iOS/Android mobile-web-app metadata and `viewport-fit=cover` support.
- Safe offline page for shell navigation.
- Service worker registration and shell-only caching.

## Financial safety boundary

- API requests under `/api/` are never intercepted or cached by the service worker.
- Money, Gold, Coin and Currency balances remain online-only and sourced from Kimia through Backend contracts.
- No order, settlement, delivery action, balance mutation or sensitive operation is available offline.
- No placeholder or stale financial balance is shown as current data.
- Custody remains separate from financial balances.

## Platform status

- PWA installability foundation: implemented.
- Responsive web on Android/iOS/desktop: implemented at application level.
- Native Android package: NOT IMPLEMENTED.
- Native iOS package/App Store wrapper: NOT IMPLEMENTED.
- Native Windows package: NOT IMPLEMENTED.
- Real-device installation and safe-area visual audit: NOT EXECUTED.

## Validation

- Contract test covers manifest wiring, mobile metadata, API cache exclusion and offline financial safety copy.
- Merge is allowed only after Customer Frontend, Frontend Release Validation, Backend RC1 Validation and Operational Readiness pass on the exact Head SHA.
