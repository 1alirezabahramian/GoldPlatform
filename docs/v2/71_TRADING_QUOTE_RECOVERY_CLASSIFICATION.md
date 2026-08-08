# V2 — Trading / Quote Recovery Classification

Status: DOCUMENTED — RECOVERY CLASSIFICATION
Date: 2026-08-08
Branch: `v2/identity-onboarding-policy-foundation`

## Purpose

Classify the historical Trading/Quote implementation before any V2 runtime integration. This document does not activate pricing, trading, balance mutation, or Kimia Write.

## Current V2 observation

At the current V2 branch, `backend/app/Domain/Trading/Quote/Quote.php` is not present. Therefore the historical Trading/Quote domain is not currently part of this branch's canonical runtime.

## Historical evidence inspected

Historical branch:
- `work/business-engine-stage03-trading-validation`

The branch contains a Trading domain including Quote/Order lifecycle, repositories, validation, idempotency and persistence. It is heavily diverged from the current V2 branch and MUST NOT be merged/cherry-picked wholesale.

### Historical Quote aggregate

The historical `Quote` aggregate provides:
- tenant/company/branch financial scope;
- trace ID;
- correlation ID;
- idempotency key;
- lifecycle states REQUESTED / FROZEN / USED / EXPIRED / CANCELLED;
- expiration enforcement;
- immutable-style state transitions.

However, the inspected aggregate contains **no authoritative price snapshot**, product/asset identity, quantity, commission/tax lines, rounding result, price-source observation, or customer confirmation payload.

### Historical persistence schema

The historical `trading_quotes` table stores scope, trace/correlation/idempotency, status and timestamps, but does not store the accepted Pricing/Quote Ground Truth fields needed for a financially authoritative immutable quote.

Therefore the old schema is insufficient as-is for the recovered V2 Pricing/Quote contract.

## Classification

Historical Trading/Quote lifecycle concepts:
- `REUSE AFTER FIX` as design/code evidence only.

Historical Trading/Quote persistence schema:
- `REBUILD` for the authoritative V2 Quote snapshot requirement.

Historical branch as a whole:
- `HISTORICAL ONLY` / `DUPLICATE CANDIDATE` for selective extraction.
- No blind merge.
- No blind cherry-pick.

## Ground Truth reconciliation

The recovered accepted Pricing/Quote decisions require the authoritative Backend pipeline to produce an immutable Quote containing the final financial result and its provenance. The historical lifecycle implementation predates/does not encode that complete contract.

V2 must preserve useful lifecycle/idempotency/scope patterns while rebuilding the Quote data contract around recovered Ground Truth.

## Balance authority correction

Any historical financial projection/reservation code that treats GoldPlatform ledger/wallet projection as the final customer Money/Gold/Coin/Currency balance is superseded.

Current rule:
- Kimia = final authority for Money / Gold / Coin / Currency.
- GoldPlatform Ledger/Journal/Event/Projection = Audit / Trace / Intent-Result / Settlement / Reconciliation only.
- Custody/Amanat physical = GoldPlatform authority.

## Safe next integration slice

Before runtime code:
1. define the V2 immutable Quote contract from accepted Ground Truth;
2. map each field to Source/Ground Truth;
3. identify unresolved unit/tax/rounding/scope rules explicitly;
4. design persistence without activating Kimia Write;
5. add tests for lifecycle and immutability only after the contract is grounded.

No financial formula or missing rule may be guessed.