# GoldPlatform V2 — Platform Customer Policy Entrypoint Inventory

Status: INVENTORY / SAFETY CLASSIFICATION
Scope: Customer Level / Trading Policy runtime entry points
Date: 2026-08-08

## Summary

The current branch contains Platform Customer Policy data models and an Admin read endpoint, but the inspected customer order entry point does not enforce an effective customer-level policy.

Financial policy mutation is already fail-closed in Backoffice, which is the correct safe posture until Ground Truth is completed.

## Customer order entry point

Current route:

```http
POST /orders
```

Current route characteristics:

- requires Sanctum authentication;
- uses customer throttle group;
- uses idempotency for `order.create`;
- is not currently nested under the `role:customer` route group;
- is not currently protected by `tenant.resolve` + `tenant.user-match` on this route;
- delegates to `OrderController::store()` and then `OrderService::create()`.

This is an existing runtime boundary that must be reviewed before V2 customer-level policy enforcement is enabled.

## Request authority issue

`StoreOrderRequest` currently accepts:

- `type`
- `gold_weight`
- `gold_price`
- optional `commission`

and prohibits client `user_id`, `status`, and `total_price`.

`OrderController` correctly overwrites `user_id` from the authenticated user.

However, `OrderService` currently uses caller-provided `gold_price` / `unit_price` and `commission` when calculating/storing the order.

Classification:

- authenticated `user_id` ownership: existing safety control;
- client financial price/commission authority: `REUSE AFTER FIX / FINANCIAL GATE`;
- customer-level policy enforcement: `NOT IMPLEMENTED` in this inspected path;
- Tenant enforcement on this legacy `/orders` route: `GAP / REQUIRES ALIGNMENT`.

No financial calculation change is authorized by this inventory alone.

## Backoffice policy endpoint

Current Admin routes expose:

```http
GET /admin/customer-policies
PUT /admin/customer-policies/{policy}
```

The mutation endpoint currently fails closed with HTTP 503 and code:

```text
FINANCIAL_POLICY_GROUND_TRUTH_REQUIRED
```

This explicitly prevents Admin from changing financial policy until rules and Kimia authority boundaries are verified.

Classification: `REUSE AS-IS` as a temporary safety gate.

## Important domain separation

Three independent dimensions remain:

1. Customer Access Status — whether the customer may currently use all/some/no protected platform operations.
2. Platform Customer Level/Group — Tenant-assigned capability and trading-policy profile.
3. Kimia AccountType/AccountGroup — Kimia accounting classification read/synchronized from Kimia.

None may silently mutate another.

## Safe implementation order

Before enabling Customer Level enforcement on orders:

1. align the customer order route with canonical Tenant resolution and customer role boundaries;
2. identify canonical quote/pricing authority so price and commission are not trusted from the client;
3. identify current Kimia balance-read service used for eligibility checks;
4. validate Platform Customer Level financial rules and precedence;
5. design/read effective policy in Backend;
6. apply access-status and effective-policy authorization before order creation;
7. test bypass attempts, cross-Tenant attempts, inactive/suspended/blocked users, and level limits;
8. keep financial policy mutation fail-closed until the corresponding Ground Truth is accepted.

## No-change statement

This inventory intentionally makes no runtime financial changes, no Kimia Write, no commission formula change, no credit-limit activation, and no Customer Level default assignment.
