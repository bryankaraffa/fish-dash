# Dockerfile
FROM php:7-apache

MAINTAINER Bryan Karaffa <BryanKaraffa@gmail.com>

COPY ./ /var/www/html/

ARG FORECASTIO_API_KEY
COPY ./config.example.php /var/www/html/config.php
RUN \
   sed -i "s/forecast io api key/${FORECASTIO_API_KEY}/" /var/www/html/config.php

RUN apt-get update && apt-get install -y \
        git

RUN \
   cd /var/www/html/ && \
   git submodule init && \
   git submodule update