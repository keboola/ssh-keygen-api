FROM php:7.0.2-apache

RUN apt-get update && apt-get install -y ssh

RUN apt-get install -y zlib1g zlib1g-dev && \
  docker-php-ext-install zip && \
  docker-php-ext-enable zip

COPY . /var/www/html
WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php
RUN php composer.phar install -n
