FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpq-dev \
    libzip-dev \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

RUN curl -fsSL https://deb.nodesource.com/setup_22.x -o nodesource_setup.sh \
    && bash nodesource_setup.sh \
    && apt-get install -y nodejs \
    && rm nodesource_setup.sh

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm ci
RUN npm run build

RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear

RUN chown -R www-data:www-data storage bootstrap/cache

RUN a2dismod mpm_event mpm_worker \
    && a2enmod mpm_prefork rewrite
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

CMD sed -i "s/Listen 80/Listen ${PORT:-8080}/" /etc/apache2/ports.conf \
    && sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT:-8080}>/" /etc/apache2/sites-available/000-default.conf \
    && php artisan migrate --force \
    && apache2-foreground
