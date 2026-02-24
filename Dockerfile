# Use the official PHP 8.2 image with Apache web server
FROM php:8.2-apache

# Install required system packages and PHP extensions
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
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Clear out cache to keep the server image small
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache routing rules (Needed for Laravel routes to work)
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory inside the server
WORKDIR /var/www/html

# Copy all your Laravel code into the server
COPY . .

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Give the server permission to write to Laravel's cache and storage folders
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Tell Apache to serve the 'public' folder (Laravel's entry point)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Open port 80 for web traffic
EXPOSE 80