FROM php:8.2-fpm

# Install system dependencies and PHP extensions (including Postgres driver)
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Install Node.js for building frontend assets (Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy dependency files first for better Docker layer caching
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-scripts

COPY package.json package-lock.json* ./
RUN npm install

# Copy the rest of the app
COPY . .

# Build frontend assets
RUN npm run build

# Finish Composer install (in case of post-install scripts needing full app code)
RUN composer dump-autoload --optimize

# Set correct permissions for Laravel's storage and cache
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

# Run migrations, cache config, then start the server
CMD php artisan migrate --force && php artisan db:seed --force && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=8080
