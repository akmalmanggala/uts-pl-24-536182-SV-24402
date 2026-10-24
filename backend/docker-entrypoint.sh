#!/bin/sh
set -e

# Ensure storage directories exist
mkdir -p /app/storage/framework/cache/data
mkdir -p /app/storage/framework/sessions
mkdir -p /app/storage/framework/views
mkdir -p /app/storage/logs
mkdir -p /app/bootstrap/cache
chmod -R 777 /app/storage /app/bootstrap/cache

# If DB is configured, wait for it then run migrations & seeders
if [ -n "$DB_HOST" ]; then
    echo "Checking database connection to $DB_HOST:$DB_PORT..."
    until nc -z -v -w30 "$DB_HOST" "${DB_PORT:-3306}" 2>/dev/null; do
        echo "Waiting for database ($DB_HOST)..."
        sleep 2
    done
    echo "Database is ready! Running migrations and seeders..."
    php artisan migrate --force --seed || true
fi

echo "Starting FrankenPHP server on port 8000..."
exec "$@"
