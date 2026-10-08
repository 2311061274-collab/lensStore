FROM php:8.3-fpm

# Install system dependencies and Nginx
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Copy custom configurations
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Install Node dependencies and build assets (Tailwind/Vite)
RUN npm install
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Make entrypoint executable
COPY docker/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port
EXPOSE 80

# Run entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
