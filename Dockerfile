FROM composer:latest as composer
WORKDIR /var/www/html
COPY composer.* ./
RUN composer install

FROM php:8.3-apache
COPY --from=composer /var/www/html .

# Copy in custom code from the host machine.
WORKDIR /var/www/html
COPY . ./
RUN chmod 777 templates_c

# Use the PORT environment variable in Apache configuration files.
# https://cloud.google.com/run/docs/reference/container-contract#port
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Configure PHP for development.
# Switch to the production php.ini for production operations.
# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# https://github.com/docker-library/docs/blob/master/php/README.md#configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"