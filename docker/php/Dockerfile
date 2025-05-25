FROM php:8.2-fpm

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Installation des extensions PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Configuration PHP-FPM
RUN echo "request_terminate_timeout = 300" >> /usr/local/etc/php-fpm.d/www.conf && \
    echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/docker-php-ext-sodium.ini && \
    echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/docker-php-ext-sodium.ini

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier composer.json et composer.lock d'abord
COPY composer.json composer.lock ./

# Configurer Git pour éviter l'erreur de propriété
RUN git config --global --add safe.directory /var/www/html

# Installer les dépendances PHP (sans les dev dependencies)
RUN composer install --no-dev --no-scripts --no-autoloader

# Copier le reste des fichiers
COPY . .

# Finaliser l'installation de composer
RUN composer dump-autoload --optimize

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]