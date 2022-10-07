FROM wordpress:6.0.2-php7.4-apache

# Install necessaries libs
RUN apt update \
  && apt install -y git libmemcached-dev wget unzip libjpeg62-turbo-dev libpng-dev libxpm-dev \
    libfreetype6-dev libzip-dev libmagickwand-dev --no-install-recommends memcached curl gnupg vim iputils-ping

WORKDIR /tmp

# Install memcached
RUN mkdir -p /usr/src/php/ext/memcached \
  && wget https://github.com/php-memcached-dev/php-memcached/archive/v3.1.3.zip; unzip v3.1.3.zip \
  && mv php-memcached-3.1.3/* /usr/src/php/ext/memcached/

WORKDIR /var/www/html

# Install necessaries libs, php extensions, npm
RUN pecl install imagick \
  && docker-php-ext-install zip \
  && docker-php-ext-enable imagick \
  && docker-php-ext-configure memcached \
  && docker-php-ext-install memcached \
  && docker-php-ext-install exif \
  && docker-php-ext-install mysqli \
  && docker-php-ext-install calendar \
  && docker-php-ext-configure gd \
  --with-jpeg=/usr/include/ \
  --with-freetype=/usr/include/ \
  && docker-php-ext-install gd \
  && wget https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
  && chmod +x wp-cli.phar \
  && mv wp-cli.phar /usr/local/bin/wp \
  && apt clean \
  && apt autoremove --fix-missing \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
  && curl -sL https://deb.nodesource.com/setup_14.x  | bash - \
  && apt -y install nodejs

# Copy and give permissions to execute the script
COPY ./entrypoint.sh /

RUN chmod +x /entrypoint.sh

RUN chown -R www-data:www-data /var/www/html

ENTRYPOINT ["/entrypoint.sh"]

# Copy composer
COPY --from=composer /usr/bin/composer /usr/bin/composer