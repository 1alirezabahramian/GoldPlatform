# GoldPlatform — Project State

- Updated: 2026-08-06
- Canonical recovery branch: `recovery/rc2-product-rebuild`
- Canonical Head SHA before Phase 06: `270137f526e60c6784d427db16e0492c8fcfa3b7`
- Latest canonical merge: PR `#189` — Cross-platform PWA Foundation
- Delivery status: **RELEASE CANDIDATE — FINAL AUDIT IN PROGRESS**
- Production Ready: **NOT CLAIMED**

## Source-of-truth contract

Kimia is the final source of truth for customer financial balances:

- Money
- Gold
- Coin
- Currency

GoldPlatform must not publish an independent or competing final balance for these four asset classes.

Internal Ledger, Journal, Event Store, Idempotency Registry and Balance Projection are permitted only for audit, traceability, intent/result recording, workflow, reconciliation and duplicate/incomplete-operation detection. They are not final customer balance authorities.

Any internal projection or snapshot must be Kimia-derived, timestamped, rebuildable and reconcilable. In a conflict, Kimia is authoritative.

GoldPlatform is the source of truth for physical Custody / Amanat. Custody remains separate from financial balances.

## Completed implementation phases

### Phase 01 — Customer Experience

Status: **COMPLETE — TESTED — MERGED**

- Professional Persian RTL mobile-first shell.
- Contract-driven Orders, Custody and Delivery resources.
- Safe loading, empty, unavailable, error and retry states.
- Kimia financial resources fail closed until verified customer account resolution exists.

### Phase 02 — Operator Experience

Status: **COMPLETE — TESTED — MERGED**

- Responsive operator queue workspace.
- Real Backend order and delivery queue contracts.
- Permission-denied and error handling.
- No Frontend financial calculation or Kimia Write.

### Phase 03 — Admin Experience

Status: **COMPLETE — TESTED — MERGED**

- Read-only Audit and Outbox monitoring.
- Explicit typed allowlisted contracts.
- Responsive operational tables and summary cards.

### Phase 04 — Shared Design System

Status: **COMPLETE — TESTED — MERGED**

- Shared design tokens and reusable component patterns.
- Focus, reduced-motion and touch-target accessibility rules.
- One shared source consumed by Customer and Backoffice applications.

### Phase 05 — Cross-platform PWA

Status: **COMPLETE — TESTED — MERGED**

- Installable PWA foundation for supported Android, iOS/iPadOS and desktop browsers.
- Manifest, standalone metadata, Safe Area support and offline page.
- Service Worker excludes `/api/` requests from interception and caching.
- Financial balances remain online-only and Kimia-authoritative.

### Phase 06 — Final Audit and Handoff

Status: **IMPLEMENTED — CI PENDING**

- Final audit and handoff report.
- Repository and production release checklist.
- Canonical state and changelog alignment.
- Operational gate requiring final release documents and safety declarations.

## Delivery PR evidence

- PR `#187` — Customer and Operator design phases — **MERGED**.
- PR `#188` — Admin and Shared Design System — **MERGED**.
- PR `#189` — Cross-platform PWA foundation — **MERGED**.
- Canonical merge SHA after PR `#189`: `270137f526e60c6784d427db16e0492c8fcfa3b7`.

The delivery PRs passed their applicable Customer Frontend, Admin Operator Frontend, Frontend Release Validation, Backend RC1 Validation and Operational Readiness workflows on their exact Head SHAs before controlled merge.

## Security and architecture safeguards

- Customer list owner-isolation regression proof exists.
- Admin Audit/Outbox responses use explicit allowlists.
- Operator queue and delivery-action responses use explicit allowlists.
- Operator actions use explicit Backend permissions.
- Frontend navigation is not authorization.
- Direct customer financial settlement completion remains blocked without verified Kimia result evidence and post-write balance readback.
- Kimia Read and Write remain separate.
- Kimia Write remains disabled until accepted ground truth exists.
- Money and weight calculations must use exact Decimal or String Decimal; float is prohibited.

## Remaining verified blockers

1. Verified authenticated customer-to-Kimia account resolution.
2. Kimia Write payloads, codes, retries and post-write readback ground truth.
3. Accepted tenant/company/branch architecture and isolation proof.
4. Production TLS, secrets, WAF and external monitoring validation.
5. Executed deployment, migration and rollback rehearsal.
6. Executed backup/restore evidence.
7. Live authentication and OpenAPI/environment integration validation.
8. Real-device PWA installation and visual audit.
9. Native Android, iOS and Windows packaging.

## Release classification

- Repository implementation: **RELEASE CANDIDATE**
- Final audit package: **IMPLEMENTED — CI PENDING**
- Production Ready: **NOT CLAIMED**
- Kimia Write: **BLOCKED BY GROUND TRUTH**
- Native packages: **NOT IMPLEMENTED**

## Current next step

Run all applicable CI on the exact Phase 06 Head SHA. Merge only when the PR is mergeable, the Head has not moved and the merge uses an expected-Head-SHA lock. After merge, record the final canonical SHA and classify Phase 06 as **COMPLETE — TESTED — MERGED**.
