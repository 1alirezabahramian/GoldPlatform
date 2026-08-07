# GoldPlatform V2 — Capability Traceability Slice 06

- Stage: `V2-00`
- Scope: bounded evidence closure for five key recovery capabilities
- Working branch: `v2/source-recovery-v2-00`
- Previous verified V2 Head: `78e22e264373a000bec4b01b162e018ea7d44315`
- Previous exact-SHA CI: Backend RC1 Validation Run `#376` — `EXECUTED — PASS`
- Canonical branch inspected: `recovery/rc2-product-rebuild`
- Canonical branch current tip observed during this slice: `d9ee5fee69969fa02ac25c96d8e1653143ba413b`
- Safety: documentation-only; no Kimia Write, financial rule, migration, API, permission or frontend behavior change.

## Base drift observed

The V2 working branch was originally based on `cd92a1144bdfbe043bae1871aab9d623ce8bad64`.

Current comparison against `recovery/rc2-product-rebuild` shows:

- `47` commits ahead
- `2` commits behind
- status: `diverged`
- merge base remains `cd92a1144bdfbe043bae1871aab9d623ce8bad64`

The net comparison from the original base to the current canonical tip reports no remaining file diff. The current canonical tip commit `d9ee5fee69969fa02ac25c96d8e1653143ba413b` removes accidental temporary file `noop2`. This slice therefore classifies the observed two-commit base drift as:

`NON-DOMAIN TEMPORARY-FILE DRIFT — NET ZERO AT INSPECTED TIP`

This classification does **not** authorize rebase, merge, cherry-pick, reset or history rewriting. No such operation is performed in V2-00.

---

## Capability 1 — Custody / Physical Delivery

### Requirement / authority

GoldPlatform is the final authority for physical Custody/Amanat. Custody must remain separate from Money/Gold/Coin/Currency financial balances.

### Historical integration evidence

- Recovery PR: `#149`
- Historical Head SHA: `925e2624ad888113be45a2dba5d09ffa67bff88c`
- Historical exact-Head CI:
  - Backend RC2 Candidate `#69` — `EXECUTED — PASS`
  - Backend RC1 Validation `#217` — `EXECUTED — PASS`
- Canonical ancestry: historical Head SHA is an ancestor of `recovery/rc2-product-rebuild`.

### Current canonical implementation evidence

- `backend/app/Http/Controllers/Api/V1/CustomerCustodyDeliveryController.php`
  - customer lookup is scoped by public UUID plus authenticated `user_id`
  - missing/foreign records fail closed with not-found behavior
  - delivery request delegates to `DeliveryService`
- `backend/app/Services/DeliveryService.php`
  - database transaction
  - `lockForUpdate`
  - ownership verification
  - terminal-state protection
  - existing active delivery request is reused
  - explicit state transitions
  - receiver identity required for physical delivery
- `backend/tests/Feature/RecoveryCustomerCustodyDeliveryHttpTest.php`
  - own-custody read
  - cross-customer access rejection
  - internal-field redaction
  - `Idempotency-Key` requirement
  - unauthenticated rejection

### Classification

`MERGED — CLOSURE PENDING`

Preservation is verified. Full closure still requires complete permission/branch/inventory/receiver-policy and visual/end-to-end evidence.

---

## Capability 2 — Kimia Read Boundary

### Requirement / authority

Kimia is the final authority for customer Money, Gold, Coin and Currency balances. Kimia Read and Write must remain separated.

### Historical integration evidence

- Recovery PR: `#150`
- Historical Head SHA: `e5d61218d7037e0cdfb29745325e7711e5025e76`
- Historical exact-Head CI:
  - Backend RC2 Candidate `#49` — `EXECUTED — PASS`
- Canonical ancestry: historical Head SHA is an ancestor of `recovery/rc2-product-rebuild`.

### Current canonical implementation evidence

- `backend/app/Clients/KimiaReadClient.php`
  - dedicated read client
  - config-driven base URL/auth/timeout
  - read retry policy
  - unsuccessful or malformed responses throw `KimiaReadException`
  - no write methods in this client

### Classification

`REUSE AFTER FIX`

Read-boundary preservation is verified. Real-output fixtures, authenticated customer-to-Kimia account resolution and broader sanitized response evidence remain incomplete.

---

## Capability 3 — Internal Balance Projection Authority Guard

### Requirement / authority

Ledger/Journal/Projection may support audit, trace, reservation, workflow and reconciliation but must not become the final customer balance authority.

### Historical integration evidence

