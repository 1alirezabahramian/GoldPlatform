# GoldPlatform V2 — Customer Access Status & Level Ground Truth

Status: OWNER-CONFIRMED GROUND TRUTH + CURRENT-SCHEMA INVENTORY
Scope: Identity / Onboarding / Customer lifecycle continuation on PR #201
Date: 2026-08-08

## Owner-confirmed business rule

Customer approval is not permanent authority.

After a customer has been approved and activated, authorized Tenant management may later:

- limit the customer;
- suspend the customer;
- block the customer;
- restore/reactivate the customer when permitted;
- change the customer's level/group according to valid Tenant rules.

These post-onboarding controls are separate from initial Jibit identity verification and initial Admin approval.

## Lifecycle separation

GoldPlatform V2 must keep these concepts separate:

1. Identity Verification
   - Did the external identity check succeed/fail?

2. Initial Onboarding / Admin Review
   - Was the registration approved or rejected by Tenant management?

3. Current Customer Access State
   - What is the customer's current permission to use the platform after onboarding?

4. Customer Level / Group
   - Which validated business/trading policy group applies to the customer?

Changing one dimension must not silently rewrite another dimension.

Example: blocking a customer must not alter their Kimia balance and must not implicitly rewrite their customer level.

## Current schema inventory

### User.is_active

The current User model exposes a boolean `is_active`.

Classification: `REUSE AFTER FIX / INSUFFICIENT AS FULL ACCESS STATE`.

It can express active vs not-active only. It cannot faithfully distinguish limited, suspended and blocked, nor preserve reason/actor/timing/history by itself.

Do not overload this single boolean with undocumented meanings.

### User.group_id / UserGroup

The current User model has `group_id -> UserGroup`.

UserGroup currently contains business/financial fields including buy/sell commission, discount, priority and active state.

Classification: `REUSE AFTER VALIDATION`.

It is a strong duplicate candidate for customer level/group and a parallel level system must not be created before this model and its historical rules are validated.

However, because UserGroup is financially coupled, changing group/level is a financially sensitive action. No default group mapping, commission implication, credit implication, freeze/limit implication or automatic level transition may be guessed.

### CustomerTradingPolicy

An existing CustomerTradingPolicy model is linked to UserGroup and contains financially sensitive fields including available-balance requirements, negative-balance allowance, lock minutes, gold/coin/money caps, credit limit and order limits.

Classification: `HISTORICAL/EXISTING FOUNDATION — REUSE ONLY AFTER FINANCIAL GROUND TRUTH VALIDATION`.

It must not be used to infer or activate unverified financial behavior.

## Access-state conceptual contract

Owner-confirmed post-onboarding states include at least these business meanings:

- Active: normal authorized use according to current permissions/level.
- Limited: selected platform capabilities are restricted while some non-sensitive access may remain.
- Suspended: temporary operational suspension; duration may be finite or open-ended according to the management action.
- Blocked: access/use is blocked until an authorized management decision changes it.

These are conceptual business meanings. Exact persisted enum/table/columns are not authorized yet.

Before adding a migration, inspect migrations, restriction/permission foundations, audit/event structures, customer policy models and historical accepted decisions for a reusable representation.

## Backend authority rule

Frontend hiding or disabling actions is never the authority boundary.

Sensitive operations must enforce the current access state in Backend and fail closed.

A blocked/suspended/limited customer must not gain permission merely by calling an API directly.

## Financial source-of-truth boundary

Customer access control does not change final financial balances.

Money / Gold / Coin / Currency remain final-authority data from Kimia.

Blocking, limiting or suspending a customer in GoldPlatform must not manufacture, zero, transfer or rewrite those balances.

Physical Custody remains separate and GoldPlatform-owned according to existing project ground truth.

## Management action requirements

Any management change to customer access state must be traceable/auditable and should capture, at minimum in the final representation:

- Tenant;
- target customer;
- previous state;
- new state;
- acting authorized Staff/Admin;
- timestamp;
- internal reason/note where applicable;
- optional effective-until/expiry when the chosen state supports temporary suspension/limitation.

Customer-facing messages must not automatically expose internal risk/rejection notes.

## Customer level/group changes

Tenant management may change a customer's level/group after activation.

This is owner-confirmed capability, but implementation is financially sensitive because the existing group/policy models contain commission, limits, credit and lock fields.

Therefore:

- do not create a parallel customer-level model before validating UserGroup;
- do not activate existing commission/credit/freeze/limit fields merely because they exist in code;
- do not infer automatic transitions between groups;
- every level change must be audited;
- the exact financial consequences require accepted Ground Truth before runtime activation.

Classification: `CAPABILITY CONFIRMED — IMPLEMENTATION/POLICY EFFECTS REQUIRE FINANCIAL GROUND TRUTH VALIDATION`.

## Relation to onboarding

Initial onboarding may end in Active or Rejected/Blocked according to the confirmed approval flow.

Later customer management is a separate lifecycle and must support post-activation controls without reopening or falsifying the historical identity-verification result.

A customer can therefore conceptually remain identity-verified while later becoming Limited, Suspended or Blocked by Tenant management.

## Current implementation classification

- User.is_active: `REUSE AFTER FIX` as a compatibility/basic active flag, not sufficient as the whole status model.
- User.group_id / UserGroup: `DUPLICATE CANDIDATE FOR CUSTOMER LEVEL — REUSE AFTER VALIDATION`.
- CustomerTradingPolicy: `REUSE ONLY AFTER FINANCIAL GROUND TRUTH VALIDATION`.
- Multi-state Customer Access model: `NOT IMPLEMENTED` until schema/history inventory proves the minimal non-duplicate representation.
- Post-activation management capability: `OWNER-CONFIRMED GROUND TRUTH`.

## Next safe gate

1. inspect migrations/history for customer restriction/status/ban/suspension tables or fields beyond User.is_active;
2. inspect authorization and order/trade entrypoints for existing active/restriction guards;
3. inspect UserGroup and CustomerTradingPolicy provenance before using them for level changes;
4. design the minimal access-state representation only if no canonical equivalent exists;
5. add fail-closed backend tests for Limited/Suspended/Blocked before exposing management API;
6. keep level-change implementation separate from access-state implementation whenever financial ground truth is unresolved.
