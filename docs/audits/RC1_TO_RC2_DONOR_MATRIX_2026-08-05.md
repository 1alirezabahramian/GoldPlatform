# GoldPlatform — RC1 to RC2 Donor Matrix

Date: 2026-08-05
Base: `5dd0653cf49dc171c4310f02257da98784863bf4`
Compared head: `f733ce3bde38ebf5537ff527721febc44858de54`
Status: Donor Classification — No Direct Merge

## Comparison

The RC2-era head is 106 commits ahead of RC1 and directly descends from RC1.

## Strong donor candidates

- Production Docker images and production compose
- Production configuration guard
- Operational health service and command
- Backup and restore scripts and drill workflow
- Security headers and dependency/security gates
- Outbox dispatcher infrastructure
- Kimia read-only hardening and validator
- Performance indexes and query-budget tests

## Conditional donors

- Rate-limit policy: must be checked against final customer/operator route structure.
- Outbox worker runtime: must be checked against final event catalog and handler registry.
- Production environment templates: must be checked for secrets, tenant configuration and deployment target assumptions.
- Performance indexes: must be checked against canonical migrations and query paths.

## Do not forward-port automatically

- Any documentation that calls internal Ledger or Wallet the final financial source of truth.
- Any Kimia write preparation beyond deny-by-default contracts.
- Any route modifications that assume the later Customer/AP/OP contract set.
- Any balance projection exposed as the customer's Money, Gold, Coin or Currency balance.

## Forward-port order

1. Correct financial source-of-truth documentation and boundaries on RC1.
2. Establish a green canonical backend regression on the repaired RC1 base.
3. Forward-port production configuration and health guards.
4. Forward-port security gates.
5. Forward-port backup/restore tooling and execute the drill.
6. Forward-port Kimia read-only hardening.
7. Forward-port Outbox runtime after final event contract selection.
8. Forward-port performance indexes only after canonical query paths are restored.

## Current conclusion

RC1 remains the recovery parent. RC2 is a high-value infrastructure donor, not a rollback target and not a direct merge source.
