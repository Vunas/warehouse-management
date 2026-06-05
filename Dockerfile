FROM node:20-alpine AS frontend_builder
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci || npm install
COPY . .
RUN npm run build

FROM php:8.2-fpm-alpine

RUN apk update && apk upgrade && \
    apk add --no-cache curl zip unzip libpng-dev libxml2-dev postgresql-dev nginx supervisor bash

RUN docker-php-ext-install pdo pdo_pgsql bcmath gd opcache && \
    { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY . .
COPY --chown=www-data:www-data --from=frontend_builder /app/public/build /var/www/html/public/build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["sh", "-c", "echo $APIPING && for url in $APIPING; do echo ping:$url; curl -v -m 5 \"$url\"; done; /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf"]