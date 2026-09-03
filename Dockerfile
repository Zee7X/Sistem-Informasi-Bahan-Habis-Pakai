FROM php:8.2-apache

# System dependencies + PHP extensions
# (pdo_mysql untuk Aiven MySQL, zip/gd/intl untuk dompdf & Laravel, bcmath untuk komputasi)
RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libicu-dev \
        zip \
        unzip \
        git \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql zip gd intl bcmath opcache

# Apache: document root -> public, enable .htaccess rewrite
RUN a2enmod rewrite headers
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node.js untuk build assets React/Inertia via Vite
COPY --from=node:22 /usr/local/bin/node /usr/local/bin/node
COPY --from=node:22 /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

WORKDIR /var/www/html

# Install vendor dependencies first (better layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress

# Install node dependencies (needed by vite build)
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

# Application code
COPY . .

# Build frontend assets lalu buang node_modules supaya image ramping
RUN npm run build \
    && rm -rf node_modules \
    && composer dump-autoload --optimize \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x start.sh

EXPOSE 80

CMD ["bash", "start.sh"]
