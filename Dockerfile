FROM php:8.3-apache

RUN apt-get update
RUN apt-get install --yes --force-yes \
    cron \
    g++ \
    gettext \
    libicu-dev \
    openssl \
    libc-client-dev \
    libkrb5-dev  \
    libxml2-dev \
    libfreetype6-dev \
    libgd-dev \
    libmcrypt-dev \
    bzip2 \
    libbz2-dev \
    libtidy-dev \
    libcurl4-openssl-dev \
    libz-dev \
    libmemcached-dev \
    libxslt-dev \
    libzip-dev

RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli

RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-enable pdo pdo_mysql

RUN docker-php-ext-install zip
RUN docker-php-ext-enable zip

RUN a2enmod rewrite

# XDebug
RUN yes | pecl install xdebug \
        && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
      && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini \
      && echo "xdebug.client_host = host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
      && docker-php-ext-enable xdebug

RUN service apache2 restart
