FROM php:8.2-fpm-alpine

RUN apk update && apk upgrade

RUN apk add --no-cache curl git zip unzip bash

RUN apk add --no-cache libpng-dev libxml2-dev postgresql-dev

RUN apk add --no-cache nginx supervisor

RUN docker-php-ext-install pdo pdo_pgsql bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN apk add --no-cache nodejs npm && npm install && npm run build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD php artisan config:clear && php artisan view:clear && php artisan migrate:fresh --force && php artisan db:seed --class=InboundSeeder --force && php artisan serve --host=0.0.0.0 --port=80