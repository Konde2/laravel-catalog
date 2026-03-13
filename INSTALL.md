# Инструкция по запуску проекта

## Быстрый старт

### 1. Запуск через Docker Compose (рекомендуется)

```bash
# Клонировать репозиторий
git clone https://github.com/USERNAME/REPOSITORY.git
cd REPOSITORY

# Запуск всех контейнеров
docker compose up -d

# Ожидание запуска MySQL (около 10-15 секунд)
# Затем выполнение миграций и сидеров
docker compose exec app php artisan migrate:fresh --seed
```

**Доступ к приложению:**
- Основное приложение: http://localhost:8082
- phpMyAdmin: http://localhost:8081
  - Логин: `laravel`
  - Пароль: `secret`

### 2. Локальный запуск (без Docker)

**Требования:**
- PHP 8.4+
- Composer
- MySQL 8.0+

```bash
# Установка зависимостей
composer install

# Копирование .env (если не существует)
cp .env.example .env

# Генерация ключа приложения
php artisan key:generate

# Настройка БД в .env (измените параметры подключения)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=catalog
# DB_USERNAME=laravel
# DB_PASSWORD=secret

# Создание БД и выполнение миграций
php artisan migrate:fresh --seed

# Запуск встроенного сервера
php artisan serve --port=8082
```

**Доступ к приложению:** http://localhost:8082

## Проверка работы

После запуска:

1. Откройте http://localhost:8080 (или http://localhost:8000)
2. Вы увидите:
   - Список категорий слева (с количеством товаров)
   - Сетку товаров (3 в ряд)
   - Возможность сортировки по названию/цене
   - Пагинацию (6, 12, 18 товаров на странице)
3. Кликните на категорию для просмотра подкатегорий и товаров
4. Кликните на товар для просмотра карточки с хлебными крошками

## Тестирование

```bash
# Запуск всех тестов
docker compose exec app php artisan test

# Запуск с покрытием кода (требуется Xdebug/PCOV)
docker compose exec app php artisan test --coverage
```

## Остановка проекта

```bash
# Остановка контейнеров
docker compose down

# Остановка с удалением данных БД
docker compose down -v
```

## Структура проекта

```
test-assignments/
├── app/
│   ├── Http/Controllers/
│   │   └── CatalogController.php      # Контроллер каталога
│   ├── Models/
│   │   ├── Group.php                  # Модель группы
│   │   ├── Product.php                # Модель товара
│   │   └── Price.php                  # Модель цены
│   └── Repositories/
│       └── GroupRepository.php        # Репозиторий групп
├── database/
│   ├── migrations/                    # Миграции БД
│   └── seeders/                       # Сидеры
├── resources/views/catalog/           # Blade шаблоны
├── routes/web.php                     # Маршруты
├── tests/Feature/                     # Тесты
├── docker-compose.yml                 # Docker конфигурация
└── README.md                          # Документация
```

## Возможные проблемы и решения

### Ошибка подключения к БД

Убедитесь, что MySQL контейнер запущен:
```bash
docker compose ps
```

Перезапустите MySQL:
```bash
docker compose restart mysql
```

### Ошибка миграций

Очистите и выполните миграции заново:
```bash
docker compose exec app php artisan migrate:fresh --seed
```

### Ошибка прав доступа к файлам

```bash
# Для Linux/Mac
sudo chown -R $USER:$USER storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## Дополнительная документация

Смотрите [README.md](README.md) для подробной информации об архитектуре и API.
