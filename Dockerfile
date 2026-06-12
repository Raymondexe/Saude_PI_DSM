FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pgsql pdo_pgsql

RUN a2enmod rewrite

COPY . /var/www/html/

EXPOSE 80
