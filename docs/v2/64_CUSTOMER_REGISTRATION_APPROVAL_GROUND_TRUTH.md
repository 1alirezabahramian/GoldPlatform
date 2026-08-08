# GoldPlatform V2 — Customer Registration Approval Ground Truth

Status: OWNER-CONFIRMED GROUND TRUTH
Scope: Identity / Onboarding continuation on PR #201
Date: 2026-08-08

## Purpose

Record the owner-confirmed separation between identity verification and business approval before changing the legacy customer registration runtime.

This document does not invent Jibit API details and does not enable Kimia customer-create Write.

## Confirmed customer authentication policy

Customer authentication is OTP-only. Customer password login is not part of the V2 customer authentication contract.

The legacy registration runtime that requires/stores a customer password and immediately issues an authenticated token is therefore `REUSE AFTER FIX` and must not be treated as the target V2 contract.

## Manual registration lifecycle

The confirmed business lifecycle is:

1. Customer submits the registration form for the resolved Tenant.
2. Identity verification is performed through Jibit when grounded Jibit integration is available.
3. A successful Jibit identity match does **not** activate the customer.
4. After successful identity verification the request remains `Pending Admin Review`.
5. The Tenant's authorized management performs the independent business review.
6. The review may be approved or rejected even when Jibit verification succeeded.
7. Full platform/trading access is granted only after the required business approval/setup is complete.

Until actual Jibit endpoint/payload/response/retry Ground Truth is available, Jibit runtime integration remains `BLOCKED BY GROUND TRUTH`.

## Independent state dimensions

Identity verification and business approval are different facts and must not be collapsed into one boolean.

Conceptual state dimensions:

- Identity verification: pending / verified / failed
- Admin review: pending / approved / rejected
- Access: limited / active / blocked

Exact persisted enum/column representation must be inventoried against the existing schema before adding a migration. These conceptual values are not authorization to create duplicate status fields if an existing canonical representation can be reused.

## Admin rejection after successful identity verification

A successful Jibit identity match only establishes the identity-match result supported by that service. It is not business permission to use GoldPlatform.

Tenant management may reject an identity-verified registration for legitimate operational/business review reasons, including examples confirmed by the owner such as:

- known bad-account history;
- suspicious customer;
- colleague/market participant whom the Tenant does not wish to onboard;
- duplicate/other known account;
- customer not sufficiently known to the business;
- incomplete or unreliable manually-entered profile/name information even when national-code/mobile ownership matches.

These examples are internal review context, not public rejection reason codes.

## Rejection communication

When management rejects the registration, the customer is informed that the registration request was not approved by management.

The internal rejection reason must remain available for Backoffice/Audit where appropriate, but it must not automatically be exposed in SMS/customer-facing output.

SMS wording and branding must be Tenant-configurable. Core must not hard-code Khalifeh Coin, a production domain, or another Tenant identity.

## Limited access while pending

After successful identity verification and while Admin Review is pending, limited non-sensitive access may be provided.

Critical rule:

`Limited access != trading authorization`

Backend authorization must fail closed for sensitive/business operations until the customer is active. UI hiding/disabled buttons alone are not an authorization boundary.

Whether market prices are visible to pending customers is a Tenant/product visibility policy and must not be globally hard-coded by this foundation.

## Notifications

The owner-confirmed communication points are:

- identity verification failed: notify the customer that mobile/national-code identity verification did not succeed and they may correct/retry as permitted by the eventual grounded flow;
- identity verified / admin pending: notify the customer that identity verification succeeded and management review is pending;
- admin rejected: notify the customer that management did not approve the registration request;
- activated: notify the customer that the account is active and platform services are available.

Actual SMS provider integration/templates are outside this document and must use Tenant configuration/white-label identity.

## Security / tenancy constraints

- Tenant is resolved from verified Host/TenantContext; client-selectable Tenant authority is prohibited.
- Jibit success must never bypass Tenant management approval in manual mode.
- Admin rejection must be auditable.
- Customer-facing output must not leak internal rejection notes by default.
- No financial balance is created as a substitute for Kimia.
- No Kimia customer-create Write is enabled by this decision.

## Classification

- Customer OTP-only policy: `OWNER-CONFIRMED / REUSE AFTER FIX` for legacy registration runtime.
- Identity verification vs business approval separation: `OWNER-CONFIRMED GROUND TRUTH`.
- Admin rejection after successful Jibit verification: `OWNER-CONFIRMED GROUND TRUTH`.
- Limited pending access: `OWNER-CONFIRMED GROUND TRUTH`.
- Jibit actual integration: `BLOCKED BY GROUND TRUTH`.
- Kimia customer-create Write: `BLOCKED BY GROUND TRUTH`.
- Referral schema foundation (`referrer_user_id`, tenant-scoped `referral_code`): `REUSE AS-IS`; runtime resolution remains to be implemented after registration-state inventory.

## Next safe implementation gate

Before changing the registration schema/runtime:

1. inventory existing User/customer status, activation, KYC, approval, restriction and rejection representations;
2. reuse an existing canonical representation where possible;
3. classify duplicate candidates before adding migrations;
4. align Registration Request/Service/Controller with OTP-only policy without guessing Jibit behavior;
5. add backend authorization tests proving pending/rejected customers cannot perform protected business operations;
6. align Customer OpenAPI and notification contracts only after the runtime representation is grounded.
