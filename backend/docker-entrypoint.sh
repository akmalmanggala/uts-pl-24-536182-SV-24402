#!/bin/sh
set -e

# Ensure storage directories exist
mkdir -p /app/storage/framework/cache/data
mkdir -p /app/storage/framework/sessions
mkdir -p /app/storage/framework/views
mkdir -p /app/storage/logs
mkdir -p /app/bootstrap/cache
chmod -R 777 /app/storage /app/bootstrap/cache

# If DB is configured, run migrations & seeders
if [ -n "$DB_HOST" ]; then
    echo "Running database migrations and seeders..."
    php artisan migrate --force --seed || true
fi

echo "Starting FrankenPHP server on port 8000..."
exec "$@"
