FROM php:7.2-cli-alpine

RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        postgresql-dev \
    ;

RUN set -eux; \
    docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
    ;

RUN set -eux; \
    RUN_DEPS="$(scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions | tr ',' '\n' | sort -u | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }')"; \
    apk add --no-cache $RUN_DEPS; \
    apk del .build-deps

WORKDIR /app

COPY --link --from=composer /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --link composer.json composer.lock ./
COPY --link database/ database/
RUN set -eux; \
    composer install --prefer-dist --no-dev --classmap-authoritative --no-scripts --no-progress; \
    composer clear-cache

COPY --link ./ ./

RUN set -eux; \
    php artisan key:generate

EXPOSE 80

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=${PORT}"]
