# ADR-024 — Platform User to Kimia Account Binding

Status: Accepted (implementation prepared; shop-runtime verification pending)

Date: 2026-08-03

## Context

A real customer may request more than one independent account. In the current release,
each mobile-authenticated platform account must resolve to exactly one financial context
before balances, orders, or transactions are exposed.

The current model expresses `User belongsTo Account` and `Account hasOne User`. The active
Kimia synchronization path currently writes to `external_accounts`, so consolidation of
the two account representations remains a separate architecture task.

## Decision

- In the current release, one GoldPlatform login/account is connected to no more than one
  local account and therefore no more than one Kimia `AccountId`.
- One Kimia `AccountId` must not be connected to more than one GoldPlatform login/account.
- If the same real customer requests a second account, a second GoldPlatform account is
  created with a different mobile number and a different Kimia `AccountId`.
- `AccountId` is unique and immutable after synchronization/linking. Editing the user's
  mobile number or national code must never replace or relink that `AccountId`.
- The mobile number remains unique among platform accounts and is the current OTP login
  identifier. It is editable only through a separately secured change flow.
- The national code is editable and is not unique: two independent accounts belonging to
  the same person may store the same national code.
- `account_code` remains a separate display/search value and must not replace `AccountId`.

These rules were confirmed by the project owner on 2026-08-03.

## Current Constraint Audit

| Contract | Current implementation | Status |
|---|---|---|
| One platform account per mobile number | `users.mobile UNIQUE` | Enforced |
| One local account per Kimia identifier | `accounts.kimia_id UNIQUE` | Enforced |
| One synchronized external identity | `external_accounts(provider, external_id) UNIQUE` | Enforced |
| One Kimia/local account per platform user | Nullable `users.account_id` foreign key | Structurally represented |
| One platform user per Kimia/local account | Nullable unique-index migration for `users.account_id` | Prepared; not run in shop runtime |
| Reusable national code | Unique index replaced by a normal lookup index; registration rule updated | Prepared; not run in shop runtime |
| Immutable Kimia identifiers and established user binding | Model guards for `Account`, `ExternalAccount`, and `User` | Prepared; automated test pending |

## Identity Fields Are Not the Financial Binding

`mobile` and `national_code` are customer identity/contact attributes. They may be
corrected later and therefore must not be used as the durable foreign identifier for
balances or financial posting. The durable Kimia binding is `AccountId`.

Allowing a repeated national code does not by itself define KYC reuse. Whether one Jibit
verification result may approve several accounts remains a separate KYC decision.

## Deferred Multi-account Entry Experience

The owner identified a possible future experience: after identity verification or login
with a mobile number or national code, the platform may show every authorized account and
require the customer to select one before viewing a balance or trading.

This is a deferred capability, not current runtime behavior. It requires a separate
identity/person layer, verified account ownership, an explicit selected-account security
context, audit logging, and a new ADR. It must not be implemented by attaching several
`AccountId` values directly to the current `User` model.

## Consequences

- Authorization and every financial operation can resolve exactly one Kimia customer
  account from the authenticated platform account.
- Mobile numbers and Kimia `AccountId` values for two independent accounts must both be
  distinct; their national code may be the same.
- Prepared migrations replace national-code uniqueness with a normal lookup index and add
  nullable uniqueness to `users.account_id`. The latter aborts if duplicate non-null links
  already exist.
- Account-linking services must reject an already-linked `AccountId` transactionally and
  record the action in the audit log.
- Prepared Eloquent guards prevent an established binding or synchronized Kimia identity
  from being changed through model updates.

## Scope Boundary

This ADR does not define household/business relationships, account delegation, operator
access, KYC reuse, the future account-selector authorization protocol, or Kimia
account-creation APIs.
