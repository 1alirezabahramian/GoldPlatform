#!/usr/bin/env bash
set -euo pipefail

COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.production.yml}"
HEALTH_URL="${HEALTH_URL:-http://127.0.0.1:18080/up}"
MAX_ATTEMPTS="${MAX_ATTEMPTS:-30}"

cleanup() {
  docker compose -f "$COMPOSE_FILE" down -v --remove-orphans >/dev/null 2>&1 || true
}
trap cleanup EXIT

wait_for_health() {
  local attempt
  for attempt in $(seq 1 "$MAX_ATTEMPTS"); do
    if curl --fail --silent "$HEALTH_URL" >/dev/null; then
      return 0
    fi
    sleep 5
  done

  docker compose -f "$COMPOSE_FILE" ps
  docker compose -f "$COMPOSE_FILE" logs --no-color
  return 1
}

# A production candidate must deploy cleanly from an empty ephemeral state.
cleanup
docker compose -f "$COMPOSE_FILE" config --quiet
docker compose -f "$COMPOSE_FILE" build
docker compose -f "$COMPOSE_FILE" up -d
wait_for_health

# Validate configuration, dependencies and database state inside the built image.
docker compose -f "$COMPOSE_FILE" exec -T php php artisan ops:validate-production-config
docker compose -f "$COMPOSE_FILE" exec -T php php artisan ops:health --json --fail-on-degraded
docker compose -f "$COMPOSE_FILE" exec -T php php artisan migrate:status --no-ansi

# A normal application restart must preserve health and configuration.
docker compose -f "$COMPOSE_FILE" restart php nginx
wait_for_health
docker compose -f "$COMPOSE_FILE" exec -T php php artisan ops:health --json --fail-on-degraded

printf 'RC2 deployment test passed\n'
