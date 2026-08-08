# GoldPlatform V2 — Platform Customer Level vs Kimia Account Group

Status: OWNER-CONFIRMED GROUND TRUTH + VERIFIED KIMIA CONTRACT SEPARATION
Scope: Customer policy / Tenant / Kimia terminology
Date: 2026-08-08

## Decision

GoldPlatform Customer Level/Group and Kimia Account Group are two different domain concepts and MUST NOT be treated as equivalent, synchronized by meaning, or reused as one model.

## GoldPlatform Customer Level / Group

This is a GoldPlatform/Tenant-owned business policy assigned to a customer by authorized Tenant management.

Its purpose is to increase, decrease, enable, disable, or remove platform capabilities and trading restrictions for that customer.

Owner-confirmed examples include:

- a normal / level-1 customer may trade only when the required money/gold balance or allowed credit condition is satisfied;
- a special / level-2 customer may be permitted to trade without the same pre-existing balance restriction;
- a VIP / level-3 customer may have fewer or no platform trading restrictions where explicitly configured;
- pricing/commission policy may differ by customer level;
- minimum and maximum buy/sell quantity or amount may differ by product and level;
- other capabilities/restrictions may be attached to the platform customer level.

The labels `normal`, `special`, `VIP`, `level 1`, `level 2`, and `level 3` are examples of presentation/business naming. They are not authorization to hard-code financial values or limits.

Exact commission, pricing, credit, balance requirement, freeze/lock, minimum/maximum and other financial values require their own confirmed Ground Truth and Tenant configuration before runtime activation.

## Existing GoldPlatform foundation

Current code already contains a candidate foundation:

- `User.group_id`
- `UserGroup`
- `CustomerTradingPolicy`

`UserGroup` currently contains fields such as buy/sell commission, discount, priority and active state.

`CustomerTradingPolicy` currently contains fields such as balance requirement, negative-balance allowance, asset lock, gold/coin/money limits, credit limit, order limits and delivery limits.

Classification:

- existing Platform group/policy foundation: `REUSE AFTER VALIDATION`
- a new parallel Customer Level system: `DUPLICATE CANDIDATE`

The existing models must not be considered financially correct merely because fields exist. Every financial field must be compared against owner-confirmed rules before activation.

## Kimia Account Type / Account Group

Kimia has a separate accounting-domain concept.

Verified project Ground Truth from Kimia Swagger / recorded API contract:

```http
GET /api/account/groups?accountType={type}
```

The query parameter for this endpoint is exactly `accountType`.

The response contract is an array of Kimia account-group records with fields including:

```text
Id
Name
AccountType
```

Known Swagger AccountType values include:

- 1 = Wholesale / بنکداری
- 3 = Retail / تکفروشی
- 5 = Capital and withdrawal / سرمایه و برداشت
- 6 = Bank / بانک
- 8 = Internal account / حساب داخلی
- 9 = Melt / ذوب
- 10 = Amanat / امانات
- 11 = Expense / هزینه
- 12 = Employee / کارمندان

Important endpoint distinction already established in project Ground Truth:

```text
GET /api/account          -> filter parameter: Type
GET /api/account/groups   -> filter parameter: accountType
```

Kimia Account Groups are read from Kimia. They must not be guessed or generated from a fixed GoldPlatform customer-level list.

## Hard separation rule

The following mapping is prohibited unless future explicit Ground Truth defines a specific integration need:

```text
GoldPlatform Customer Level == Kimia AccountGroup
```

Likewise, a customer being VIP/special/normal in GoldPlatform does not by itself change the customer's Kimia AccountType or AccountGroup.

GoldPlatform customer-level assignment is platform policy. Kimia AccountType/AccountGroup is Kimia accounting classification.

If a future workflow requires changing Kimia AccountGroup as a side effect, that would be a separate Kimia Write decision requiring verified endpoint/payload/response/idempotency/reconciliation Ground Truth.

## Naming rule

To prevent domain drift, V2 documentation/code/API should prefer explicit terminology:

- `Platform Customer Level` or `Platform Customer Group` for GoldPlatform policy;
- `Kimia AccountType` for Kimia account type;
- `Kimia AccountGroup` for Kimia account group.

The legacy class name `UserGroup` may remain temporarily while reuse/refactor is evaluated. Its name must not be used as evidence that it represents a Kimia group.

## Relationship to Customer Access Status

Platform Customer Level is also separate from runtime Customer Access Status.

Examples:

```text
Customer Access Status: Active / Limited / Suspended / Blocked
Platform Customer Level: Normal / Special / VIP (or Tenant-defined labels)
Kimia classification: AccountType / AccountGroup
```

Changing access status must not silently change Platform Customer Level.
Changing Platform Customer Level must not silently change Kimia AccountGroup.
Changing either must not create an independent Money/Gold/Coin/Currency balance in GoldPlatform.

## Authority and audit

- Platform Customer Level is assigned/changed by authorized Tenant management.
- The assignment must be Tenant-scoped.
- Cross-Tenant changes must fail closed.
- Changes must be auditable.
- Financial effects of a level change must come from validated platform policy, not from client-side calculations.

## Next validation gate

Before enabling Platform Customer Level policies in runtime:

1. inventory all uses of `User.group_id`, `UserGroup`, and `CustomerTradingPolicy`;
2. compare each existing financial field with owner-confirmed rules and prior accepted project evidence;
3. identify fields that are safe to reuse, need refactor, or must remain inactive;
4. verify product-specific min/max representation before assuming group-wide fields are sufficient;
5. ensure Backend is the only authority applying the effective trading policy;
6. ensure Frontend only displays the effective policy/eligibility returned by Backend;
7. keep Kimia AccountType/AccountGroup integration separate.
