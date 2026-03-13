#!/bin/bash

set -e

echo "🚀 Laravel Catalog Starting..."

# Ждём MySQL
echo "⏳ Waiting for MySQL..."
until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" &>/dev/null; do
    sleep 1
done
echo "✅ MySQL ready!"

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
php artisan db:seed --class=CatalogSeeder --force || true

echo "✅ Ready!"

# Запускаем PHP-FPM
exec php-fpm
