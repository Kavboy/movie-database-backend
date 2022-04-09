FROM php:7.4-fpm


# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y install build-essential libpng-dev libjpeg62-turbo-dev libonig-dev libfreetype6-dev jpegoptim optipng pngquant gifsicle libzip-dev libxml2-dev gnupg apt-transport-https unzip \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install required extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql mbstring bcmath ctype zip json tokenizer
RUN docker-php-ext-install fileinfo
RUN docker-php-ext-install xml

# Clear cache
RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

EXPOSE 9000

ENTRYPOINT /bin/bash ./docker/php/entrypoint.sh
