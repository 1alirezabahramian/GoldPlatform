# GoldPlatform V2 — Platform Customer Policy Runtime Gap

Status: INVENTORY / GAP CLASSIFICATION
Scope: Platform Customer Level / Trading Policy
Date: 2026-08-08

## Verified current foundation

The current branch contains three distinct concepts:

1. `User.group_id -> UserGroup`: Platform-side customer grouping candidate.
2. `CustomerTradingPolicy.user_group_id`: Platform-side trading policy candidate.
3. `AccountGroup`: Kimia-synchronized accounting group (`kimia_id`, `account_type`, `name`, `synced_at`).

These must remain separate.

## Kimia separation verified in current code

`KimiaAccountRepository::groups()` calls:

```http
GET /api/account/groups?accountType=...
```

while account listing uses:

```http
GET /api/account?Type=...
```

`KimiaSyncGroups` synchronizes Kimia groups into `AccountGroup` using the returned `Id`, `Name`, and `AccountType` fields.

Therefore current code already has a dedicated Kimia AccountGroup model and synchronization path. `UserGroup` must not be repurposed as Kimia AccountGroup.

## Platform policy fields found

`UserGroup` currently exposes:

- title
- buy_commission
- sell_commission
- discount_percent
- priority
- is_active
- description

`CustomerTradingPolicy` currently exposes:

- user_group_id
- requires_available_balance
- allow_negative_balance
- asset_lock_minutes
- max_gold_weight
- max_coin_quantity
- max_money_amount
- credit_limit
- min_order_amount
- max_order_amount
- max_delivery_items
- is_active
- metadata

These fields are evidence of an earlier Platform Customer Policy foundation. They are not proof that the current runtime applies the owner-confirmed V2 policy correctly.

## Runtime gap found

The current `OrderService::create()` validates tradability and positive quantity/unit price, then accepts caller-provided commission/total-price data and creates a pending order.

In the inspected runtime path, no effective `UserGroup` / `CustomerTradingPolicy` enforcement is visible before order creation.

Classification:

- Platform Customer Level data foundation: `REUSE AFTER VALIDATION`
- CustomerTradingPolicy data foundation: `REUSE AFTER VALIDATION`
- Kimia AccountGroup sync/model: `REUSE AS-IS` for the separation boundary inspected here
- effective Platform Customer Policy enforcement in inspected order-creation path: `NOT IMPLEMENTED / GAP`

## Financial safety stop

Do not wire these policy fields into trading yet.

Several fields directly affect financial behavior, including:

- commission / discount;
- available-balance requirements;
- negative-balance/credit behavior;
- credit limit;
- asset lock;
- order limits.

The owner has confirmed the capability model conceptually, but exact effective values and precedence must be validated before runtime activation.

No value is to be inferred from example labels such as Normal / Special / VIP or Level 1 / 2 / 3.

## Product-specific limit gap

The owner-confirmed rule includes minimum/maximum buy/sell limits per product/customer level.

The currently inspected `CustomerTradingPolicy` has generic fields such as `min_order_amount`, `max_order_amount`, `max_gold_weight`, and `max_coin_quantity`, but no verified product-specific policy relation was established in this inventory.

Therefore generic group-wide limits must not be assumed sufficient.

Classification: `GAP — REQUIRES DESIGN AGAINST EXISTING PRODUCT/ASSET MODEL`.

## Commission authority gap

The inspected `OrderService::create()` currently accepts `commission` from input data.

For the target V2 architecture, Frontend/client input must not become the authority for financial commission calculation. The effective commission/pricing policy must be calculated/validated by Backend from grounded Tenant/customer/product policy.

This document does not change commission behavior because exact pricing/commission Ground Truth must be validated first.

Classification: `REUSE AFTER FIX / FINANCIAL GATE`.

## Next safe steps

Before implementation:

1. inventory all order/trade entry points and determine where commission, credit and balance eligibility are currently calculated;
2. inspect existing product/asset limit models before creating a new level-product policy table;
3. compare Project Memory / accepted owner rules for normal/special/VIP behavior with current fields;
4. establish precedence among Tenant defaults, customer level, product policy and per-customer overrides if such representations already exist;
5. only then design an effective-policy resolver in Backend;
6. add tests proving the client cannot bypass level restrictions by supplying financial values;
7. keep Kimia AccountGroup entirely outside this resolver unless a separately grounded Kimia integration requires it.
