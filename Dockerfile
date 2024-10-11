FROM php:8.2-fpm as php

# Install dependencies.
RUN apt-get update && apt-get upgrade -y && apt-get install -y unzip libpq-dev libcurl4-gnutls-dev nginx libonig-dev

# Install PHP extensions.
RUN docker-php-ext-install pgsql pdo pdo_pgsql bcmath curl opcache mbstring pcntl

# Copy composer executable.
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set working directory to /var/www.
WORKDIR /var/www/html

# Copy files from current folder to container current folder (set in workdir).
COPY --chown=www-data:www-data . .

# Adjust user permission & group
RUN usermod --uid 1000 www-data
RUN groupmod --gid 1001 www-data

# Run the entrypoint file.
COPY docker/entrypoint.sh /usr/bin/
RUN chmod +x /usr/bin/entrypoint.sh
ENTRYPOINT ["/usr/bin/entrypoint.sh"]