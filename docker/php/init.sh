#!/bin/bash

# Скрипт автоматической инициализации проекта
# Запускается при первом старте контейнера

echo "🚀 Starting Laravel Catalog initialization..."

# Ждём пока MySQL будет готов
echo "⏳ Waiting for MySQL to be ready..."
while ! mysqladmin ping -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent 2>/dev/null; do
    sleep 2
done
echo "✅ MySQL is ready!"

# Проверяем, установлен ли vendor
if [ ! -d "/var/www/html/vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Проверяем, существует ли .env
if [ ! -f "/var/www/html/.env" ]; then
    echo "📄 Creating .env file..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Проверяем, сгенерирован ли APP_KEY
if [ -z "$(grep -m1 '^APP_KEY=' /var/www/html/.env | cut -d'=' -f2)" ] || [ "$(grep -m1 '^APP_KEY=' /var/www/html/.env | cut -d'=' -f2)" = "" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Проверяем, выполнена ли миграция
if ! php artisan migrate:status 2>/dev/null | grep -q "migrations"; then
    echo "🗄️ Running migrations..."
    php artisan migrate --force
fi

# Проверяем, есть ли данные в таблице groups
GROUPS_COUNT=$(php artisan tinker --execute="echo App\Models\Group::count();" 2>/dev/null)
if [ "$GROUPS_COUNT" = "0" ] || [ -z "$GROUPS_COUNT" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --class=CatalogSeeder --force
fi

# Кэширование для production
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Caching configuration..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "✅ Initialization complete!"
echo "🎉 Application is ready at http://localhost:8082"
