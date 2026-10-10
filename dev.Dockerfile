# Local development image. Production runs PHP 8.1 FPM directly on the server (see deploy-live-aws.sh).
FROM php:8.1-fpm-alpine

RUN apk --no-cache add \
    bash \
    git \
    zip \
    unzip \
    shadow \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" gd pdo_mysql mbstring zip exif pcntl bcmath opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-asl.ini
COPY docker/php/entrypoint.sh /usr/local/bin/asl-entrypoint

# Give www-data the host user's UID/GID so files written into the bind mount
# (vendor/, storage/, .env) belong to you and not to root.
ARG HOST_UID=1000
ARG HOST_GID=1000
RUN groupmod -o -g "${HOST_GID}" www-data \
    && usermod -o -u "${HOST_UID}" -g www-data www-data \
    && chmod +x /usr/local/bin/asl-entrypoint

ENV COMPOSER_HOME=/tmp/composer
WORKDIR /var/www/html
USER www-data

EXPOSE 9000
ENTRYPOINT ["asl-entrypoint"]
CMD ["php-fpm"]
