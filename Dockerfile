FROM node:20-alpine AS frontend_builder
WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci || npm install

COPY . .
RUN npm run build

FROM php:8.2-fpm-alpine

RUN apk update && apk upgrade && \
    apk add --no-cache curl zip unzip libpng-dev libxml2-dev postgresql-dev nginx supervisor bash

# Cài đặt extension PHP cần thiết cho WMS
# Bổ sung thêm 'opcache' để RAM nạp sẵn code PHP, boot trong chớp mắt
RUN docker-php-ext-install pdo pdo_pgsql bcmath gd opcache && \
    docker-php-ext-enable opcache

WORKDIR /var/www/html

# Copy Composer từ image chính thức
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tối ưu Cache Docker: Chỉ copy file composer.json trước
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy toàn bộ mã nguồn Backend
COPY . .

# Hứng toàn bộ file CSS/JS đã được biên dịch sang đây
COPY --from=frontend_builder /app/public/build ./public/build

# Phân quyền chuẩn xác cho Nginx/PHP-FPM đọc ghi log
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ==========================================
# KHÓA BỘ NHỚ ĐỆM (Ép Laravel ngậm sẵn cấu hình)
# ==========================================
RUN composer dump-autoload --optimize && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Copy 2 file cấu hình Server 
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

# Chạy Supervisor để kích hoạt song song 2 động cơ Nginx và PHP-FPM
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]