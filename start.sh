#!/bin/bash

echo "🚀 Запуск Laravel Catalog..."
echo

# Проверка наличия Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker не найден! Установите Docker."
    exit 1
fi

echo "✅ Docker найден"
echo

# Проверка наличия .env
if [ ! -f ".env" ]; then
    echo "📄 Создание .env из .env.example..."
    cp .env.example .env
    echo "✅ .env создан"
    echo
fi

# Сборка и запуск контейнеров
echo "🐳 Запуск Docker Compose..."
docker compose up -d --build

if [ $? -ne 0 ]; then
    echo "❌ Ошибка запуска контейнеров!"
    exit 1
fi

echo
echo "✅ Контейнеры запущены!"
echo
echo "📦 Приложение доступно: http://localhost:8082"
echo "📦 phpMyAdmin: http://localhost:8081"
echo
echo "📋 Логи: docker compose logs -f"
echo "⏹️  Остановка: docker compose down"
echo
