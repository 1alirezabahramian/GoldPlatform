# Phase 03–04 — Admin Experience and Design System

Status: IMPLEMENTED — CI PENDING

## Base

- Canonical branch: `recovery/rc2-product-rebuild`
- Base SHA: `415728a67be5af6d31b52f996c68f0d28bbc029e`
- Working branch: `ux/admin-design-system-completion`

## Phase 03 — Admin Experience

The Admin workspace now renders the accepted read-only Backend resources:

- `GET /api/admin/audit-logs`
- `GET /api/admin/outbox`

The workspace includes loading, empty, forbidden and error states; Audit and Outbox summaries; responsive tables; Persian dates; and explicit safety boundaries. No financial policy update, balance mutation, settlement action or Kimia operation is enabled.

## Phase 04 — Design System

The existing `shared-ui/styles/tokens.css` was preserved as the accepted token source. Creating a second token system was classified as a Duplicate Candidate and rejected.

A shared component-pattern layer was added at `shared-ui/styles/components.css` and loaded by both Customer and Admin/Operator Nuxt applications. It provides reusable patterns for:

- page headers
- buttons
- badges and semantic states
- state panels
- data lists
- responsive tables
- metric cards

Semantic state colors remain fixed and are not tenant-overridable by this change. Existing focus, touch-target and reduced-motion rules remain authoritative.

## Safety and architecture

- Kimia remains the final source of truth for Money, Gold, Coin and Currency.
- Custody remains separate.
- No API, Permission, Tenant, Company, Branch, Migration or Backend response changed.
- No financial calculation or Rial/Toman conversion was added.
- No frontend write operation was added.

## Validation

Tests added:

- Admin endpoint contract guard
- Admin resource field guard
- Shared component-pattern availability
- Touch-target accessibility guard
- Financial mutation and calculation guard

Execution state at commit time: WRITTEN — NOT EXECUTED.

## Remaining risk

Visual QA on real devices and screenshot comparison are not part of this PR. Admin user/role management, tenant/company architecture, branding configuration and policy-write workflows require separate verified Backend contracts and must not be inferred from UI requirements.
