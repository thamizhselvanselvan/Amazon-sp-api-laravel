#!/bin/sh
php artisan down
composer install
php artisan migrate --force
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan up