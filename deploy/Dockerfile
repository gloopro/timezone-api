# Set base image
FROM public.ecr.aws/i7r5e7e2/php-fpm-8.0

ARG CLOUD_ID
ARG CLOUD_AUTH
ARG SERVICE_ENVIRONMENT
ARG SERVICE_NAME

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libgmp-dev \
    re2c \
    libmhash-dev \
    libmcrypt-dev \
    file \
    nginx \
    supervisor \
    libicu-dev && apt-get install -y --no-install-recommends supervisor wget gnupg2 


# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN ln -s /usr/include/x86_64-linux-gnu/ /usr/local/include/

# Configure dependencies for composer
RUN docker-php-ext-configure gmp \
    && docker-php-ext-configure gd \
    --with-jpeg \
    && docker-php-ext-configure zip

# Install dependencies for composer
RUN docker-php-ext-install pdo_mysql zip exif pcntl gmp gd zip sockets intl

# Install pcov extension - for generating PHPUnit coverage reports
RUN pecl install pcov && docker-php-ext-enable pcov

RUN pecl install scoutapm

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www
COPY ./php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY ./php/process.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./deploy/supervisord.conf /etc/supervisor/conf.d/comms-supervisord.conf

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

#configure nginx
RUN rm /etc/nginx/sites-enabled/default
COPY ./nginx/deploy.conf /etc/nginx/conf.d/default.conf



# Copy existing application directory permissions
RUN mkdir -p /mnt/app
COPY --chown=www:www . /var/www
COPY --chown=www:www ./storage/app* /mnt/app
RUN chown www:www /var/log/* 
RUN chmod -R 777 /var/www/storage


#USER www
RUN chmod +x ./deploy/run.sh

EXPOSE 80

ENTRYPOINT ["/var/www/deploy/run.sh"]
