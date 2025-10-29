# Use the official PHP 8.2 image with Apache
FROM php:8.2-apache

# Enable Apache mod_rewrite (for pretty URLs)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN apt-get update && apt-get install -y unzip git \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction --no-scripts --no-autoloader

# Copy the rest of your application code
COPY . .

# Run composer again to autoload after files are copied
RUN composer dump-autoload

# Configure Apache DocumentRoot to point to the public folder
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for Render
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
