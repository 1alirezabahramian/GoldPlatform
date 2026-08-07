#!/usr/bin/env bash
set -Eeuo pipefail

COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.production.yml}"
HEALTH_URL="${HEALTH_URL:-http://127.0.0.1:18080/up}"
MAX_ATTEMPTS="${MAX_ATTEMPTS:-36}"

cleanup() {
  docker compose -f "$COMPOSE_FILE" --profile workers down -v --remove-orphans >/dev/null 2>&1 || true
}

print_diagnostics() {
  docker compose -f "$COMPOSE_FILE" --profile workers ps --all || true
  docker compose -f "$COMPOSE_FILE" --profile workers logs --no-color --tail=300 || true
}

on_error() {
  local exit_code=$?
  printf '\nProduction operations validation failed (exit=%s line=%s command=%s)\n' \
    "$exit_code" "$1" "$2"
  print_diagnostics
  exit "$exit_code"
}

trap 'on_error "$LINENO" "$BASH_COMMAND"' ERR
trap cleanup EXIT

wait_for_health() {
  local attempt
  for attempt in $(seq 1 "$MAX_ATTEMPTS"); do
    if curl --fail --silent --show-error "$HEALTH_URL" >/dev/null 2>&1 \
      && docker compose -f "$COMPOSE_FILE" exec -T php \
        php artisan ops:health --json --fail-on-degraded >/dev/null 2>&1; then
      return 0
    fi
    sleep 5
  done
  return 1
}

assert_running() {
  local service="$1"
  local container_id
  container_id="$(docker compose -f "$COMPOSE_FILE" --profile workers ps -q "$service")"
  test -n "$container_id"
  test "$(docker inspect -f '{{.State.Running}}' "$container_id")" = "true"
}

cleanup

docker compose -f "$COMPOSE_FILE" --profile workers config --quiet
docker compose -f "$COMPOSE_FILE" --profile workers up -d --build
wait_for_health

docker compose -f "$COMPOSE_FILE" exec -T php \
  php artisan migrate --force --no-interaction

docker compose -f "$COMPOSE_FILE" exec -T php \
  php artisan ops:validate-production-config

docker compose -f "$COMPOSE_FILE" exec -T php \
  php artisan schedule:list --no-ansi

for service in nginx php mysql redis queue-worker scheduler; do
  assert_running "$service"
done

docker compose -f "$COMPOSE_FILE" exec -T php php artisan queue:restart
sleep 8
assert_running queue-worker

docker compose -f "$COMPOSE_FILE" --profile workers restart php nginx queue-worker scheduler
wait_for_health

for service in nginx php queue-worker scheduler; do
  assert_running "$service"
done

docker compose -f "$COMPOSE_FILE" exec -T php \
  php artisan ops:health --json --fail-on-degraded

printf '\nProduction operations validation passed\n'
