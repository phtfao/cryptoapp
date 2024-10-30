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
RUN set -eux; \
    composer install --prefer-dist --no-dev --no-autoloader --no-plugins --no-scripts --no-progress; \
    composer clear-cache

COPY --link ./ ./

COPY <<'ENTRYPOINT' /usr/bin/entrypoint
#!/bin/sh -e
php artisan migrate
php artisan serve --host=0.0.0.0 --port=$PORT
ENTRYPOINT

RUN set -eux; \
    composer dump-autoload --classmap-authoritative; \
    chmod +x /usr/bin/entrypoint; \
    php artisan key:generate; \
    chown www-data:www-data -R ./;

EXPOSE 80

ENTRYPOINT ["/usr/bin/entrypoint"]

USER www-data
