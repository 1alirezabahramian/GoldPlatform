# GoldPlatform Design System Foundation

Status: IMPLEMENTED — CI PENDING

## Canonical direction

GoldPlatform uses **Trusted Financial Minimal** as the primary visual direction, with **Modern Persian Enterprise** density and workflow patterns for Operator and Admin surfaces.

Gold is a restrained accent. It is not the dominant page background and does not replace semantic success, warning, danger, information, stale-data, or security meanings.

## Shared foundation

The shared token source is:

`shared-ui/styles/tokens.css`

It provides:

- brand and neutral colors;
- protected semantic status colors;
- typography and font fallbacks;
- spacing, radius, shadow and layout scales;
- minimum touch target;
- focus-visible treatment;
- reduced-motion behavior;
- motion durations and easing.

Both Nuxt applications load the same token file before their local structural CSS.

## Product personality

### Customer

Calm, clear, mobile-first, low jargon, and confidence-oriented. Financial data remains read-only presentation of accepted backend contracts.

### Operator

Fast, compact, keyboard-friendly, queue-oriented, and designed to reduce operational mistakes.

### Admin

Enterprise, audit-friendly, data-oriented, and suitable for desktop and tablet workflows.

## Safety boundaries

- No financial calculation was added to Frontend.
- No Rial/Toman, weight, commission, price, or balance conversion was added.
- No Kimia route, identifier, or write behavior was exposed.
- No API route or payload was invented.
- No Tenant, Company, or Branch architecture was introduced.
- Semantic security and financial status meanings are not tenant-overridable.
- Existing Customer, Operator, and Admin routes remain unchanged.

## Current scope

This stage establishes foundations only. It does not claim a complete component library, Figma library, visual regression suite, PWA closure, native application, or final production UI.

## Next stages

1. Component primitives and state patterns.
2. Customer reference screens.
3. Operator workspace patterns.
4. Admin data-grid and audit patterns.
5. Visual, responsive, accessibility and browser validation.
6. Figma handoff after visual direction validation.
