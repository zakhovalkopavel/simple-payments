FROM php:8.3-rc-fpm-alpine

LABEL authors="Pavlo ZAKHOVALKO, zakhovalko.pavel@gmail.com"
LABEL description="PHP-fpm alpine with extra modules, composer, symfony and cron"

ENV COMPOSER_HOME /usr/local/share/composer
ENV PATH $PATH:$COMPOSER_HOME/vendor/bin/

RUN apk update \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && apk add bash build-base wget git curl libmcrypt-dev libzip-dev zip \
    openssl-dev gmp-dev icu-dev busybox nano
RUN docker-php-ext-install bcmath intl bz2 pdo_mysql

RUN pecl install redis && docker-php-ext-enable redis
RUN pecl install zip && docker-php-ext-enable zip

RUN apk add sudo \
    && curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sudo -E bash \
    && apk add symfony-cli

# Add xdebug
RUN apk add --update linux-headers \
    && pecl install xdebug && docker-php-ext-enable xdebug \
    && apk del -f .build-deps

# Configure Xdebug
COPY ./docker/etc/php/xdebug/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY ./docker/etc/crontabs/ /vsar/spool/cron/crontabs/
RUN echo "=== Check php and install tools ===" \
    \
    && php -v \
    && php -m \
    && php --ini \
    \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version --no-plugins --no-scripts 2>/dev/null \
    \
    && composer require --dev php-parallel-lint/php-parallel-lint \
    \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www \
    && chown -R root:root /var/spool/cron/crontabs \
    \
    && wget https://phar.phpunit.de/phpunit.phar && chmod +x phpunit.phar && mv phpunit.phar /usr/local/bin/phpunit \
    && phpunit --version \
    \
    && echo "=== The end ==="
COPY ./docker/entrypoint.bash /usr/sbin/entrypoint.bash
RUN chmod a+x /usr/sbin/entrypoint.bash
ENTRYPOINT /usr/sbin/entrypoint.bash

EXPOSE 9000