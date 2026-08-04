#!/usr/bin/env bash
set -Eeuo pipefail

COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.production.yml}"
HEALTH_URL="${HEALTH_URL:-http://127.0.0.1:18080/up}"
MAX_ATTEMPTS="${MAX_ATTEMPTS:-30}"
CURRENT_STAGE="initialization"

log_stage() {
  CURRENT_STAGE="$1"
  printf '\n::group::RC2 stage: %s\n' "$CURRENT_STAGE"
}

end_stage() {
  printf 'RC2 stage passed: %s\n::endgroup::\n' "$CURRENT_STAGE"
}

print_diagnostics() {
  local exit_code="$1"
  local line_number="$2"
  local failed_command="$3"

  printf '\n::error title=RC2 deployment failure::Stage=%s; exit=%s; line=%s; command=%s\n' \
    "$CURRENT_STAGE" "$exit_code" "$line_number" "$failed_command"
  printf '\n--- docker compose ps ---\n'
  docker compose -f "$COMPOSE_FILE" ps --all || true
  printf '\n--- docker compose logs ---\n'
  docker compose -f "$COMPOSE_FILE" logs --no-color --tail=300 || true
}

cleanup() {
  docker compose -f "$COMPOSE_FILE" down -v --remove-orphans >/dev/null 2>&1 || true
}

on_error() {
  local exit_code=$?
  local line_number="$1"
  local failed_command="$2"

  trap - ERR
  print_diagnostics "$exit_code" "$line_number" "$failed_command"
  exit "$exit_code"
}

trap 'on_error "$LINENO" "$BASH_COMMAND"' ERR
trap cleanup EXIT

wait_for_health() {
  local attempt
  for attempt in $(seq 1 "$MAX_ATTEMPTS"); do
    printf 'Health attempt %s/%s\n' "$attempt" "$MAX_ATTEMPTS"
    if curl --fail --silent --show-error "$HEALTH_URL" >/dev/null 2>&1 \
      && docker compose -f "$COMPOSE_FILE" exec -T php \
        php artisan ops:health --json --fail-on-degraded >/dev/null 2>&1; then
      return 0
    fi
    sleep 5
  done

  printf 'Health did not become ready after %s attempts\n' "$MAX_ATTEMPTS"
  docker compose -f "$COMPOSE_FILE" ps --all || true
  docker compose -f "$COMPOSE_FILE" logs --no-color --tail=300 || true
  return 1
}

# A production candidate must deploy cleanly from an empty ephemeral state.
log_stage "clean previous stack"
cleanup
end_stage

log_stage "validate compose configuration"
docker compose -f "$COMPOSE_FILE" config --quiet
end_stage

log_stage "build production images"
docker compose -f "$COMPOSE_FILE" build
end_stage

log_stage "start production stack"
docker compose -f "$COMPOSE_FILE" up -d
end_stage

# A clean deployment must initialize the database before migration status is read.
log_stage "apply database migrations"
docker compose -f "$COMPOSE_FILE" exec -T php \
  php artisan migrate --force --no-interaction
end_stage

log_stage "initial application health"
wait_for_health
end_stage

# Validate configuration, dependencies and database state inside the built image.
log_stage "validate production configuration"
docker compose -f "$COMPOSE_FILE" exec -T php php artisan ops:validate-production-config
end_stage

log_stage "validate operational health"
docker compose -f "$COMPOSE_FILE" exec -T php php artisan ops:health --json --fail-on-degraded
end_stage

log_stage "read migration status"
docker compose -f "$COMPOSE_FILE" exec -T php php artisan migrate:status --no-ansi
end_stage

# A normal application restart must preserve health and configuration.
log_stage "restart php and nginx"
docker compose -f "$COMPOSE_FILE" restart php nginx
end_stage

log_stage "post-restart application health"
wait_for_health
end_stage

log_stage "post-restart operational health"
docker compose -f "$COMPOSE_FILE" exec -T php php artisan ops:health --json --fail-on-degraded
end_stage

printf '\nRC2 deployment test passed\n'
