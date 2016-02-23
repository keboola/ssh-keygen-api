FROM php:7.0.2-apache

RUN apt-get update && apt-get install -y ssh

COPY . /var/www/html
WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php
RUN php composer.phar install -n

