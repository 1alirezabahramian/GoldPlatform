# GoldPlatform — Design System Foundation

**Status:** Proposed framework-neutral foundation; no production UI code

**Date:** 2026-08-03

## Goal

Create one consistent Persian, RTL, accessible, and White-label experience for Customer,
Operator, and Admin panels while keeping financial and Kimia complexity in Backend.

This document starts the Frontend foundation without selecting React, Vue, Blade, Inertia,
or another implementation stack. It defines stable experience rules that should survive a
later framework decision.

## Accepted experience invariants

- The default customer experience is Persian and RTL.
- Backend absorbs Kimia/accounting complexity; Frontend presents human concepts.
- Customer UI never exposes raw `AccountId`, Voucher, Action, Debit/Credit, Kimia field
  names, credentials, or connector internals.
- Money shown to the user is in تومان; Kimia Rial-to-Toman conversion is explicit and
  tested in Backend, not recreated independently in JavaScript.
- Money, weight, price, and balance values cross the API as exact decimal strings or an
  equally exact contract. JavaScript floating-point arithmetic is not a financial source
  of truth.
- Negative Money/Gold/Coin/Currency balances are valid domain states and must not be
  silently clamped to zero or hidden.
- Custody/Amanat is a physical asset experience and is not merged into the four financial
  balance cards.
- Branding and enabled modules come from the resolved tenant; components do not contain
  Khalifeh Coin literals as platform defaults.
- Frontend visibility is not authorization. Backend policy decisions remain final.

## Experience language

| Backend/domain concept | Customer-facing concept |
|---|---|
| Money balance | موجودی پولی |
| Gold balance | طلای من |
| Coin balance | سکه‌های من |
| Currency balance | ارزهای من |
| Custody/Amanat | امانات من |
| Ready for pickup | آماده تحویل |
| Buy order | خرید |
| Sell order | فروش |
| Convert money to gold | تبدیل به طلا |
| Convert gold to money | تبدیل به پول |
| Rejected order | رد شده همراه با دلیل قابل‌فهم |
| Expired price/order | منقضی شده؛ نیازمند تأیید قیمت جدید |

Internal codes remain available to authorized staff only when operationally necessary and
must not appear in Customer views.

## Semantic design tokens

The Design System uses semantic tokens. A tenant supplies values; product components use
meaning rather than hard-coded brand colors.

### Brand

```text
brand.primary
brand.on_primary
brand.accent
brand.on_accent
brand.logo
brand.wordmark
```

### Surfaces and text

```text
surface.page
surface.card
surface.elevated
surface.muted
border.default
text.primary
text.secondary
text.muted
text.inverse
```

### Financial and system states

```text
state.success
state.warning
state.danger
state.info
state.pending
state.disabled
price.fresh
price.stale
balance.negative
```

Color is never the only signal. Every state also has text, an icon or shape, and an
accessible label.

### Spacing, shape, and motion

- Spacing follows a consistent compact scale suitable for mobile financial screens.
- Radius, border, and shadow values are tokens so a tenant theme cannot break hierarchy.
- Motion is short, functional, and disabled or reduced when the operating system requests
  reduced motion.
- Financial confirmation never depends on animation completion.

Exact values, typography, and brand palette remain pending visual approval.

## Numeric presentation contract

| Value | Display rule | Calculation rule |
|---|---|---|
| Platform money | تومان with clear thousands grouping | Backend exact decimal/string |
| Kimia money | Never displayed as raw Rial by Customer UI | Converted and tested in Backend |
| Gold weight | Unit `گرم` and approved precision | Backend exact decimal/string |
| Coin | Quantity plus dynamic Kimia-derived name | Never use a hard-coded finite coin list |
| Currency | Dynamic symbol/name from approved projection | Never use a hard-coded finite currency list |
| Negative balance | Preserve minus/state and provide understandable context | Never clamp or apply `abs()` |
| Price age | Show freshness/last update when relevant | Backend supplies timestamp and tradability state |

