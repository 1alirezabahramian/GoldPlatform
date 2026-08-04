# Stage 20 — Security Hardening

## Implemented controls

- API security headers middleware:
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: DENY`
  - `Referrer-Policy: no-referrer`
  - restrictive `Permissions-Policy`
  - API-only `Content-Security-Policy`
  - API responses marked `no-store`
  - HSTS only when the request is HTTPS
- dedicated feature test for security headers;
- Composer lockfile dependency audit in GitHub Actions;
- security-focused regression filter covering permissions, order creation, Kimia client safety and production configuration.

## Existing controls retained

- Sanctum authentication and role isolation;
- rate limiting by route category;
- idempotency keys for sensitive mutations;
- audit logs and request correlation;
- Kimia deny-by-default write preparation;
- secret scan in Backend RC validation;
- private MySQL and Redis ports in production Compose;
- production configuration guard and secure session cookie requirement.

## Boundaries

- This stage does not claim an independent third-party penetration test.
- TLS termination and external WAF configuration belong to the approved deployment environment.
- Kimia writes remain disabled until endpoint, payload, mapping and business behavior are explicitly approved from trusted evidence.
