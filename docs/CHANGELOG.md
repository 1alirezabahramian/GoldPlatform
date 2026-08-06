# GoldPlatform Changelog

## 2026-08-06 — Recovery security hardening

### Merged

- PR `#176` — Aligned final-audit documentation with the then-current canonical head.
- PR `#177` — Added explicit HTTP regression proof that customer order and delivery lists exclude another customer's records.
- PR `#178` — Replaced raw admin audit/outbox model responses with explicit operational allowlists.
- PR `#179` — Replaced raw operator order/delivery queue responses with explicit operational allowlists.
- PR `#180` — Replaced raw operator delivery-action responses with explicit allowlists and proved the full `requested → approved → ready → delivered` flow.
- PR `#181` — Added explicit per-action operator permissions while preserving existing `operator` and `admin` access through an additive migration.

### Validation

Latest validated security change before this documentation update:

- PR: `#181`
- Head SHA: `016d6f878e69e425badce36d92799949b87fd180`
- Operational Readiness #11: **EXECUTED — PASS**
- Backend RC1 Validation #302: **EXECUTED — PASS**
- Canonical merge SHA: `fe0aad5c4920a650da2d9ba0755ab7883e5bf4a2`

### Safety boundaries preserved

- No Kimia Write behavior was enabled.
- No financial rule or independent customer balance authority was introduced.
- No tenant, company or branch architecture was guessed.
- Custody remains GoldPlatform-owned and separate from Kimia financial balances.
- Production Ready remains unclaimed pending final release evidence and closure gates.
