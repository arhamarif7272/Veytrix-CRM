#!/bin/sh
set -e

# Default port to 10000 if not set by Render
PORT="${PORT:-10000}"
echo "==> Starting Veytrix CRM on port $PORT..."

# Substitute __PORT__ in Nginx default configuration
sed "s/__PORT__/${PORT}/g" /var/www/docker/nginx.conf > /etc/nginx/http.d/default.conf

# Ensure storage and bootstrap/cache directories exist and have proper permissions
mkdir -p /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/logs \
         /var/www/bootstrap/cache

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Run Laravel optimizations
echo "==> Caching configuration, routes, and views..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Seed default settings and administrative accounts if needed
if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo "==> Running database seeders..."
    php artisan db:seed --force || true
fi

# Start PHP-FPM in daemon mode
echo "==> Starting PHP-FPM..."
php-fpm -D

# Start Nginx in foreground to keep container running
echo "==> Starting Nginx..."
exec nginx -g 'daemon off;'
