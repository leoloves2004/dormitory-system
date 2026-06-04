#!/usr/bin/env bash

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Installing NPM dependencies..."
npm install

echo "Building frontend assets..."
npm run build

echo "Caching Laravel config..."
php artisan config:cache

echo "Caching Laravel routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force