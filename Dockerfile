FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    pkg-config \
    libbrotli-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libpq-dev \
    librabbitmq-dev \
    libsodium-dev \
    libzip-dev \
    nodejs \
    npm

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    sockets \
    sodium \
    zip

RUN pecl install swoole \
    && docker-php-ext-enable swoole

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install

RUN npm install -g chokidar-cli

RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

CMD php artisan octane:start --host=0.0.0.0 --port=8000 --watch
