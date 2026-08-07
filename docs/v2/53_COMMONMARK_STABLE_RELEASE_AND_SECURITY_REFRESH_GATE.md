# V2-00 — CommonMark Stable Release and Security Refresh Gate

Date: 2026-08-07

## Status

CORRECTION: the previous claim that stable `league/commonmark 2.9.0` had been published was incorrect.

Verified upstream evidence from Packagist on 2026-08-07:

- Latest stable release: `league/commonmark 2.8.3`.
- `2.9.x-dev` / `dev-main` exist, but no stable `2.9.0` is published.
- The package page still reports the security-advisory state affecting the currently locked `2.8.3` line.

Therefore the correct current classification for PR #196 is restored to:

`BLOCKED BY UPSTREAM STABLE SECURITY RELEASE`

## Required path when a stable patched release exists

1. Re-verify the stable release from Packagist and upstream release metadata.
2. Use Composer's resolver on a narrow dependency-security branch based on the operational branch.
3. Do not hand-edit `composer.lock`.
4. Do not use `2.9.x-dev`, `dev-main`, or another development alias.
5. Do not suppress Composer advisories or weaken Security Hardening.
6. Run exact-head Security Hardening and Backend RC2 Candidate after the resolver-generated lock update.
7. Keep Kimia read-only / write-disabled safety boundaries unchanged.
8. Only after all exact-SHA security and regression gates pass may the dependency refresh be considered for integration.
9. PR #196 must then be refreshed against the corrected operational base without shared-history rebase and must rerun exact-head CI before any merge decision.

## Duplicate audit

Repository search found no existing CommonMark security-refresh PR and no existing resolver-generated CommonMark refresh path to reuse.

Classification: `NO DUPLICATE CANDIDATE FOUND`.

## Safety boundaries

No financial rule, Kimia mapping, Kimia Write payload, migration, customer balance behavior, API contract, permission model, or frontend behavior is authorized by this dependency-security gate.

Manual lockfile editing, advisory suppression, development-version substitution, or merging PR #196 with red security CI remain prohibited.

## V2-00 impact

The dependency-security blocker remains external and unresolved until a patched stable release is actually published and re-verified.

The V2-00 stage remains:

`GATE NOT PASSED`
