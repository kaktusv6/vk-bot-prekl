FROM php:8.0-cli

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions
RUN install-php-extensions bcmath mcrypt pgsql pdo_pgsql gd xdebug @composer

ADD ./scripts/queue.sh /tmp/

RUN chmod +x /tmp/queue.sh

RUN chown -R www-data:www-data /var/www

WORKDIR /var/www

ENTRYPOINT bash -c /tmp/queue.sh
CMD []
