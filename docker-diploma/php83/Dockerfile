FROM php:8.3-rc-fpm
RUN useradd  -m -N -p userPASS -s /bin/bash anyteam
RUN apt-get update && apt-get install -y libpq-dev git curl libpng-dev zip unzip libxml2-dev 
RUN pecl install dbase && docker-php-ext-enable dbase
ENV PHP_EXTRA_CONFIGURE_ARGS --enable-fpm --with-fpm-user=anyteam --with-fpm-group=www-data
RUN docker-php-ext-install -j$(nproc) pdo pdo_pgsql soap
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /home/anyteam

CMD ["php-fpm"]

