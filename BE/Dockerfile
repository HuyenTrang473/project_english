FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev unzip git curl \
    && docker-php-ext-install pdo pdo_mysql zip

COPY . /var/www/html

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php
RUN php composer.phar install --no-dev

RUN cp .env.example .env
RUN php artisan key:generate

CMD php artisan serve --host=0.0.0.0 --port=10000
