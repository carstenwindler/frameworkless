FROM php:8-fpm

# ARG USER=root

WORKDIR /app

RUN apt-get update -y \
    && apt-get install -qqy zlib1g-dev libzip-dev libicu-dev g++ wget gnupg2 openssh-client curl libpng-dev git libxml2-dev \
    && docker-php-source extract \
    && pecl install xdebug redis \
    && docker-php-ext-enable xdebug redis \
    && docker-php-source delete \
    && docker-php-ext-install pdo_mysql soap intl zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /tmp/*
    # \ && adduser ${USER}

# USER ${USER}