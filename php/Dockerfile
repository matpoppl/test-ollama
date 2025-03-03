FROM php:8.4-fpm

RUN apt update && apt install -y \
    libcurl4-openssl-dev \
    libzip-dev \
    libpq-dev \
    && pecl install xdebug \
    && docker-php-ext-install zip pcntl pdo_mysql pdo_pgsql \
    && docker-php-ext-enable zip pcntl pdo_mysql pdo_pgsql xdebug

RUN pecl install --configureoptions='enable-redis-igbinary="no" enable-redis-lzf="no" enable-redis-zstd="no" enable-redis-msgpack="no" enable-redis-lz4="no" with-liblz4="yes"' redis \
     && docker-php-ext-enable redis

RUN <<EOF
cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
EOF

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