- Recovery PR: `#153`
- Historical Head SHA: `7f2121d50b76b86bb1bfed1ef1a155a84523a28f`
- Historical exact-Head CI:
  - Backend RC1 Validation `#224` — `EXECUTED — PASS`
- Canonical ancestry: historical Head SHA is an ancestor of `recovery/rc2-product-rebuild`.

### Current canonical implementation evidence

- `backend/app/Services/Wallet/BalanceProjectionService.php`
  - `PURPOSE = 'audit_reconciliation_only'`
  - `CUSTOMER_BALANCE_SOURCE = false`
  - explicit documentation that customer-facing balances come from Kimia reads
- `backend/tests/Architecture/InternalBalanceProjectionBoundaryTest.php`
  - asserts purpose
  - asserts projection is not a customer balance source

### Classification

`MERGED — CLOSURE PENDING`

Authority boundary is preserved and tested historically. Full reconciliation workflow remains incomplete.

---

## Capability 4 — Settlement Completion Guard

### Requirement / authority

Settlement completion must not be inferred from an internal ledger/reference alone. Sensitive external completion requires verified Kimia result evidence and the approved workflow/readback boundary.

### Historical integration evidence

- Recovery PR: `#175`
- Historical Head SHA: `be966d979b7e30ed44ce49416bad8fd73df0f16e`
- Historical exact-Head CI:
  - Backend RC1 Validation `#288` — `EXECUTED — PASS`
  - Operational Readiness `#3` — `EXECUTED — PASS`
- Canonical ancestry: historical Head SHA is an ancestor of `recovery/rc2-product-rebuild`.

### Current canonical implementation evidence

- `backend/tests/Unit/Architecture/DirectSettlementCompletionBoundaryTest.php`
  - verifies a reference string alone is insufficient evidence
  - guards against direct `SettlementStatus::Completed` assignment inside the direct completion path

### Classification

`REUSE AFTER FIX`

Fail-closed settlement boundary is preserved. This does not enable Kimia Write and does not prove end-to-end settlement execution.

---

## Capability 5 — Customer Kimia Source State / Fail-Closed Financial UX

### Requirement / authority

When authoritative Kimia financial balances are unavailable or unresolved, the frontend must not invent, derive or substitute zero balances and must not perform independent financial calculations.

### Historical integration evidence

- Recovery PR: `#186`
- Historical Head SHA: `10121aeb8cbcf1057df71f51ff251aabefaf5a37`
- Historical exact-Head CI:
  - Backend RC1 Validation `#313` — `EXECUTED — PASS`
  - Customer Frontend `#15` — `EXECUTED — PASS`
  - Frontend Release Validation `#12` — `EXECUTED — PASS`
  - Operational Readiness `#17` — `EXECUTED — PASS`
- Canonical ancestry: historical Head SHA is an ancestor of `recovery/rc2-product-rebuild`.

### Current canonical implementation evidence

- `frontend/customer-app/tests/customer-kimia-source-state.test.mjs`
  - identifies Kimia as source for Money/Gold/Coin/Currency
  - rejects invented/zero-substituted balances
  - verifies accepted read endpoints remain used
  - rejects frontend Weight750 and Rial/Toman style financial calculations

### Classification

`MERGED — CLOSURE PENDING`

Fail-closed source-state behavior is preserved. Real customer account resolution, live balance rendering and real-device visual verification remain incomplete.

---

## Traceability result

For these five capabilities the following chain is now evidenced at a bounded-slice level:

`Requirement → Architecture boundary → Historical PR → Historical Head SHA → Historical exact-Head CI → Canonical ancestry → Current canonical file/test preservation evidence`

Not yet closed for all five:

`Database/applied state → full API/OpenAPI mapping → complete Permission/Audit/Idempotency matrix → current canonical exact-SHA runtime execution → real frontend visual verification → production evidence`

Therefore none of these capabilities is promoted to `Production Ready` or globally `Complete` by this slice.

## V2-00 impact

This slice closes a previously broad traceability gap for five high-value capabilities but does not pass the V2-00 Gate by itself.

Remaining major V2-00 gaps include:

1. wider PR/branch/SHA/CI ledger closure;
2. complete sanitized Kimia Ground Truth evidence set;
3. authenticated customer-to-Kimia account resolution evidence;
4. database/applied-migration/export evidence;
5. real frontend visual verification;
6. production/restore/monitoring evidence;
7. documentation namespace normalization or formal carry-forward decision.

`V2-00 — GATE NOT PASSED — CONTINUE STRICT EVIDENCE RECOVERY`
