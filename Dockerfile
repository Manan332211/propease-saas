# Use the official PHP 8.2 image with Apache web server
FROM php:8.2-apache

# Install required system packages, PHP extensions, and PostgreSQL drivers
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
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

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

# Install dependencies securely (The fix: --no-scripts prevents Artisan from crashing the build)
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Give the server permission to write to Laravel's cache and storage folders
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Tell Apache to serve the 'public' folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Open port 80 for web traffic
EXPOSE 80