# syntax=docker/dockerfile:1.7-labs
FROM php:8.3-alpine as base
WORKDIR /app

ENV COMPOSER_MEMORY_LIMIT=-1
######################################################
# Step 1 | Install Dependencies
######################################################
# Download  install-php-extension
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apk update \
    && apk add --no-cache curl git unzip openssl tar ca-certificates \
    && install-php-extensions gd bcmath pdo_mysql zip intl opcache pcntl redis swoole @composer \
    && rm -rf /var/cache/apk/*

RUN chown -R www-data:www-data /app
USER www-data

######################################################
# Copy Configuration
######################################################
COPY .github/docker/php/opcache.ini $PHP_INI_DIR/conf.d/opcache.ini
COPY .github/docker/php/php.ini $PHP_INI_DIR/conf.d/php.ini
######################################################
# Local Stage
######################################################
FROM base as local
######################################################
# Build Ziggy Package - Vite needs ziggy package available
######################################################
FROM base as vite-vendor-build
WORKDIR /app
RUN COMPOSER_ALLOW_SUPERUSER=1 | composer require tightenco/ziggy:^2 --ignore-platform-reqs
######################################################
# NodeJS Stage
######################################################
FROM node:20-alpine as vite
WORKDIR /app
COPY package.json package-lock.json tailwind.config.js vite.config.js postcss.config.js ./
RUN npm install --force
COPY ./resources /app/resources
COPY --from=vite-vendor-build /app/vendor/tightenco/ziggy /app/vendor/tightenco/ziggy
RUN npm run build
######################################################
# Production Stage
######################################################
FROM base as production
COPY --chown=www-data:www-data composer.json composer.lock /app/
RUN composer install --no-dev --optimize-autoloader --no-cache --no-scripts
COPY --chown=www-data:www-data . /app/
RUN composer dump-autoload --optimize
COPY --from=vite --chown=www-data:www-data /app/public/build ./public/build
CMD sh -c "php artisan octane:start --host=0.0.0.0 --port=80"
