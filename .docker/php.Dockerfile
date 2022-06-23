FROM php:7.4-fpm-alpine as production-php-alpine

ENV BUILD_DEPS \
    curl curl-dev \
    yaml yaml-dev

ENV YAML_VERSION 2.2.2

RUN apk add --no-cache --virtual .build-deps \
    g++ make autoconf ${BUILD_DEPS}

# Install and enable yaml extension support to php
RUN apk add --update yaml yaml-dev
RUN pecl channel-update pecl.php.net
RUN pecl install yaml-${YAML_VERSION} && docker-php-ext-enable yaml







