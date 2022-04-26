FROM php:8.0-fpm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions
RUN install-php-extensions bcmath mcrypt pgsql pdo_pgsql gd xdebug @composer

COPY ./scripts/deploy.sh /tmp/

RUN chmod +x /tmp/deploy.sh

RUN chown -R www-data:www-data /var/www

WORKDIR /var/www

ENTRYPOINT bash -c /tmp/deploy.sh
CMD []
