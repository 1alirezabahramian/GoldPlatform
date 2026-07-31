# GoldPlatform — Project Phases and Delivery Roadmap

**Document status:** Accepted baseline  
**Last updated:** 2026-07-31  
**Purpose:** Define the correct order of project execution and prevent jumping between modules without completing dependencies.

## Phase 0 — Discovery and Domain Capture

**Status:** Mostly complete; remains a living activity

### Completed

- Initial business analysis
- Gold, coin, currency, wallet, custody, and delivery concepts documented
- Kimia terminology and real examples collected
- Customer groups and credit-trading concepts defined
- Core rule: complex backend, simple frontend
- Source-of-truth and no-guessing policy established

### Remaining

- Resolve conflicting older rules
- Confirm uncertain Kimia mappings through real API behavior
- Convert accepted decisions into ADRs

### Exit criteria

- All critical business terms have one accepted definition
- Unknown behavior is explicitly marked as unknown
- Conflicting assumptions are superseded or rejected

---

## Phase 1 — Infrastructure and Backend Foundation

**Status:** Substantially complete

### Completed

- Docker environment
- Nginx
- PHP-FPM
- MySQL
- Redis
- Laravel backend
- Git repository
- Authentication foundation
- Sanctum
- OTP tables
- User model
- Wallet foundation
- Spatie Permission
- Queue/session/cache foundations

### Remaining

- Automated CI health checks
- Environment validation
- Production logging and monitoring plan
- Remove accidental or duplicate files only after verification

### Exit criteria

- Clean installation works from documentation
- Containers start successfully
- Migrations and tests run successfully
- Health check is reproducible

---

## Phase 2 — Product Foundation and Multi-Tenancy

**Status:** Architecture accepted; implementation not started

### Scope

- Tenant model
- Tenant-aware users, customers, products, orders, wallets, and settings
- Tenant scoping middleware or global scopes
- Tenant branding
- Tenant feature flags
- Subscription/licensing foundations
- First pilot tenant: Khalifeh Coin

### Required decisions

- Shared database with `tenant_id` versus database-per-tenant
- Tenant resolution by domain, subdomain, header, or internal context
- Super-admin access boundaries
- Cross-tenant reporting rules

### Recommended current decision

Start with a shared database and mandatory `tenant_id` isolation, while designing service boundaries so dedicated deployments remain possible.

### Exit criteria

- Tenant isolation covered by automated tests
- No tenant can read or mutate another tenant’s data
- Khalifeh settings exist as tenant configuration, not hardcoded logic

---

## Phase 3 — Kimia Connector Stabilization

**Status:** In progress; current technical priority

### Completed

- Swagger reviewed
- Runtime logs reviewed
- Main endpoint families identified
- Account type values documented
- Account list parameter correction identified: `Type=3`
- Product, coin, currency, balance, voucher, wallet, and report areas mapped at audit level
- Adapter boundary accepted

### Required work

1. Identify the one active Kimia HTTP client.
2. Identify the one active account synchronization flow.
3. Capture raw successful account response.
4. Fix and verify account synchronization.
5. Capture and verify account groups.
6. Capture and verify balance semantics.
7. Extract all remaining Swagger schemas.
8. Implement typed DTOs and mappers.
9. Implement exception categories.
10. Implement safe idempotency for mutations.
11. Build MockConnector.
12. Add contract and integration tests.

### Exit criteria

- Account and group synchronization works reliably
- Balance mapping is verified with real examples
- Kimia outages do not corrupt local state
- No raw Kimia codes leak into the core domain
- Duplicate classes can be safely removed after usage verification

---

## Phase 4 — Catalog and Pricing Engine

**Status:** Not started as a production module

### Scope

- Dynamic categories and products
- Financial and physical product separation
- Tenant-specific product visibility
- Kimia identifiers and mappings
- Gold price API connectors
- Formula engine
- Fees and commissions
- Rounding
- Buy/sell spread
- Customer-group price leverage
- Price freshness and stale-price protection

### Exit criteria

- A tenant can configure products without code changes
- Price calculations are deterministic and tested
- Price API failure behavior is explicit
- Prices are auditable and reproducible

---

## Phase 5 — Wallet, Ledger, and Settlement Engine

