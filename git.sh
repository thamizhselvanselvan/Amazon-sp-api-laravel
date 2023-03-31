#!/usr/bin/env bash

echo "GIT hook started"

npm install

composer install --ignore-platform-reqs

#php artisan self-diagnosis

php artisan migrate
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear

echo "GIT hook finished"
