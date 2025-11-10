FROM php:8.2-zts-alpine3.17

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer clear-cache \
    && composer -V

RUN apk add bash git

# Install Xdebug for code coverage
RUN apk add --no-cache $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

WORKDIR /app