**Status:** Foundation exists; production workflow incomplete

### Scope

- Money, gold, coin, and currency balances
- Negative balance permissions
- Immutable ledger
- Holds and locked balances
- 24-hour anti-scalping lock
- Credit limits
- Settlement state
- Reconciliation with Kimia

### Exit criteria

- Every balance change has a ledger entry
- Financial operations are transactional and idempotent
- Wallet and Kimia reconciliation is testable
- Balance inconsistencies trigger alerts

---

## Phase 6 — Trading and Order Management

**Status:** Not started as a complete workflow

### Scope

- Buy and sell orders
- Weight-based and amount-based gold orders
- Price snapshots
- Per-user freeze timers
- Expiration
- Admin/operator approval and rejection
- Rejection reasons
- Customer-group limits
- Credit trading
- Kimia posting after approval
- Retry and compensation workflows

### Exit criteria

- End-to-end order lifecycle is tested
- Duplicate submission cannot create duplicate accounting records
- Expired prices cannot be accepted silently
- Every status transition is audited

---

## Phase 7 — Custody, Amanat, and Delivery

**Status:** Domain defined; implementation not started

### Scope

- Physical product purchase
- Amanat record creation
- Ready-for-pickup status
- Branch selection
- Delivery appointment/request
- Delivered status
- Resale of custody assets
- Conversion to money or gold when allowed

### Exit criteria

- Physical assets are never confused with financial balances
- Each custody item has an auditable lifecycle
- Delivery removes or closes custody correctly

---

## Phase 8 — Notifications and External Services

**Status:** Partial SMS foundations exist

### Scope

- SMS connector
- Telegram connector
- Email or push notifications if needed
- Payment gateways
- KYC connector
- Webhooks
- Retry queues and dead-letter handling

### Exit criteria

- Connectors are replaceable
- Failures are retried safely
- Notification failure cannot corrupt financial operations

---

## Phase 9 — Panels and Frontend

**Status:** Not started

### Applications

- Customer panel
- Operator panel
- Admin panel
- Super-admin platform panel

### Planned stack

- Next.js
- React
- TypeScript
- Tailwind CSS
- shadcn/ui
- TanStack Query
- Zustand
- React Hook Form
- Zod

### Recommended execution order

1. Shared design system
2. Authentication shell
3. Admin tenant configuration
4. Operator workflow
5. Customer wallet and prices
6. Trading workflow
7. Custody and delivery
8. Reports

### Exit criteria

- Responsive and accessible interfaces
- No Kimia/accounting complexity shown to customers
- Role and tenant permissions enforced by backend and reflected in UI

---

## Phase 10 — Reporting, Audit, and Operations

**Status:** Not started

### Scope

- Audit trail
- Financial reconciliation
- Order reports
- Wallet reports
- Custody reports
- Operator performance
- Tenant dashboards
- Alerts and anomaly detection
- Backup and disaster recovery

### Exit criteria

- Important business actions are traceable
- Financial discrepancies can be diagnosed
- Restore procedures are tested

---

## Phase 11 — Commercialization and SaaS Readiness

**Status:** Future

### Scope

- Tenant onboarding
- Basic, Professional, and Enterprise plans
- Feature licensing
- Billing
- Demo environment
- Dedicated installation option
- Support and update policy
- Marketplace/connectors strategy

### Exit criteria

- A new tenant can be provisioned without modifying core code
- Plans and features are enforced by backend
- Pilot tenant data and branding are isolated

---

# Current Correct Execution Order

The project must proceed in this order:

```text
Documentation consolidation
        ↓
Product foundation and tenancy design
        ↓
Kimia connector stabilization
        ↓
Catalog and pricing
        ↓
Wallet and ledger guarantees
        ↓
Trading workflow
        ↓
Custody and delivery
        ↓
Panels and frontend
        ↓
Reporting, operations, and commercialization
```

Frontend mockups may be explored earlier, but production frontend implementation must not define or invent unresolved financial behavior.

# Current Milestone

**Milestone name:** Product Foundation and Kimia Stabilization

The project should not move into full Trading Engine implementation until:

- the active Kimia path is verified,
- account sync works,
- group sync works,
- balance semantics are confirmed,
- tenant strategy is documented,
- and core domain boundaries are accepted.
