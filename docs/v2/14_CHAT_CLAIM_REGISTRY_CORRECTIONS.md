# GoldPlatform V2 — Claim Registry Corrections

- Owner: Alireza Bahramian
- Stage: `V2-00 — Complete Source Recovery & Knowledge Reconstruction`
- Applies to: `docs/v2/13_CHAT_CLAIM_REGISTRY_SHARED_KIMIA_CONVERSATION.md`
- Correction date: 2026-08-06
- Status: `ACTIVE OVERRIDE — HIGHER-PRIORITY EVIDENCE APPLIED`

## Purpose

The initial chat Claim Registry correctly treated the shared conversation as historical evidence, but three entries were classified too conservatively because the current `03_KIMIA_GROUND_TRUTH.md` already records higher-priority sanitized runtime evidence from `kimia:inspect-transactions --id=350`.

This correction file prevents the initial classifications from overriding current Kimia Ground Truth. It does not authorize Kimia Write.

## Authority rule

When this file and Claim Registry 13 differ, use this order:

1. sanitized real Kimia runtime evidence recorded in `03_KIMIA_GROUND_TRUTH.md`;
2. official Swagger/OpenAPI;
3. accepted Project Memory/ADR and owner-confirmed domain meaning;
4. historical conversation claims.

## Corrected claims

| Claim ID | Initial classification | Corrected classification | Higher-priority evidence | Final V2 handling |
|---|---|---|---|---|
| `KC-014` | `BLOCKED BY GROUND TRUTH` as a generic Action mapping | `HISTORICAL TERMINOLOGY — PARTIALLY RESOLVED` | Runtime inspection for AccountId `350` distinguishes operational/form codes `3/4` from Swagger API Actions `32/64`. | Do not use `1/2/3/4/7/8` as a universal API Action table. Preserve `3/4` only as observed operational/form-side terminology for the confirmed gold scenario. |
| `KC-015` | `BLOCKED BY GROUND TRUTH` | `ACCEPTED FOR CONFIRMED GOLD READ INTERPRETATION — WRITE STILL BLOCKED` | Runtime transaction evidence: customer buys gold → Kimia sells → operational/form `4` → API Action `64`; customer sells gold → Kimia buys → operational/form `3` → API Action `32`. | Use for interpreting the confirmed sanitized read evidence. Do not generalize across endpoints or enable Write without exact request/response evidence. |
| `KC-016` | `BLOCKED BY GROUND TRUTH` | `RUNTIME-CONFIRMED FOR RECORDED PAPER-GOLD MAPPING — DO NOT GENERALIZE BLINDLY` | Project Memory and real transaction inspection recorded Money product code `4` for the confirmed mapping. | May be documented as confirmed for that evidence set. It must still be discovered/validated for the active Kimia book/environment before a Write payload is enabled. |

## Conflict corrections

### `CF-KIMIA-001 — Action code conflict`

Previous status: `BLOCKED BY GROUND TRUTH`.

Corrected interpretation:

- the apparent `3/4` versus `32/64` conflict is resolved for the recorded Paper Gold read evidence by separating two code systems;
- `3/4` are recorded operational/form codes in the inspected scenario;
- `32/64` are Swagger API Action values and transaction Action values;
- customer-side direction is opposite the Kimia business side:
  - customer buys → Kimia sells → `64`;
  - customer sells → Kimia buys → `32`.

Final status:

`RESOLVED FOR CONFIRMED READ INTERPRETATION — WRITE CONTRACT STILL BLOCKED`

### `CF-KIMIA-002 — ProductId 4 meaning`

Previous status: `BLOCKED BY GROUND TRUTH`.

Corrected interpretation:

- Product/Money code `4` is recorded by the current Kimia Ground Truth for the confirmed Paper Gold mapping;
- this is stronger than a Swagger example alone because runtime transaction evidence was inspected;
- it is not permission to assume every Kimia book/environment uses the same identifier without discovery or validation.

Final status:

`RUNTIME-CONFIRMED FOR RECORDED MAPPING — ENVIRONMENT REVALIDATION REQUIRED BEFORE WRITE`

## Items that remain blocked or rejected

This correction does not change the following classifications:

- `RequestId` replay/idempotency semantics remain `BLOCKED BY GROUND TRUTH`;
- Coin/Currency balance derivation from transaction history remains `UNSUPPORTED — REJECT`;
- Weight750, pricing, GoldUnit, rounding and commission formulas remain blocked where not explicitly grounded;
- `/exchangegold` and `/exchangecurrency` write semantics remain blocked without exact sanitized request/response evidence;
- guessed Action defaults in configuration remain rejected;
- internal Wallet remains non-authoritative for customer financial balances;
- missing balances must not be converted to zero;
- generated Adapter snippets remain `CHAT-ONLY PROPOSAL` unless GitHub evidence proves implementation.

## Safety result

- Documentation only.
- No Feature, API, migration, permission, balance behavior or Kimia Write changed.
- No Action code was added to executable code.
- No identifier was hard-coded into runtime behavior.
- Kimia Write remains deny-by-default.
