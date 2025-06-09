# Use PHP with Apache
FROM php:8.1-apache

# Copy project files to Apache web root
COPY . /var/www/html/

# Set permissions (optional)
RUN chown -R www-data:www-data /var/www/html

# Enable Apache rewrite (optional for pretty URLs)
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80
