#!/bin/bash

composer install
composer dump-autoload
php artisan migrate
php artisan db:seed

php-fpm
