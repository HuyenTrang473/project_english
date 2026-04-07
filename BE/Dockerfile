FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV PORT=10000

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git curl unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && a2enmod rewrite \
    && sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf \
    && sed -ri \
        -e "s!<VirtualHost \*:80>!<VirtualHost *:${PORT}>!" \
        -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" \
        /etc/apache2/sites-available/000-default.conf \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# quyền
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# storage link
RUN php artisan storage:link || true

EXPOSE 10000

CMD ["apache2-foreground"]
