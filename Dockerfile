# We can enable have "base", "xdebug", "profiler"
ARG IMAGE_TYPE=base

# Used for local development
FROM php:8.0-alpine AS base

ENV PATH "$PATH:vendor/bin/"

RUN set -x \
    && apk add --no-cache binutils git sudo 1>/dev/null \
    && apk add --no-cache --virtual .build-deps autoconf pkgconf make g++ gcc 1>/dev/null \
    # install xdebug (for testing with code coverage), but do not enable it
    && pecl install xdebug-3.0.0 1>/dev/null \
    && apk del .build-deps \
    && php -v \
    && php -m

ENV COMPOSER_HOME "/tmp/composer"
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN set -x \
    && mkdir --parents --mode=777 /src ${COMPOSER_HOME}/cache/repo ${COMPOSER_HOME}/cache/files \
    && ln -s /usr/bin/composer /usr/bin/c \
    && composer --version

FROM base AS xdebug
RUN set -x \
    && docker-php-ext-enable xdebug 1>/dev/null \
    && echo "xdebug.mode = coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request = trigger" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

FROM xdebug AS profiler
RUN set -x \
    && echo "xdebug.mode = profile" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.output_dir = /app/profiler" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

FROM ${IMAGE_TYPE}
WORKDIR /app
