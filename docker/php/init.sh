#!/bin/bash

set -e

echo "🚀 Starting Laravel Catalog initialization..."

# Ждём пока MySQL будет готов
echo "⏳ Waiting for MySQL to be ready..."
until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" &>/dev/null; do
    echo "   MySQL is unavailable - sleeping..."
    sleep 2
done
echo "✅ MySQL is ready!"

# Проверяем, установлен ли vendor
if [ ! -d "/var/www/html/vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-progress
    echo "✅ Composer dependencies installed!"
fi

# Проверяем, существует ли .env
if [ ! -f "/var/www/html/.env" ]; then
    echo "📄 Creating .env file..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Проверяем, сгенерирован ли APP_KEY
if ! grep -q "^APP_KEY=.*[a-zA-Z0-9]" /var/www/html/.env 2>/dev/null; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Проверяем, выполнена ли миграция
echo "🗄️ Checking migrations..."
if ! php artisan migrate:status 2>&1 | grep -q "migrations"; then
    echo "🗄️ Running migrations..."
    php artisan migrate --force
fi

# Проверяем, есть ли данные в таблице groups
echo "🌱 Checking database seed..."
GROUPS_COUNT=$(php artisan tinker --execute="echo App\Models\Group::count();" 2>/dev/null || echo "0")
if [ "$GROUPS_COUNT" = "0" ] || [ -z "$GROUPS_COUNT" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --class=CatalogSeeder --force
    echo "✅ Database seeded!"
fi

echo "✅ Initialization complete!"
echo "🎉 Application is ready at http://localhost:8082"

# Запускаем PHP-FPM в фоновом режиме
exec php-fpm
