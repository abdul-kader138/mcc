#!/usr/bin/env bash

set -euo pipefail

cd -- "$(dirname -- "${BASH_SOURCE[0]}")"

PHP_BIN="${PHP_BIN:-php}"
SERVER_HOST="${SERVER_HOST:-127.0.0.1}"
SERVER_PORT="${SERVER_PORT:-8001}"

exec "$PHP_BIN" \
  -d upload_max_filesize=128M \
  -d post_max_size=150M \
  -d max_execution_time=300 \
  -d max_input_time=300 \
  -S "${SERVER_HOST}:${SERVER_PORT}" \
  -t public \
  server.php
