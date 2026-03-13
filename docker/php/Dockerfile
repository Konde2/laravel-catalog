FROM php:8.4-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    default-mysql-client \
    netcat-openbsd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка PHP расширений
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создание пользователя для работы с Laravel
ARG PUID=1000
ARG PGID=1000

RUN groupadd -g ${PGID} laravel && \
    useradd -u ${PUID} -g laravel -m laravel

WORKDIR /var/www/html

# Копируем файлы зависимостей
COPY composer.json composer.lock* package.json package-lock.json* ./

# Устанавливаем зависимости ОТ ROOT (чтобы работали с volumes)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-progress --no-scripts --ignore-platform-reqs \
    && npm install --legacy-peer-deps \
    && chown -R laravel:laravel /var/www/html/vendor \
    && chown -R laravel:laravel /var/www/html/node_modules

# Копируем entrypoint
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Копируем весь проект
COPY --chown=laravel:laravel . .

# Создаём директории storage
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs \
    && chown -R laravel:laravel storage \
    && chmod -R 775 storage

USER laravel

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
