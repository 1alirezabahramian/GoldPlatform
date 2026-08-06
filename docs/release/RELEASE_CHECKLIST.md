# GoldPlatform — Release Checklist

- Status: **IN PROGRESS**
- Canonical branch: `recovery/rc2-product-rebuild`
- Audited base SHA: `270137f526e60c6784d427db16e0492c8fcfa3b7`

## Mandatory repository gates

- [x] Customer Frontend implementation exists.
- [x] Operator Frontend implementation exists.
- [x] Admin Frontend implementation exists.
- [x] Shared Design System exists and is consumed by both frontends.
- [x] Cross-platform PWA foundation exists.
- [x] Backend RC1 workflow exists.
- [x] Frontend Release Validation workflow exists.
- [x] Operational Readiness workflow exists.
- [x] Customer owner-isolation regression proof exists.
- [x] Admin and Operator sensitive-response allowlists exist.
- [x] Operator actions have explicit Backend permission gates.
- [x] `/api/` traffic is excluded from Service Worker caching.
- [x] Project State, Changelog and Final Handoff documents are present.

## Exact-Head closure gates

These must be completed on the final-audit PR Head SHA:

- [ ] Operational Readiness — EXECUTED — PASS
- [ ] Backend RC1 Validation — EXECUTED — PASS
- [ ] Customer Frontend — EXECUTED — PASS, when triggered
- [ ] Admin Operator Frontend — EXECUTED — PASS, when triggered
- [ ] Frontend Release Validation — EXECUTED — PASS, when triggered
- [ ] PR Head unchanged after CI
- [ ] PR mergeable
- [ ] Merge performed with expected Head SHA
- [ ] Canonical merge SHA recorded

## Production-environment gates

These are outside repository-only validation and remain open until executed in the target environment:

- [ ] Production TLS and domain validation
- [ ] Production secret-store validation
- [ ] External monitoring and alert-delivery validation
- [ ] Production migration rehearsal
- [ ] Production rollback rehearsal
- [ ] Backup and restore execution evidence
- [ ] Live authentication/session validation
- [ ] Verified customer-to-Kimia account resolution
- [ ] Live OpenAPI/environment contract validation
- [ ] Real-device PWA installation and visual audit

## Ground-truth blocked gates

These must not be implemented by guessing:

- [ ] Kimia Write payload contract
- [ ] Kimia Action Code and Transaction Code mapping
- [ ] Kimia Write retry policy
- [ ] Post-write Kimia readback contract
- [ ] Tenant/company/branch architecture and isolation proof
- [ ] Native Android, iOS and Windows packaging decision

## Release decision

- Repository Release Candidate: **YES, subject to final-audit PR CI and merge**
- Production Ready: **NO — NOT CLAIMED**
- Kimia Write Ready: **NO — BLOCKED BY GROUND TRUTH**
