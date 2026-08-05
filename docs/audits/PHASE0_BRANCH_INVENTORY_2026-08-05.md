# GoldPlatform — Phase 0 Branch Inventory

> Status: In Progress
>
> Evidence date: 2026-08-05
>
> Branch: `recovery/canonical-inventory`
>
> Parent evidence branch: `recovery/phase-0-current-state`

## 1. Purpose

This document records live branch evidence before any canonical reconstruction. It does not approve any branch for direct merge.

## 2. Recovery branches

- `recovery/phase-0-current-state`
- `recovery/baseline-repair-v1`
- `recovery/canonical-inventory`

Classification:

- `recovery/phase-0-current-state`: recovery evidence and planning.
- `recovery/baseline-repair-v1`: experimental repair donor; not canonical and not approved for merge.
- `recovery/canonical-inventory`: inventory-only branch. No product feature may be added here.

## 3. Business Engine branches

- `work/business-engine-stage00`
- `work/business-engine-stage01-kimia-read`
- `work/business-engine-stage02-financial-kernel`
- `work/business-engine-stage03-trading-validation`

Live status summary:

- Stage 00–02 have merged evidence on `main`.
- Stage 03 remains open and stacked on the historical Stage 02 branch.
- Stage 03 is a donor requiring dependency-closed reconstruction, not direct merge.

## 4. Product-line feature branches

- `feature/goldplatform-developer-mcp`
- `feature/local-agent-runner`

Classification:

- `feature/goldplatform-developer-mcp`: historical product donor containing many merged Customer, Custody, Delivery, Settlement, Security and Production capabilities. It is not a safe direct merge source for `main` because its history diverged.
- `feature/local-agent-runner`: tooling and historical backend donor. It must not determine product architecture.

## 5. Customer branches

Twenty-four live branches matching the Customer track were found:

- `work/customer-panel-contract-foundation`
- `work/customer-assets-read-contract`
- `work/customer-order-status-contract`
- `work/customer-custody-delivery-contract`
- `work/customer-profile-read-contract`
- `work/customer-activity-timeline-contract`
- `work/customer-bootstrap-contract`
- `work/customer-api-error-contract`
- `work/customer-openapi-foundation`
- `work/customer-pagination-contract`
- `work/customer-status-filter-contract`
- `work/customer-sort-contract`
- `work/customer-date-filter-contract`
- `work/customer-no-store-cache-contract`
- `work/customer-trace-header-contract`
- `work/customer-request-id-header-contract`
- `work/customer-api-readiness-gate`
- `work/customer-contract-regression-gate`
- `work/customer-panel-final-regression`
- `work/customer-panel-phase-closure`
- `work/customer-cp02-read-resources`
- `work/customer-frontend-fe01-foundation`
- `work/customer-frontend-fe02-nuxt-shell`
- `work/admin-operator-ap05-customer-groups-read`

Classification:

- The merged CP chain is protected donor evidence.
- `work/customer-cp02-read-resources` is a duplicate/dirty donor and must not be merged directly.
- FE-01 and FE-02 are frontend donors and require install, typecheck, build and E2E before selection.

## 6. Admin and Operator branches

Twenty-six live branches matching Admin/Operator were found:

- `work/admin-operator-ap00-discovery`
- `work/admin-operator-ap01-foundation`
- `work/admin-operator-ap02-safe-dashboards`
- `work/admin-operator-ap03-safe-operational-queues`
- `work/admin-operator-ap04-user-read-foundation`
- `work/admin-operator-ap05-customer-groups-read`
- `work/admin-operator-ap06-policy-approval-foundation`
- `work/admin-operator-ap07-roles-permissions-read`
- `work/admin-operator-ap08-order-read-foundation`
- `work/admin-operator-ap09-delivery-custody-read`
- `work/admin-operator-ap10-settlement-read`
- `work/admin-operator-ap11-kimia-read`
- `work/admin-operator-ap12-system-health-read`
- `work/admin-operator-ap13-product-pricing-read`
- `work/admin-operator-ap14-branch-read`
- `work/admin-operator-ap15-white-label-discovery`
- `work/admin-operator-ap16-notification-read`
- `work/admin-operator-ap17-reports-export-contract`
- `work/admin-operator-ap18-safe-operator-actions`
- `work/admin-operator-ap19-settlement-sensitive-actions`
- `work/admin-operator-ap20-frontend-foundation`
- `work/admin-operator-foundation`
- `work/admin-operator-op02-session-bootstrap`
- `work/admin-operator-op03-application-shell`
- `work/admin-operator-op04-operational-dashboard`
- `work/admin-operator-op05-operator-queues`

Classification:

- AP and OP are parallel donor tracks.
- No AP or OP branch is approved for direct merge.
- Permission, route and response contracts must be compared slice-by-slice.
- AP-20 and OP-03+ are competing frontend foundations.

## 7. Recovery conclusions from branch inventory

1. Evidence is still present; branches have not been destroyed.
2. The repository contains at least four concurrent development lines: `main`, historical product, Business Stage 03, and AP/OP/Frontend tracks.
3. No existing branch is accepted as the complete canonical product baseline.
4. Direct merge, broad retargeting, blind cherry-pick and shared-history rebase remain prohibited.
5. The next evidence unit is a path-level file matrix between `main` and `feature/goldplatform-developer-mcp`.

## 8. Next fixed actions

- Inventory top-level and backend domain paths on `main`.
- Inventory the same paths on `feature/goldplatform-developer-mcp`.
- Compare Auth/User, Kimia, Wallet/Ledger, Order, Settlement, Custody and Delivery first.
- Record each path as `KEEP — VERIFIED`, `HEALTHY DONOR`, `DUPLICATE`, `BROKEN`, `ARCHITECTURE DRIFT`, `HISTORICAL` or `NEEDS GROUND TRUTH`.

Current status: `BRANCH INVENTORY RECORDED — FILE INVENTORY IN PROGRESS`.
