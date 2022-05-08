FROM php:8.0-apache
WORKDIR /var/www/html

COPY src/esa1/* ./

EXPOSE 80