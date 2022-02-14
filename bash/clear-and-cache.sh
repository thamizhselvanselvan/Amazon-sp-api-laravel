#!/bin/sh

php artisan down

php artisan cache:clear
php artisan config:clear
php artisan debugbar:clear
php artisan event:clear
php artisan optimize:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan event:cache
php artisan permission:cache-reset
php artisan route:cache
php artisan view:cache

php artisan clear-compiled
composer dump-autoload

php artisan up