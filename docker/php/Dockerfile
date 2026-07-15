FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    mbstring

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html