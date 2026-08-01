#!/usr/bin/env bash
chmod -R 777 storage bootstrap/cache
php artisan storage:link || true
php artisan config:clear
php artisan view:clear
php artisan migrate --force
php -S 0.0.0.0:$PORT -t public