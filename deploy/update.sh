#!/usr/bin/env bash
# Pulls the latest code and redeploys. Run this on the server after pushing
# new commits to main:
#   ssh into the server, then: bash /var/www/ecomm-team/deploy/update.sh

set -euo pipefail

APP_DIR="/var/www/ecomm-team"
cd "$APP_DIR"

echo "== Pulling latest code =="
sudo git pull origin main

echo "== Installing dependencies =="
sudo composer install --no-dev --optimize-autoloader --no-interaction
sudo npm ci
sudo npm run build

echo "== Running migrations & rebuilding caches =="
sudo php artisan migrate --force
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache

echo "== Fixing permissions =="
sudo chown -R www-data:www-data "$APP_DIR"

echo "== Restarting services =="
sudo systemctl restart php8.3-fpm ecomm-queue
sudo systemctl reload nginx

echo "Done."
