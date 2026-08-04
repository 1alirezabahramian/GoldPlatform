# ADR — Trading Quote Lifecycle

Status: Accepted
Date: 2026-08-04

## Context

GoldPlatform requires a tenant-scoped quote contract before an order can be created. Pricing formulas, spreads, commissions and freeze durations are not authorized in the current sources and must not be guessed.

## Decision

1. Quote lifecycle is: Requested -> Frozen -> Used, with Expired and Cancelled terminal outcomes.
2. A frozen quote must receive its exact expiration timestamp from an external approved policy or caller.
3. No default freeze duration is embedded in the domain.
4. A quote cannot be used at or after its expiration timestamp.
5. Quote repositories require FinancialScope for every read and write.
6. Quote identity is UUID-based and separate from internal database keys.
7. Quote carries TraceId, CorrelationId and IdempotencyKey.
8. Quote creation, freezing and expiry do not mutate Ledger, Wallet, Settlement or Kimia.

## Explicitly excluded

- Pricing formula
- Price source mapping
- Spread and commission
- Customer group limits
- Wallet availability checks
- Kimia write mapping
- Database schema for quotes

## Consequences

The Quote aggregate can be used by the next Order Lifecycle stage without introducing unapproved financial rules. Persistence remains behind a tenant-scoped repository contract.
