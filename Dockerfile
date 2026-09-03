FROM php:8.4-cli
# Install MySQL driver for PHP
RUN docker-php-ext-install pdo_mysql
WORKDIR /var/www/html
# Copy Composer binary from official Composer image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# Copy application files
COPY . .
# Install PHP dependencies
RUN composer install
# Expose Laravel development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]