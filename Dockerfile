FROM php:8.3-fpm

# Install system dependencies and PHP extensions.
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP extensions.
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer from the official Composer image.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory.
WORKDIR /var/www

# Copy application source.
COPY . .

# Install PHP dependencies.
RUN composer install --prefer-dist --no-dev --optimize-autoloader

# Expose port 9000 and start PHP-FPM.
EXPOSE 9000
CMD ["php-fpm"]
