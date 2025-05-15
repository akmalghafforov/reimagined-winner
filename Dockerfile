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
    git \
    busybox-suid \
    cronie \
    supervisor

# Configure PHP extensions
RUN docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) pdo pdo_pgsql pgsql zip intl mbstring opcache bcmath

# Set working directory
WORKDIR /var/www

# Copy your application (optional)
COPY . .

RUN mkdir -p /etc/crontabs \
 && echo '* * * * * echo "cron works" >> /var/log/cron.log' > /etc/crontabs/root \
 && chmod 600 /etc/crontabs/root

# Set up cron job
RUN rm -f /var/run/crond.pid
COPY supervisord.conf /etc/supervisord.conf

# Expose port (if needed)
EXPOSE 9000

# Start PHP-FPM & Start cron in foreground
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
