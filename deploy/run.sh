#!/bin/sh
set -e
php artisan migrate --force
service supervisor start
service nginx restart
php-fpm