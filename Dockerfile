# Use PHP with Apache
FROM php:8.4-apache

# Install MySQL support for PHP
RUN docker-php-ext-install mysqli

# Copy website files into Apache
COPY . /var/www/html/

# Enable Apache to use Render's port
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost *:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf

# Start Apache
CMD ["apache2-foreground"]