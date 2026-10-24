# syntax=docker/dockerfile:1
# ---------------------------------------------------------
# Backend Dockerfile using FrankenPHP (Bonus +10)
# Base image locked to specific tag dunglas/frankenphp:1.4-php8.3-alpine
# ---------------------------------------------------------
FROM dunglas/frankenphp:1.4-php8.3-alpine

# Set environment
ENV SERVER_NAME=":8000"
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install required PHP extensions & utilities
RUN install-php-extensions \
    pdo_mysql \
    bcmath \
    zip \
    pcntl \
    intl \
    opcache

RUN apk add --no-cache netcat-openbsd

# Install composer from official composer image
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Layer Caching: Copy composer definitions first to maximize Docker cache
COPY composer.json composer.lock* ./

# Install dependencies (no dev, optimize autoloader)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# Copy application code
COPY . .

# Generate optimized autoload and discover packages
RUN composer dump-autoload --optimize --no-dev

# Copy custom Caddyfile and entrypoint script
COPY Caddyfile /etc/caddy/Caddyfile
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Storage permissions
RUN mkdir -p /app/storage /app/bootstrap/cache && \
    chmod -R 777 /app/storage /app/bootstrap/cache

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
