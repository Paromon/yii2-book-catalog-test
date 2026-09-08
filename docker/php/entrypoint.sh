#!/bin/sh
set -e

mkdir -p runtime/cache runtime/logs web/assets web/uploads
chmod -R 0777 runtime web/assets web/uploads || true

exec docker-php-entrypoint "$@"
