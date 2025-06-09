# Use PHP with Apache
FROM php:8.1-apache

# Install mysqli extension and dependencies
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy project files to Apache web root
COPY . /var/www/html/

# Set permissions (optional)
RUN chown -R www-data:www-data /var/www/html

# Enable Apache rewrite (optional)
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80
