#!/usr/bin/env bash
# Provisions a fresh Ubuntu 24.04 Oracle Cloud VM to run this app:
# Nginx + PHP-FPM 8.3 + MySQL + Composer + Node (for the Vite build).
#
# Run as a regular sudo-capable user (e.g. `ubuntu`), NOT as root directly:
#   bash provision.sh
#
# Safe to re-run — apt/composer/npm steps are idempotent. Re-running does NOT
# reset the database or overwrite an existing .env.

set -euo pipefail

APP_DIR="/var/www/ecomm-team"
REPO_URL="https://github.com/liamflores-09/ecomm-team.git"
DB_NAME="ecomm"
DB_USER="ecomm"
PHP_VERSION="8.3"

echo "== Updating system packages =="
sudo apt-get update -y
sudo apt-get upgrade -y

echo "== Installing Nginx, PHP ${PHP_VERSION}, MySQL, Composer, Node =="
sudo apt-get install -y \
    nginx \
    mysql-server \
    php${PHP_VERSION}-fpm php${PHP_VERSION}-mysql php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-xml php${PHP_VERSION}-curl php${PHP_VERSION}-zip \
    php${PHP_VERSION}-gd php${PHP_VERSION}-bcmath php${PHP_VERSION}-sqlite3 \
    unzip git curl

if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi

if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
    sudo apt-get install -y nodejs
fi

echo "== Opening firewall ports (ufw + the iptables rules Oracle images ship with) =="
sudo apt-get install -y ufw
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable
# Oracle's stock Ubuntu images also ship a restrictive iptables ruleset via
# netfilter-persistent that ufw does not manage — without this, 80/443 stay
# blocked at the OS level even after opening them in the Oracle console.
sudo iptables -I INPUT -p tcp --dport 80 -j ACCEPT
sudo iptables -I INPUT -p tcp --dport 443 -j ACCEPT
sudo netfilter-persistent save 2>/dev/null || true

echo "== Setting up MySQL database =="
DB_PASS="$(openssl rand -base64 24 | tr -d '/+=' | head -c 32)"
sudo mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
sudo mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

echo "== Cloning the app =="
if [ ! -d "$APP_DIR" ]; then
    sudo git clone "$REPO_URL" "$APP_DIR"
fi
cd "$APP_DIR"
sudo git config --global --add safe.directory "$APP_DIR"

echo "== Installing PHP & JS dependencies =="
sudo composer install --no-dev --optimize-autoloader --no-interaction
sudo npm ci
sudo npm run build

echo "== Configuring .env =="
if [ ! -f .env ]; then
    sudo cp .env.example .env
    sudo sed -i "s/^APP_ENV=.*/APP_ENV=production/" .env
    sudo sed -i "s/^APP_DEBUG=.*/APP_DEBUG=false/" .env
    sudo sed -i "s#^APP_URL=.*#APP_URL=http://$(curl -s ifconfig.me)#" .env
    sudo sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" .env
    sudo sed -i "s/^# DB_HOST=.*/DB_HOST=127.0.0.1/" .env
    sudo sed -i "s/^# DB_PORT=.*/DB_PORT=3306/" .env
    sudo sed -i "s/^# DB_DATABASE=.*/DB_DATABASE=${DB_NAME}/" .env
    sudo sed -i "s/^# DB_USERNAME=.*/DB_USERNAME=${DB_USER}/" .env
    sudo sed -i "s/^# DB_PASSWORD=.*/DB_PASSWORD=${DB_PASS}/" .env
    echo ""
    echo "!! Generated DB password (saved in .env, shown once): ${DB_PASS}"
fi

sudo php artisan key:generate --force
sudo php artisan storage:link
sudo php artisan migrate --force
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache

echo "== Fixing permissions =="
sudo chown -R www-data:www-data "$APP_DIR"
sudo find "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" -type d -exec chmod 775 {} \;
sudo find "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" -type f -exec chmod 664 {} \;

echo "== Installing Nginx site =="
sudo cp deploy/nginx-ecomm.conf /etc/nginx/sites-available/ecomm
sudo sed -i "s/PHP_VERSION_PLACEHOLDER/${PHP_VERSION}/" /etc/nginx/sites-available/ecomm
sudo ln -sf /etc/nginx/sites-available/ecomm /etc/nginx/sites-enabled/ecomm
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
sudo systemctl enable nginx php${PHP_VERSION}-fpm mysql

echo "== Installing queue worker service =="
sudo cp deploy/ecomm-queue.service /etc/systemd/system/ecomm-queue.service
sudo systemctl daemon-reload
sudo systemctl enable --now ecomm-queue

echo ""
echo "=================================================================="
echo "Done. Visit: http://$(curl -s ifconfig.me)"
echo "=================================================================="
