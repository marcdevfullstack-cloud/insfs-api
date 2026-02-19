FROM php:8.3-cli-alpine

# ── Dépendances Alpine + extensions PHP ──
RUN apk add --no-cache \
    git \
    curl \
    unzip \
    libpng-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    libxml2-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        bcmath \
        gd \
        zip \
        exif \
        intl \
        dom \
        xml

# ── Composer ──
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ── Dépendances PHP ──
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# ── Code source ──
COPY . .

# ── Permissions ──
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions \
        storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan storage:link 2>/dev/null; \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
