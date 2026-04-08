FROM php:8.3-fpm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
    @composer \
    exif \
    intl \
    mysqli \
    pdo_mysql \
    xdebug \
    zip \
    pcntl

RUN apt-get update && apt-get install -y \
  default-mysql-client \
  default-libmysqlclient-dev

COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/php.ini

WORKDIR /var/www