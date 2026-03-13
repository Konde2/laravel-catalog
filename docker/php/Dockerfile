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

# Копируем файлы зависимостей для установки в образ
COPY --chown=laravel:laravel composer.json composer.lock* package.json package-lock.json* ./

# Устанавливаем PHP зависимости
RUN if [ -f "composer.lock" ]; then \
        composer install --no-interaction --prefer-dist --optimize-autoloader --no-progress --no-scripts; \
    else \
        composer update --no-interaction --prefer-dist --optimize-autoloader --no-progress --no-scripts; \
    fi \
    && chown -R laravel:laravel /var/www/html/vendor

# Устанавливаем Node.js зависимости
RUN if [ -f "package-lock.json" ]; then \
        npm ci --legacy-peer-deps; \
    else \
        npm install --legacy-peer-deps; \
    fi \
    && chown -R laravel:laravel /var/www/html/node_modules

# Копирование всего проекта
COPY --chown=laravel:laravel . .

# Создание директорий с правильными правами
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs \
    && chown -R laravel:laravel storage \
    && chmod -R 775 storage

# Копирование скрипта entrypoint
COPY --chown=laravel:laravel docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER laravel

EXPOSE 9000

# Запускаем entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
