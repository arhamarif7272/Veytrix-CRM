#!/bin/sh
set -e

PORT="${PORT:-10000}"

echo "==> Starting Veytrix CRM on port $PORT..."

# Generate Nginx configuration
sed "s/__PORT__/${PORT}/g" \
    /var/www/docker/nginx.conf \
    > /etc/nginx/http.d/default.conf

echo "==> Testing Nginx configuration..."
nginx -t

# PHP-FPM logging
sed -i 's/;catch_workers_output = yes/catch_workers_output = yes/g' \
    /usr/local/etc/php-fpm.d/www.conf 2>/dev/null || true

sed -i 's/;decorate_workers_output = no/decorate_workers_output = no/g' \
    /usr/local/etc/php-fpm.d/www.conf 2>/dev/null || true

# Laravel logs
export LOG_CHANNEL=stderr

# Laravel directories
mkdir -p \
    /var/www/storage/framework/cache/data \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/storage/logs \
    /var/www/bootstrap/cache

# Permissions
chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache

chmod -R 775 \
    /var/www/storage \
    /var/www/bootstrap/cache

# Laravel optimization
echo "==> Caching configuration, routes, and views..."

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Restore permissions
chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache

chmod -R 775 \
    /var/www/storage \
    /var/www/bootstrap/cache

# Optional seeders
if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo "==> Running database seeders..."
    php artisan db:seed --force || true
fi

# Start PHP-FPM
echo "==> Starting PHP-FPM..."
php-fpm -D

sleep 1

echo "==> Starting Nginx on port $PORT..."
exec nginx -g 'daemon off;'
