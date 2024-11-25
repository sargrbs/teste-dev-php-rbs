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
COPY .env .env
RUN git config --global --add safe.directory /var/www/html
RUN composer install --no-interaction -vvv

RUN npm install -g chokidar-cli

EXPOSE 8000

RUN chmod +x setup.sh
RUN chmod 755 setup.sh
CMD ["bash", "./setup.sh"]
