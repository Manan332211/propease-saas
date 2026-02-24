# 1. Upgrade to PHP 8.3 (Standard for modern Laravel 11)
FROM php:8.3-apache

# 2. Fix the Memory and Permissions crashes
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1

# 3. Install all required Linux packages AND the PHP zip extension
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    libonig-dev \
    libxml2-dev \
    curl \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Clear out cache to keep the server image small
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache routing rules
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory inside the server
WORKDIR /var/www/html

# Copy all your Laravel code into the server
COPY . .

# 4. The Ultimate Install Command (Ignores platform requirements to prevent version crashes)
RUN composer install --optimize-autoloader --no-dev --no-scripts --ignore-platform-reqs

# Give the server permission to write to Laravel's cache and storage folders
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Tell Apache to serve the 'public' folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Open port 80 for web traffic
EXPOSE 80