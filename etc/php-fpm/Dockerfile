FROM php:7.2-fpm-alpine

# set timezone
RUN rm /etc/localtime && \
    ln -s /usr/share/zoneinfo/Europe/London /etc/localtime && \
    "date"

# install packages and build tools
RUN echo http://dl-2.alpinelinux.org/alpine/edge/community/ >> /etc/apk/repositories

RUN apk --no-cache add \
    gcc musl-dev make curl bash openssl openssl-dev autoconf shadow

# install latest version php redis extension
RUN pecl install redis-4.2.0 \
    && pecl install xdebug \
    && pecl install ast \
    && rm -rf /tmp/pear

RUN docker-php-ext-enable redis ast xdebug

RUN docker-php-ext-install bcmath

RUN echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

# update default (82) uid for www-data
RUN set -x \
    && usermod -u 1000 www-data \
    && groupmod -g 1000 www-data

# install composer
RUN wget https://getcomposer.org/download/1.2.4/composer.phar -O /usr/local/bin/composer \
    && chmod a+rx /usr/local/bin/composer

ENV PATH /root/.composer/vendor/bin:$PATH

# hirak/prestissimo enables parallelization of composer dependencies download
RUN composer global require hirak/prestissimo "0.3.7" --no-interaction --quiet

# make storage directory writable by php
RUN mkdir -p /var/www/app/storage/logs \
    && chmod -R 775 /var/www/app/storage/

WORKDIR /var/www/app

EXPOSE 9000
