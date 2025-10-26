FROM php:8.3-fpm

ARG user
ARG uid

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && docker-php-ext-install pdo_pgsql pgsql

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-progress

# Create user
RUN useradd -u $uid -ms /bin/bash -g www-data $user

# Create folder for session storage
RUN mkdir -p /var/lib/php/sessions
RUN chown -R $user:www-data /var/lib/php/sessions
RUN chmod 0700 /var/lib/php/sessions

COPY . /var/www

RUN chown -R $user:www-data /var/www

USER $user

EXPOSE 9000
CMD ["php-fpm"]
