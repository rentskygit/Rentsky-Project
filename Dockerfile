FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo \
    && docker-php-ext-install zip \
    && docker-php-ext-install opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN { \
        echo 'upload_max_filesize = 25M'; \
        echo 'post_max_size = 500M'; \
        echo 'memory_limit = 512M'; \
        echo 'max_file_uploads = 50'; \
        echo 'max_execution_time = 300'; \
        echo 'max_input_time = 300'; \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=256'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.save_comments=1'; \
    } > /usr/local/etc/php/conf.d/zz-production.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/bootstrap/cache

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-scripts --no-autoloader --no-dev

COPY . .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage/framework/views

RUN composer dump-autoload --optimize --no-scripts --no-dev
RUN composer run-script post-autoload-dump || true

EXPOSE 9000
CMD ["php-fpm"]