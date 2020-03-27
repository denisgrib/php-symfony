#!/usr/bin/env bash

cd /var/www/html

composer install

php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration