# Recovery Baseline Correction — RC2

Date: 2026-08-05
Owner: Alireza Bahramian
Status: Accepted

## Correct canonical rollback point

The canonical recovery baseline is the merged Stage 22 / Backend RC2 production-candidate commit:

- Merge commit: `cada4441184e59d09f5ddac567d7b9b8d19b34ae`
- Candidate head SHA: `d7e2a5981c475f3fb5ed7777c4d8dc538f86c146`
- Target historical branch: `feature/goldplatform-developer-mcp`

## Verified CI evidence on candidate head

All of the following completed successfully on `d7e2a5981c475f3fb5ed7777c4d8dc538f86c146`:

- Backend RC2 Candidate
- Backend RC1 Validation
- Security Hardening
- Stage 21 Performance
- Production Compose Validation
- Backup and Restore Drill

## Why RC2, not RC1

RC2 was the last integrated and fully gated backend state before development split into parallel Customer, Business Engine, Admin/AP, Operator/OP and frontend paths.

The earlier RC1 rollback decision is retained only as historical evidence and is superseded by this document.

## Recovery rules from this point

1. `recovery/rc2-snapshot-2026-08-04` is immutable evidence.
2. `recovery/canonical-from-rc2` is the sole working recovery branch.
3. No historical parallel branch is merged directly.
4. Post-RC2 work is inspected as donor slices only.
5. No Kimia write, financial rule, tenant architecture, or critical customer behavior is introduced without accepted ground truth.
6. Kimia remains authoritative for Money, Gold, Coin and Currency.
7. GoldPlatform remains authoritative for Custody/Amanat.

## Superseded recovery branches

The following branches are preserved but are not canonical product branches:

- `recovery/canonical-from-rc1`
- `recovery/rc1-snapshot-2026-08-04`
- `recovery/phase-0-current-state`
- `recovery/baseline-repair-v1`
- `reconstruct/permission-foundation-v1`

No branch is deleted, rebased, force-pushed, or merged as part of this correction.
