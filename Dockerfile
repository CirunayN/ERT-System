FROM php:8.4-apache
# Install system packages and PHP extension
RUN apt-get update && apt-get install -y \
git \
unzip \
curl \
libpq-dev \
libzip-dev \
libonig-dev \
libxml2-dev \
libpng-dev \
zip \
&& docker-php-ext-install pdo pdo_mysql pdo_pgsql zip mbstring xml \
&& apt-get clean \
&& rm -rf /var/lib/apt/lists/*
# Enable Apache rewrite
RUN a2enmod rewrite
# Make Apache use port 10000 (Render default)
RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf

# Create a robust Apache VirtualHost configuration
RUN echo '<VirtualHost *:10000>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf
# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
&& apt-get install -y nodejs
# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# Set working directory
WORKDIR /var/www/html
# Copy Laravel app
COPY . .
# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction
# Install frontend dependencies and build assets
RUN npm install && npm run build
RUN php artisan config:clear \
&& php artisan route:clear \
&& php artisan view:clear
# Create storage symlink
RUN php artisan storage:link || true
# Fix permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions \
storage/framework/views bootstrap/cache public/uploads \
&& chown -R www-data:www-data /var/www/html \
&& find /var/www/html -type d -exec chmod 755 {} \; \
&& find /var/www/html -type f -exec chmod 644 {} \; \
&& chmod -R 775 storage bootstrap/cache public/uploads
# (Optional) Run migrations
RUN php artisan migrate --force || true
# Expose port
EXPOSE 10000
# Start Apache
CMD ["apache2-foreground"]