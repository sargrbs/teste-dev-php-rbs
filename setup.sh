#!/bin/bash

# Configure
cd /var/www/html

echo "==============================="
echo "[1] RUNNING COMPOSER INSTALL..."
composer install --no-interaction -vvv

echo "==============================="
echo "[2] KEY GENERATE..."
php artisan key:generate

echo "==============================="
echo "[4] START SERVER..."
php artisan octane:start --host=0.0.0.0 --port=8000
