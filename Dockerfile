# Use the official PHP image as a base
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk --no-cache add \
    build-base \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libxpm-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    bash \
    fcgi \
    libmcrypt-dev \
    oniguruma-dev \
    postgresql-dev

# Clear cache
RUN rm -rf /var/cache/apk/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring zip exif pcntl bcmath opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Add www-data user with specified UID and GID (1000 in this example)
RUN deluser www-data && \
    addgroup -g 1000 www-data && \
    adduser -u 1000 -G www-data -s /bin/sh -D www-data

# Copy existing application directory contents
COPY . /var/www/html

# Set ownership and permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html

# Change current user to www-data
USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

