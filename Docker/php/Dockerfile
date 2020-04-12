FROM php:7.3-apache
LABEL maintainer="gopinath <gopinath2nr@gmail.com>"
RUN a2enmod rewrite \
    && pecl install xdebug-2.7.2 \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-install pdo pdo_mysql

EXPOSE 80 9000

# to move custom configuretion file while building images
# COPY httpd-config/000-default.conf /etc/apache2/sites-available/000-default.conf

# enable xdebug remote debug
# && echo "xdebug.remote_enable = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#     && echo "xdebug.remote_autostart = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \ 
#     && echo "xdebug.remote_host=192.168.1.33" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \ 