FROM php:8.3-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
    && rm -rf /var/lib/apt/lists/*


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .


RUN composer install --working-dir=/var/www/html/backend \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader


RUN chown -R www-data:www-data \
    /var/www/html/backend/storage \
    /var/www/html/backend/bootstrap/cache

WORKDIR /var/www/html/backend


EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]