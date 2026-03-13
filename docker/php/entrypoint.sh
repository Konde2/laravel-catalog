#!/bin/bash

set -e

echo "🚀 Laravel Catalog Starting..."

# Ждём MySQL (до 60 секунд)
echo "⏳ Waiting for MySQL..."
for i in {1..30}; do
    if mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" &>/dev/null; then
        echo "✅ MySQL ready!"
        break
    fi
    echo "   Waiting... ($i/30)"
    sleep 2
done

# Создаём .env если нет
if [ ! -f "/var/www/html/.env" ]; then
    echo "📄 Creating .env..."
    cp /var/www/html/.env.example /var/www/html/.env
    echo "   .env created"
fi

# Генерируем ключ если нужно
if ! grep -q "^APP_KEY=.*[a-zA-Z0-9]" /var/www/html/.env 2>/dev/null; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
fi

# Миграции
echo "🗄️ Running migrations..."
php artisan migrate --force --no-interaction

# Сиды
echo "🌱 Seeding database..."
php artisan db:seed --class=CatalogSeeder --force --no-interaction || true

# Vite build
echo "📦 Building assets..."
npm run build || true

echo "✅ Ready! Opening http://localhost:8082"

# Запускаем PHP-FPM
exec php-fpm
