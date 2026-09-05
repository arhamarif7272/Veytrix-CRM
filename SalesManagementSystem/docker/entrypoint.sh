#!/bin/sh
set -e

# Default port to 10000 if not set by Render
PORT="${PORT:-10000}"
echo "==> Starting Veytrix CRM on port $PORT..."

# Substitute __PORT__ in Nginx default configuration
sed "s/__PORT__/${PORT}/g" /var/www/docker/nginx.conf > /etc/nginx/http.d/default.conf

# Configure PHP-FPM to route worker stdout/stderr and error log to container stderr
sed -i 's/;catch_workers_output = yes/catch_workers_output = yes/g' /usr/local/etc/php-fpm.d/www.conf 2>/dev/null || true
sed -i 's/;decorate_workers_output = no/decorate_workers_output = no/g' /usr/local/etc/php-fpm.d/www.conf 2>/dev/null || true

# Forward Laravel logs to stderr for Render log visibility
export LOG_CHANNEL=stderr

# Ensure storage and bootstrap/cache directories exist
mkdir -p /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/logs \
         /var/www/bootstrap/cache

# Initial permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Run Laravel optimizations
echo "==> Caching configuration, routes, and views..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Re-apply permissions after artisan writes cache files as root
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Diagnostic pre-flight check on health route and login route
echo "==> Running pre-flight self-check..."
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    \$up = \$kernel->handle(Illuminate\Http\Request::create('/up', 'GET'));
    echo '==> Health /up HTTP status: ' . \$up->getStatusCode() . PHP_EOL;
} catch (\Throwable \$e) {
    echo '==> Health /up error: ' . \$e->getMessage() . PHP_EOL;
}

try {
    \$res = \$kernel->handle(Illuminate\Http\Request::create('/login', 'GET'));
    echo '==> Pre-flight /login HTTP status: ' . \$res->getStatusCode() . PHP_EOL;
    if (\$res->getStatusCode() >= 400) {
        echo '==> Pre-flight /login FAILED (body sample):' . PHP_EOL;
        echo substr(\$res->getContent(), 0, 1500) . PHP_EOL;
    }
} catch (\Throwable \$e) {
    echo '==> Pre-flight /login exception: ' . \$e->getMessage() . ' in ' . \$e->getFile() . ':' . \$e->getLine() . PHP_EOL;
    echo \$e->getTraceAsString() . PHP_EOL;
}
"

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
