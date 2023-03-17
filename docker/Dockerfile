FROM node:lts-alpine AS node-chokidar
WORKDIR /var/www/html
COPY src/package.json .
RUN npm install

FROM ghcr.io/roadrunner-server/roadrunner:latest AS roadrunner

FROM alpine:edge

LABEL Maintainer="Imam Fahmi Fadillah <imam.dev93@gmail.com>" \
    Description="Laravel Octane on Alpine Linux."

ARG PHP_VERSION="8.2.3-r0"

# Install packages
RUN apk --no-cache add php82 php82-opcache php82-openssl php82-curl php82-phar php82-session \
    php82-fileinfo php82-pdo php82-pdo_mysql php82-mbstring php82-dom php82-xml php82-xmlwriter php82-sodium \
    php82-tokenizer php82-posix php82-pecl-swoole php82-pecl-redis \
    curl nodejs npm

# Symlink php82 => php
RUN ln -sf /usr/bin/php82 /usr/bin/php

# Configure PHP
COPY docker/php/custom.ini /etc/php82/conf.d/custom.ini

# Setup document root
RUN mkdir -p /var/www/html
COPY docker/docker-entrypoint.sh docker-entrypoint.sh
RUN chmod +x docker-entrypoint.sh

COPY --from=node-chokidar /var/www/html /var/www/html

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN chown -R nobody.nobody /var/www/html && \
    chown -R nobody.nobody /run

# Switch to use a non-root user from here on
USER nobody

# Add application
WORKDIR /var/www/html
COPY --chown=nobody src/ /var/www/html

# Install Roadrunner from the official image
COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

# Install composer from the official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Run composer install to install the dependencies
RUN composer install --no-cache --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress

# Expose the port app is reachable on
EXPOSE 80

# Let start
ENTRYPOINT ["/docker-entrypoint.sh"]