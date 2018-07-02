# Dockerfile
FROM php:7-apache

MAINTAINER Bryan Karaffa <BryanKaraffa@gmail.com>

COPY ./ /var/www/html/
RUN apt-get update && apt-get install -y \
        git

RUN \
   cd /var/www/html/ && \
   git submodule init && \
   git submodule update