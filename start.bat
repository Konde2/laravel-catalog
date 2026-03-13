@echo off
echo 🚀 Запуск Laravel Catalog...
echo.

REM Проверка наличия Docker
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker не найден! Установите Docker Desktop.
    pause
    exit /b 1
)

echo ✅ Docker найден
echo.

REM Проверка наличия .env
if not exist ".env" (
    echo 📄 Создание .env из .env.example...
    copy .env.example .env >nul
    echo ✅ .env создан
    echo.
)

REM Сборка и запуск контейнеров
echo 🐳 Запуск Docker Compose...
docker compose up -d --build

if %errorlevel% neq 0 (
    echo ❌ Ошибка запуска контейнеров!
    pause
    exit /b 1
)

echo.
echo ✅ Контейнеры запущены!
echo.
echo 📦 Приложение доступно: http://localhost:8082
echo 📦 phpMyAdmin: http://localhost:8081
echo.
echo 📋 Логи: docker compose logs -f
echo ⏹️  Остановка: docker compose down
echo.
pause
