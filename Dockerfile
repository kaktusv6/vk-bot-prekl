ARG PHP_EXTS="bcmath ctype fileinfo mbstring pdo pgsql pdo_pgsql dom pcntl"
ARG PHP_PECL_EXTS="xdebug"

# We need to build the Composer base to reuse packages we've installed
FROM composer:2.2 as app_base
# We need to declare that we want to use the args in this build step
ARG PHP_EXTS
ARG PHP_PECL_EXTS

# First, create the application directory, and some auxilary directories for scripts and such
RUN mkdir -p /opt/bots/vk-bot /opt/bots/vk-bot/bin

# Next, set our working directory
WORKDIR /opt/bots/vk-bot

# We need to create a composer group and user, and create a home directory for it, so we keep the rest of our image safe,
# And not accidentally run malicious scripts
RUN apk update && apk add  --no-cache libzip-dev zip libpq-dev postgresql-dev

RUN addgroup -S composer \
    && adduser -S composer -G composer \
    && chown -R composer /opt/bots/vk-bot \
    && apk add --virtual build-dependencies --no-cache ${PHPIZE_DEPS} openssl ca-certificates libpq-dev libxml2-dev oniguruma-dev \
    && pecl install ${PHP_PECL_EXTS} \
    && docker-php-ext-enable ${PHP_PECL_EXTS} \
    && docker-php-ext-install -j$(nproc) ${PHP_EXTS} \
    && apk del build-dependencies

# Next we want to switch over to the composer user before running installs.
# This is very important, so any extra scripts that composer wants to run,
# don't have access to the root filesystem.
# This especially important when installing packages from unverified sources.
USER composer

# Copy in our dependency files.
# We want to leave the rest of the code base out for now,
# so Docker can build a cache of this layer,
# and only rebuild when the dependencies of our application changes.
COPY --chown=composer composer.json composer.lock ./

# Install all the dependencies without running any installation scripts.
# We skip scripts as the code base hasn't been copied in yet and script will likely fail,
# as `php artisan` available yet.
# This also helps us to cache previous runs and layers.
# As long as comoser.json and composer.lock doesn't change the install will be cached.
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copy in our actual source code so we can run the installation scripts we need
# At this point all the PHP packages have been installed,
# and all that is left to do, is to run any installation scripts which depends on the code base
COPY --chown=composer . .
# Now that the code base and packages are all available,
# we can run the install again, and let it run any install scripts.
RUN composer install --prefer-dist


FROM php:8.0-alpine as app-cli

# We need to declare that we want to use the args in this build step
ARG PHP_EXTS
ARG PHP_PECL_EXTS

WORKDIR /opt/bots/vk-bot

RUN apk update && apk add  --no-cache libzip-dev zip libpq-dev postgresql-dev
# We need to install some requirements into our image,
# used to compile our PHP extensions, as well as install all the extensions themselves.
# You can see a list of required extensions for Laravel here: https://laravel.com/docs/8.x/deployment#server-requirements
RUN apk add --virtual build-dependencies --no-cache ${PHPIZE_DEPS} openssl ca-certificates libxml2-dev libpq-dev oniguruma-dev && \
    pecl install ${PHP_PECL_EXTS} && \
    docker-php-ext-enable ${PHP_PECL_EXTS} && \
    docker-php-ext-install -j$(nproc) ${PHP_EXTS} && \
    apk del build-dependencies

# Xdebug
COPY ./docker/php/confs/php.ini /usr/local/etc/php/php.ini

# Next we have to copy in our code base from our initial build which we installed in the previous stage
COPY --from=app_base /opt/bots/vk-bot /opt/bots/vk-bot


# We need a stage which contains FPM to actually run and process requests to our PHP application.
FROM php:8.0-fpm-alpine as app

# We need to declare that we want to use the args in this build step
ARG PHP_EXTS
ARG PHP_PECL_EXTS

WORKDIR /opt/bots/vk-bot
RUN apk update && apk add  --no-cache libzip-dev zip libpq-dev postgresql-dev bash

RUN apk add --virtual build-dependencies --no-cache ${PHPIZE_DEPS} openssl ca-certificates libxml2-dev libpq-dev oniguruma-dev && \
    pecl install ${PHP_PECL_EXTS} && \
    docker-php-ext-enable ${PHP_PECL_EXTS} && \
    docker-php-ext-install -j$(nproc) ${PHP_EXTS} && \
    apk del build-dependencies

# Xdebug
COPY ./docker/php/confs/php.ini /usr/local/etc/php/php.ini

# We have to copy in our code base from our initial build which we installed in the previous stage
COPY --from=app_base --chown=www-data /opt/bots/vk-bot /opt/bots/vk-bot

# Scripts for entrypoint
COPY ./docker/php/scripts/deploy.sh /tmp/

RUN chmod +x /tmp/deploy.sh

# As FPM uses the www-data user when running our application,
# we need to make sure that we also use that user when starting up,
# so our user "owns" the application when running
USER  www-data

COPY --from=app_base --chown=www-data /opt/bots/vk-bot/.env /opt/bots/vk-bot/.env


FROM php:8.0-alpine as app-queue

# We need to declare that we want to use the args in this build step
ARG PHP_EXTS
ARG PHP_PECL_EXTS

WORKDIR /opt/bots/vk-bot

RUN apk update && apk add  --no-cache libzip-dev zip libpq-dev postgresql-dev bash
# We need to install some requirements into our image,
# used to compile our PHP extensions, as well as install all the extensions themselves.
# You can see a list of required extensions for Laravel here: https://laravel.com/docs/8.x/deployment#server-requirements
RUN apk add --virtual build-dependencies --no-cache ${PHPIZE_DEPS} openssl ca-certificates libxml2-dev libpq-dev oniguruma-dev && \
    pecl install ${PHP_PECL_EXTS} && \
    docker-php-ext-enable ${PHP_PECL_EXTS} && \
    docker-php-ext-install -j$(nproc) ${PHP_EXTS} && \
    apk del build-dependencies

# Xdebug
COPY ./docker/php/confs/php.ini /usr/local/etc/php/php.ini

# We have to copy in our code base from our initial build which we installed in the previous stage
COPY --from=app_base --chown=www-data /opt/bots/vk-bot /opt/bots/vk-bot

# Queue scrtip
COPY ./docker/php/scripts/queue.sh /tmp/

RUN chmod +x /tmp/queue.sh

# As FPM uses the www-data user when running our application,
# we need to make sure that we also use that user when starting up,
# so our user "owns" the application when running
USER  www-data

COPY --from=app_base --chown=www-data /opt/bots/vk-bot/.env /opt/bots/vk-bot/.env

CMD php artisan queue:listen --tries=3


# We need an nginx container which can pass requests to our FPM container,
# as well as serve any static content.
FROM nginx:1.21-alpine as web

WORKDIR /opt/bots/vk-bot

COPY ./public ./public

# We need to add our NGINX template to the container for startup,
# and configuration.
COPY docker/nginx/confs/default.conf.template /etc/nginx/templates/default.conf.template
