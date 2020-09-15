FROM php:7.4-fpm

ARG UID
ARG GID

USER root

COPY --chown=www-data:www-data ./usr /usr

RUN apt-get update && \
    pecl channel-update pecl.php.net && \
    pecl install apcu && \
    docker-php-ext-enable apcu \
    && apt-get install -y \
            libzip-dev \
            zip \
    && apt-get install -y \
            libpq-dev \
            libfreetype6-dev \
            libmcrypt-dev \
            libicu-dev\
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql pgsql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install zip

RUN usermod -u $UID www-data \
    && groupmod -g $GID www-data \
    && rm /var/log/lastlog \
    && rm /var/log/faillog

WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet
ENV COMPOSER_ALLOW_SUPERUSER 1

USER www-data