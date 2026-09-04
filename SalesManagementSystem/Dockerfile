FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www

# Install system dependencies & build tools
RUN apk update && apk add --no-cache \
    nginx \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    openssl-dev \
    autoconf \
    gcc \
    g++ \
    make \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd bcmath zip opcache pdo && \
    pecl install mongodb && \
    docker-php-ext-enable mongodb && \
    apk del autoconf gcc g++ make linux-headers

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure production PHP settings & Opcache
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini

# Copy application files
COPY . /var/www

# Install PHP dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Set directory permissions for Laravel
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache && \
    chmod +x /var/www/docker/entrypoint.sh

# Render dynamically sets $PORT (defaults to 10000)
EXPOSE 10000

# Set entrypoint to our startup orchestrator
ENTRYPOINT ["/var/www/docker/entrypoint.sh"]
