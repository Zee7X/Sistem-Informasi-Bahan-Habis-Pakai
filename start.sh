#!/bin/bash
set -e

# Render forwards traffic to the port from $PORT (default 10000)
PORT="${PORT:-10000}"
sed -i -E "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf

# Fail loudly if the Aiven CA certificate is configured but missing
if [ -n "${MYSQL_ATTR_SSL_CA}" ] && [ ! -f "${MYSQL_ATTR_SSL_CA}" ]; then
    echo "WARNING: MYSQL_ATTR_SSL_CA=${MYSQL_ATTR_SSL_CA} tidak ditemukan. Commit ca.pem dari Aiven console ke root repo." >&2
fi

# Fallback only — set APP_KEY in the Render dashboard (php artisan key:generate --show)
# so sessions survive redeploys. A generated key changes on every restart.
if [ -z "${APP_KEY:-}" ]; then
    export APP_KEY="base64:$(openssl rand -base64 32)"
    echo "WARNING: APP_KEY belum diisi di dashboard, memakai key sementara (sesi hangus tiap restart)." >&2
fi

run_artisan() {
    local attempts=0
    until php "$1" "${@:2}"; do
        attempts=$((attempts + 1))
        if [ "$attempts" -ge 3 ]; then
            echo " artisan command failed after 3 attempts" >&2
            exit 1
        fi
        echo "Retrying in 5s..." >&2
        sleep 5
    done
}

# Migrate + seed (seeders are idempotent, safe on every boot)
run_artisan artisan migrate --force
run_artisan artisan db:seed --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting apache on port ${PORT}..."
exec apache2-foreground
