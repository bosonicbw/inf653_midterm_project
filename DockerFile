# Use PHP with Apache
FROM php:8.1-apache

# Install system dependencies for PostgreSQL PDO
RUN apt-get update && apt-get install -y libpq-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite (not required if you're not using .htaccess routing)
RUN a2enmod rewrite

# Copy your code
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html
