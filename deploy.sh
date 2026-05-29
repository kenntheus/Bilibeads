#!/bin/sh
set -e

echo "=== Bilibeads Deploy ==="

# Fly.io sets DATABASE_URL when Postgres is attached.
# Laravel reads it natively via the 'url' key in config/database.php.
# We just need to ensure DB_CONNECTION points to pgsql.
if [ -n "$DATABASE_URL" ]; then
  export DB_CONNECTION=pgsql
fi

# Generate app key if not already set
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

# Clear any stale cached config so env vars are always read fresh
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run pending migrations
php artisan migrate --force

# Cache routes only (not config — lets DATABASE_URL be read from env each boot)
php artisan route:cache

# Create storage symlink if not already present
php artisan storage:link 2>/dev/null || true

echo "=== Deploy complete ==="
