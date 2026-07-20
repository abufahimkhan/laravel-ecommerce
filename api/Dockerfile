# ============================================
# Stage 1: Build dependencies
# ============================================
FROM php:8.4-cli-alpine AS vendor

WORKDIR /app

# Install build tools and Composer
RUN apk add --no-cache \
    git \
    curl \
    unzip \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    linux-headers \
    $PHPIZE_DEPS \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy only composer files first (Docker layer caching)
COPY composer.json composer.lock ./

# Install dependencies (no scripts, no autoloader yet)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

# Copy the rest of the application
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --classmap-authoritative --no-dev

# ============================================
# Stage 2: PHP-FPM runtime
# ============================================
FROM php:8.4-fpm-alpine AS runtime

# Install system dependencies needed by PHP extensions
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    linux-headers \
    $PHPIZE_DEPS

# Install PostgreSQL client libs
RUN apk add --no-cache postgresql-dev postgresql-client

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    bcmath \
    gd \
    intl \
    zip \
    opcache

# Install Redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer (we'll use it for some operations)
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code + vendor from build stage
COPY --from=vendor /app /var/www/html

# Copy custom PHP configuration
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Set proper ownership for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Expose PHP-FPM port (Nginx will connect here)
EXPOSE 9000

# Default command - run PHP-FPM in foreground
CMD ["php-fpm", "-F"]
