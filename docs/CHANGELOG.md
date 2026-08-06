# GoldPlatform Changelog

## 2026-08-06 — Final UX delivery and handoff

### Merged delivery phases

- PR `#187` — Completed Customer and Operator experience phases.
- PR `#188` — Completed Admin experience and Shared Design System.
- PR `#189` — Added Cross-platform PWA foundation.

### Canonical delivery checkpoint

- Branch: `recovery/rc2-product-rebuild`
- Canonical SHA after PR `#189`: `270137f526e60c6784d427db16e0492c8fcfa3b7`
- Customer Experience: **COMPLETE — TESTED — MERGED**
- Operator Experience: **COMPLETE — TESTED — MERGED**
- Admin Experience: **COMPLETE — TESTED — MERGED**
- Shared Design System: **COMPLETE — TESTED — MERGED**
- Cross-platform PWA: **COMPLETE — TESTED — MERGED**

### Final audit package

- Added `docs/release/FINAL_AUDIT_AND_HANDOFF.md`.
- Added `docs/release/RELEASE_CHECKLIST.md`.
- Aligned `docs/PROJECT_STATE.md` with the current canonical delivery SHA.
- Extended Operational Readiness validation to require final handoff artifacts and safety declarations.

### Safety boundaries preserved

- Kimia remains the final source of truth for Money, Gold, Coin and Currency.
- GoldPlatform remains the source of truth for physical Custody / Amanat.
- No independent customer financial balance was introduced.
- No Kimia Write behavior, payload, Action Code or Transaction Code was guessed or enabled.
- `/api/` traffic remains excluded from Service Worker caching.
- Production Ready remains unclaimed pending environment-specific evidence.

## 2026-08-06 — Recovery security hardening

### Merged

- PR `#176` — Aligned final-audit documentation with the then-current canonical head.
- PR `#177` — Added explicit HTTP regression proof that customer order and delivery lists exclude another customer's records.
- PR `#178` — Replaced raw admin audit/outbox model responses with explicit operational allowlists.
- PR `#179` — Replaced raw operator order/delivery queue responses with explicit operational allowlists.
- PR `#180` — Replaced raw operator delivery-action responses with explicit allowlists and proved the full `requested → approved → ready → delivered` flow.
- PR `#181` — Added explicit per-action operator permissions while preserving existing `operator` and `admin` access through an additive migration.

### Validation

- PR `#181` Head SHA: `016d6f878e69e425badce36d92799949b87fd180`
- Operational Readiness #11: **EXECUTED — PASS**
- Backend RC1 Validation #302: **EXECUTED — PASS**
- Canonical merge SHA at that checkpoint: `fe0aad5c4920a650da2d9ba0755ab7883e5bf4a2`
