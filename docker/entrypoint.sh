#!/bin/bash

if [ ! -f "vendor/autoload.php" ]; then
  composer install --no-ansi --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader
fi


php artisan migrate
php artisan clear
php artisan optimize:clear

php artisan serve --host 0.0.0.0