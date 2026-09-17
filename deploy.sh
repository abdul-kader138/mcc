#!/usr/bin/env bash

# Deploy the Laravel application from the repository root.
# Optional environment variables:
#   PHP_BIN=php COMPOSER_BIN=composer NPM_BIN=npm
#   PHP_FPM_SERVICE=php8.3-fpm QUEUE_SERVICE=supervisor

set -Eeuo pipefail

APP_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
cd "$APP_DIR"

log() { printf '\n==> %s\n' "$*"; }
fail() { printf '\nDeployment failed: %s\n' "$*" >&2; exit 1; }

PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"

trap 'fail "command failed on line $LINENO: $BASH_COMMAND"' ERR

command -v "$PHP_BIN" >/dev/null 2>&1 || fail "PHP was not found"
command -v "$COMPOSER_BIN" >/dev/null 2>&1 || fail "Composer was not found"
command -v "$NPM_BIN" >/dev/null 2>&1 || fail "npm was not found"

[[ -f artisan ]] || fail "Run this script from a Laravel project checkout"
[[ -f composer.lock ]] || fail "composer.lock is missing"
[[ -f .env ]] || fail ".env is missing; create and configure it before deploying"

if [[ "${APP_ENV:-}" != "" && "${APP_ENV}" != "production" ]]; then
    printf 'Warning: APP_ENV=%s; continuing because deployment was explicitly requested.\n' "$APP_ENV" >&2
fi

log "Installing PHP dependencies"
"$COMPOSER_BIN" install --no-dev --no-interaction --prefer-dist --optimize-autoloader

log "Installing and building frontend assets"
if [[ -f package-lock.json ]]; then
    "$NPM_BIN" ci
else
    printf 'Warning: package-lock.json is missing; using npm install. Commit the lockfile for reproducible deployments.\n' >&2
    "$NPM_BIN" install --no-audit --no-fund
fi
"$NPM_BIN" run build

log "Preparing Laravel"
"$PHP_BIN" artisan storage:link || true
"$PHP_BIN" artisan migrate --force
"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache

log "Restarting Laravel queue workers"
if "$PHP_BIN" artisan list --raw 2>/dev/null | grep -qx 'horizon:terminate'; then
    "$PHP_BIN" artisan horizon:terminate || printf 'Horizon termination skipped: no active Horizon workers detected.\n'
fi
"$PHP_BIN" artisan queue:restart || printf 'Queue restart skipped: no active queue workers detected.\n'

restart_service() {
    local service="$1"
    [[ -n "$service" ]] || return 0
    command -v systemctl >/dev/null 2>&1 || { printf 'systemctl not found; skipped %s restart.\n' "$service"; return 0; }
    local systemctl_cmd=(systemctl)
    if [[ "$EUID" -ne 0 ]]; then
        sudo -n true 2>/dev/null || { printf 'No passwordless sudo; skipped %s restart.\n' "$service"; return 0; }
        systemctl_cmd=(sudo -n systemctl)
    fi
    if "${systemctl_cmd[@]}" is-active --quiet "$service"; then
        "${systemctl_cmd[@]}" restart "$service"
        printf 'Restarted %s.\n' "$service"
    else
        printf '%s is not active; skipped restart.\n' "$service"
    fi
}

restart_service "${PHP_FPM_SERVICE:-}"
restart_service "${QUEUE_SERVICE:-}"
restart_service "${NGINX_SERVICE:-}"

log "Deployment completed"
printf 'Application path: %s\n' "$APP_DIR"
printf 'If using a web server, point its document root to: %s/public\n' "$APP_DIR"
