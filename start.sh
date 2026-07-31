#!/usr/bin/env bash
php artisan storage:link
php -S 0.0.0.0:10000 -t public