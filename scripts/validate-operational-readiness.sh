#!/usr/bin/env bash
set -euo pipefail

required_files=(
  "backend/artisan"
  "backend/composer.json"
  "backend/routes/api.php"
  "frontend/customer-app/package.json"
  "frontend/customer-app/nuxt.config.ts"
  "frontend/customer-app/public/manifest.webmanifest"
  "frontend/customer-app/public/sw.js"
  "frontend-admin/package.json"
  "frontend-admin/nuxt.config.ts"
  "tests/frontend-e2e/package.json"
  "docker-compose.yml"
  "docs/PROJECT_STATE.md"
  "docs/CHANGELOG.md"
  "docs/release/FINAL_AUDIT_AND_HANDOFF.md"
  "docs/release/RELEASE_CHECKLIST.md"
)

for path in "${required_files[@]}"; do
  test -f "$path" || { echo "Missing required operational artifact: $path"; exit 1; }
done

# The four financial balance classes must remain Kimia-authoritative.
grep -q "Kimia is the final source of truth" docs/PROJECT_STATE.md
grep -q "GoldPlatform is the source of truth for physical Custody" docs/PROJECT_STATE.md
grep -q "Production Ready: \*\*NOT CLAIMED\*\*" docs/PROJECT_STATE.md
grep -q "Kimia Write: \*\*BLOCKED BY GROUND TRUTH\*\*" docs/PROJECT_STATE.md

# Final handoff must preserve honest release boundaries.
grep -q "Production Ready: \*\*NOT CLAIMED\*\*" docs/release/FINAL_AUDIT_AND_HANDOFF.md
grep -q "Kimia Write: \*\*BLOCKED BY GROUND TRUTH\*\*" docs/release/FINAL_AUDIT_AND_HANDOFF.md
grep -q "Real-device visual and installation audit" docs/release/FINAL_AUDIT_AND_HANDOFF.md
grep -q "Production Ready: \*\*NO — NOT CLAIMED\*\*" docs/release/RELEASE_CHECKLIST.md

# Production frontends must remain strict and buildable.
grep -q '"typecheck"' frontend/customer-app/package.json
grep -q '"build"' frontend/customer-app/package.json
grep -q '"typecheck"' frontend-admin/package.json
grep -q '"build"' frontend-admin/package.json

# PWA must never cache authenticated API traffic.
grep -q '"display"[[:space:]]*:[[:space:]]*"standalone"' frontend/customer-app/public/manifest.webmanifest
grep -q "url.pathname.startsWith('/api/')" frontend/customer-app/public/sw.js

# Sensitive authenticated surfaces must not lose their middleware boundary.
grep -Fq "auth:sanctum" backend/routes/api.php
grep -Fq "role:customer" backend/routes/api.php
grep -Fq "role:operator|admin" backend/routes/api.php
grep -Fq "role:admin" backend/routes/api.php

# Frontend code must not contain direct Kimia infrastructure references.
if grep -RInE '94\.101\.184\.26|/api/voucher/|KIMIA_(USERNAME|PASSWORD|TOKEN)' frontend frontend-admin --exclude-dir=node_modules; then
  echo "Direct Kimia reference found in frontend"
  exit 1
fi

# Secrets must not be committed in operational surfaces.
if grep -RInE 'BEGIN (RSA|OPENSSH|EC) PRIVATE KEY|sk-[A-Za-z0-9_-]{20,}' backend frontend frontend-admin docker scripts --exclude-dir=node_modules --exclude='*.lock'; then
  echo "Potential secret found"
  exit 1
fi

echo "Operational readiness and final handoff contract checks passed."
