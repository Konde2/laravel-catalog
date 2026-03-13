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
    default-mysql-client

# Очистка кэша
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка PHP расширений
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создание пользователя для работы с Laravel
ARG PUID=1000
ARG PGID=1000

RUN groupadd -g ${PGID} laravel && \
    useradd -u ${PUID} -g laravel -m laravel

# Установка прав
RUN chown -R laravel:laravel /var/www/html

USER laravel

WORKDIR /var/www/html

# Копирование скрипта инициализации
COPY --chown=laravel:laravel init.sh /usr/local/bin/init.sh
RUN chmod +x /usr/local/bin/init.sh

# Скрипт запуска
CMD ["sh", "-c", "/usr/local/bin/init.sh && php-fpm"]

EXPOSE 9000
