# Dockerfile
FROM php:7.1.8-apache

MAINTAINER Bryan Karaffa <BryanKaraffa@gmail.com>

COPY ./ /var/www/html/
COPY ./config.example.php /var/www/html/config.php

RUN apt-get update && apt-get install -y \
        git

RUN \
   cd /var/www/html/ && \
   git submodule init && \
   git submodule update


EXPOSE 80
EXPOSE 443

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]