Open presentation decisions include Persian versus Latin digits, Jalali versus Gregorian
dates, and the customer-facing wording for negative/debt states.

## Required component states

Every data-bearing component must define these states before it is considered complete:

1. Loading/skeleton.
2. Loaded with data.
3. Valid empty state.
4. Recoverable error with retry.
5. Permission denied.
6. Temporarily unavailable integration.
7. Stale price or stale balance projection.
8. Disabled action with a human-readable reason.

Raw stack traces, HTTP bodies, Kimia messages, and credentials never appear in UI. A safe
support/reference code may be shown when Backend supplies one.

## Panel shells

### Customer

- OTP entry and verification.
- Overview of Money, Gold, dynamic Coin, and dynamic Currency balances.
- Custody summary kept visually separate.
- Prices and product availability.
- Buy/Sell/Convert flows only when Backend marks the action available.
- Orders, rejection reasons, expiry, settlement, and delivery state.

### Operator

- Pending orders and operational queues.
- Approval/rejection with mandatory reason where the business flow requires it.
- Customer search without exposing unnecessary personal data.
- Custody readiness and delivery handoff.
- Integration failure state and safe retry/escalation cues.

### Tenant Admin

- Customer groups and limits.
- Product visibility and pricing configuration.
- Operator permissions.
- Tenant branding, domains, branches, modules, and connector health only after the
  corresponding Backend contracts are accepted.
- Audit and reconciliation views.

### Platform Super Admin

This is a separate future shell only if ADR-026 confirms the role. It must not be hidden
inside Tenant Admin navigation. Cross-tenant actions require explicit context and audit.

## White-label bootstrap boundary

Before rendering a branded public page, Frontend needs a safe tenant bootstrap contract
from Backend. It may include:

- public brand name and approved logo assets;
- semantic theme values;
- locale and public support contact;
- enabled customer-facing modules;
- maintenance or availability state.

It must exclude:

- Kimia/SMS/KYC credentials;
- raw connector or accounting identifiers;
- internal license enforcement data;
- other tenants or domains;
- financial rules that Backend has not accepted.

The endpoint name and exact schema remain pending ADR-026 and API-contract work.

## RTL and accessibility baseline

- Document direction is RTL; isolated technical identifiers can use LTR direction without
  flipping the full layout.
- Keyboard focus is visible and navigation order follows visual reading order.
- Inputs have persistent labels; placeholder text is not the only label.
- Validation errors identify the field and the corrective action.
- Tap targets are at least 44 by 44 CSS pixels where practical.
- Text and interactive elements target WCAG AA contrast.
- Status is never communicated through color alone.
- Confirmation dialogs name the action, asset, amount/weight, and consequence plainly.

## Responsive baseline

- Customer flows are mobile-first.
- Operator and Admin shells support desktop tables but preserve complete actions on tablet.
- Important financial values do not truncate silently.
- Dense tables provide a card/list alternative on narrow screens.
- Primary buy/sell actions remain distinguishable and are never positioned so closely that
  accidental activation is likely.

## Implementation sequence after stack approval

1. Publish semantic tokens and tenant theme adapter.
2. Build typography, icon, button, input, badge, card, table/list, dialog, and notification
   primitives.
3. Build shared Loading/Empty/Error/Stale/Disabled states.
4. Build Auth shell.
5. Build Customer asset overview with mocked exact-string data.
6. Build Operator and Tenant Admin navigation shells.
7. Connect only to accepted Backend API contracts.
8. Add RTL visual regression and accessibility checks.

## Decisions still required

1. Frontend implementation stack.
2. Khalifeh Coin visual palette and approved logo variants.
3. Persian typeface and fallback fonts.
4. Persian or Latin digit presentation.
5. Jalali/Gregorian date presentation and display timezone.
6. Light-only first release or light/dark themes.

These choices block visual implementation, not the architecture rules recorded above.
