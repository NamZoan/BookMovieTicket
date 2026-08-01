#!/usr/bin/env bash
php artisan storage:link
php artisan migrate --force
php -S 0.0.0.0:$PORT -t public