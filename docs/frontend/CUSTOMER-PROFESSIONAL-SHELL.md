# Customer Professional Shell

Status: Implemented — CI Pending

## Scope

This stage upgrades the existing Customer Nuxt shell without changing routes, API contracts, financial rules, Kimia behavior, permissions, or tenant architecture.

Implemented:

- Trusted Financial Minimal application shell.
- Accessible skip navigation.
- Clear brand lockup and secure-connection indicator.
- Mobile-first bottom navigation with text labels.
- Professional loading, empty, unavailable, error, and ready states.
- Explicit preservation of unavailable financial information; unavailable values are never shown as zero.
- Retry action for temporary and generic read failures.
- Responsive behavior for small mobile widths.
- Reduced-motion safeguards.

## Contract boundary

The current Customer endpoints are consumed through the accepted Customer API composable. No dashboard, asset, order, custody, or profile response schema was invented in this stage. Data-specific cards and tables remain blocked until their real response contracts are inspected and mapped.

## Validation

- Contract tests: WRITTEN — NOT EXECUTED
- Typecheck: NOT EXECUTED
- Production build: NOT EXECUTED
- Browser E2E: NOT EXECUTED
- Screenshot visual audit: NOT EXECUTED
- Figma: NOT EXECUTED
