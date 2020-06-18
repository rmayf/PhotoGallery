FROM composer:latest as build
COPY composer.json .
RUN composer install --ignore-platform-reqs --no-autoloader

FROM php:7.0-apache as production
RUN apt-get update && apt-get install -y \
    imagemagick
RUN pecl install mongodb-1.5.3 \
    && docker-php-ext-enable mongodb
COPY --from=build /app/vendor /var/www/html/
COPY www/ /var/www/html/
EXPOSE 80
