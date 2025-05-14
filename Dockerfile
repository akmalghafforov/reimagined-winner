FROM php:8.3-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    icu-dev \
    oniguruma-dev \
    zlib-dev \
    libxml2-dev \
    autoconf \
    g++ \
    make \
    curl \
    nginx  \
    curl \
    git \
    unzip

# Configure PHP extensions
RUN docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) pdo pdo_pgsql pgsql zip intl mbstring opcache bcmath

# Set working directory
WORKDIR /var/www

# Copy your application (optional)
COPY . .

# Expose port (if needed)
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
