FROM php:7.3-apache

# Install system dependencies, PHP extensions, and MariaDB (MySQL)
ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y mariadb-server \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html/

# Copy database schema
COPY Docker/mysql/dbinit/tables.sql /var/www/html/init_db.sql

# Configure Apache to allow .htaccess without overriding PHP handler
RUN printf '<Directory /var/www/html/>\n    Options Indexes FollowSymLinks\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
        > /etc/apache2/conf-available/rinku-allow-override.conf \
    && a2enconf docker-php \
    && a2enconf rinku-allow-override

# Set permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod +x /var/www/html/docker-entrypoint.sh

# Expose port 80
EXPOSE 80

ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
