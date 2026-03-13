#!/bin/bash

set -e

echo "🚀 Laravel Catalog Starting..."

# Функция для ожидания MySQL
wait_for_mysql() {
    echo "⏳ Waiting for MySQL..."
    while ! mysqladmin ping -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent 2>/dev/null; do
        sleep 1
    done
    echo "✅ MySQL ready!"
}

# Функция для ожидания готовности PHP-FPM
wait_for_php() {
    echo "⏳ Waiting for PHP-FPM..."
    while ! nc -z app 9000 2>/dev/null; do
        sleep 1
    done
    echo "✅ PHP-FPM ready!"
}

# Ждём MySQL
wait_for_mysql

# Устанавливаем зависимости если нужно
if [ ! -d "/var/www/html/vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-progress
    echo "✅ Composer done!"
fi

# Создаём .env если нет
if [ ! -f "/var/www/html/.env" ]; then
    echo "📄 Creating .env..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Генерируем ключ если нужно
if ! grep -q "^APP_KEY=.*[a-zA-Z0-9]" /var/www/html/.env 2>/dev/null; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
fi

# Миграции
echo "🗄️ Running migrations..."
php artisan migrate --force

# Сиды если база пустая
echo "🌱 Seeding database..."
php artisan db:seed --class=CatalogSeeder --force

echo "✅ Ready! Opening http://localhost:8082"

# Запускаем PHP-FPM
exec php-fpm
