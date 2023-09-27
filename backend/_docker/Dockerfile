FROM php:8.1-fpm

RUN apt-get -y update && apt-get -y upgrade && apt-get install -y \
    apt-utils \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    zip unzip \
    git && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install bcmath && \
    docker-php-ext-install gd && \
    docker-php-ext-install zip && \
    apt-get install -y libjpeg-dev libpng-dev && \ 
    apt-get install -y ffmpeg && \
    docker-php-ext-configure gd --with-jpeg && \ 
    docker-php-ext-install gd && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


COPY ./portfolio/backend/_docker/php.ini /usr/local/etc/php/conf.d/php.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

WORKDIR /var/www/portfolio/backend