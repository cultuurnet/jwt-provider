FROM php:7.4-apache

RUN apt-get update && apt-get install -y --no-install-recommends git zip unzip libicu-dev libtidy-dev zlib1g-dev libpng-dev libzip-dev

RUN docker-php-ext-install bcmath tidy sockets

RUN docker-php-ext-configure zip && docker-php-ext-install zip

RUN docker-php-ext-configure intl && docker-php-ext-install intl

RUN docker-php-ext-configure gd && docker-php-ext-install gd

RUN curl https://getcomposer.org/download/2.6.6/composer.phar -o /usr/local/bin/composer && chmod +x /usr/local/bin/composer

RUN echo "memory_limit=4096M" > $PHP_INI_DIR/conf.d/memory-limit.ini
RUN echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE" > $PHP_INI_DIR/conf.d/error_reporting.ini

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/web
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN echo '\nLimitRequestFieldSize 163840\nLimitRequestFields 1000\nLimitRequestLine 81900\n' >> /etc/apache2/apache2.conf
