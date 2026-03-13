# 🛒 Каталог интернет-магазина на Laravel

[![Tests](https://github.com/USERNAME/REPOSITORY/workflows/Tests/badge.svg)](https://github.com/USERNAME/REPOSITORY/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP](https://img.shields.io/badge/PHP-8.4-blue.svg)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20.svg)](https://laravel.com)

Учебный проект каталога товаров с многоуровневыми категориями, сортировкой, пагинацией и AJAX.

## ✨ Возможности

- 📂 **Многоуровневые категории** с подсчётом товаров в каждой
- 🔀 **Сортировка** по цене и названию (возрастание/убывание)
- 📄 **Пагинация** с выбором количества товаров (6, 12, 18)
- ⚡ **AJAX подгрузка** товаров без перезагрузки страницы
- 🐳 **Docker Compose** для быстрого старта
- ✅ **36 тестов** (100% прохождение)
- 📊 **Покрытие ключевой функциональности** тестами
- 🎨 **Адаптивный дизайн** на Bootstrap 5
- 🍞 **Хлебные крошки** для навигации
- 🔄 **Развёртывание дерева категорий** в сайдбаре

## 🚀 Быстрый старт

### Через Docker Compose (рекомендуется)

**Всё просто — одна команда!**

1. Скачайте проект:
   ```bash
   git clone https://github.com/Konde2/laravel-catalog.git
   cd laravel-catalog
   ```

2. Запустите одной командой:
   ```bash
   docker compose up -d
   ```

3. Откройте http://localhost:8082

**Первый запуск:**
- 🐳 Сборка Docker-образа (~1 мин)
- 📦 Установка Composer зависимостей (~30 сек)
- 📦 Установка npm зависимостей (~30 сек)
- 🗄️ Миграции и сиды (~10 сек)

**Последующие запуски:** несколько секунд (зависимости сохраняются в volumes)

**Доступ:**
- Приложение: http://localhost:8082
- phpMyAdmin: http://localhost:8081 (логин: `laravel`, пароль: `secret`)

---

## 📋 Содержание

- [Технологии](#-технологии)
- [Архитектура](#-архитектура)
- [Установка и запуск](#-быстрый-старт)
- [Структура базы данных](#-структура-базы-данных)
- [API методы](#-api-методы)
- [Тестирование](#-тестирование)
- [Принятые решения](#-принятые-решения)

## 🛠 Технологии

| Компонент | Технология |
|-----------|------------|
| Язык | PHP 8.4 |
| Фреймворк | Laravel 12 |
| База данных | MySQL 8.0 |
| Веб-сервер | Nginx |
| Контейнеризация | Docker, Docker Compose |
| Frontend | Bootstrap 5.3, Blade Templates |
| Тестирование | PHPUnit |

## 🏗 Архитектура

### Общая схема

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Nginx     │────▶│   PHP-FPM   │────▶│    MySQL    │
│   (8082)    │     │   (Laravel) │     │    (3306)   │
└─────────────┘     └─────────────┘     └─────────────┘
                           │
                           ▼
                    ┌─────────────┐
                    │  phpMyAdmin │
                    │    (8081)   │
                    └─────────────┘
```

### Структура приложения

```
app/
├── Http/
│   └── Controllers/
│       └── CatalogController.php    # Контроллер каталога
├── Models/
│   ├── Group.php                    # Модель группы товаров
│   ├── Product.php                  # Модель товара
│   └── Price.php                    # Модель цены
└── Repositories/
    └── GroupRepository.php          # Репозиторий для работы с группами

database/
├── migrations/                      # Миграции БД
└── seeders/                         # Сидеры данных

resources/views/
└── catalog/
    ├── index.blade.php              # Главная страница
    ├── category.blade.php           # Страница категории
    ├── product.blade.php            # Карточка товара
    └── partials/                    # Частные компоненты

routes/
└── web.php                          # Маршруты приложения

tests/Feature/
├── GroupTest.php                    # Тесты модели Group
├── ProductTest.php                  # Тесты модели Product
├── GroupRepositoryTest.php          # Тесты репозитория
└── CatalogControllerTest.php        # Тесты контроллера
```

## 🚀 Установка и запуск

### Требования

- Docker и Docker Compose
- Минимум 2GB свободной оперативной памяти

### Быстрый старт

1. **Клонирование репозитория**
   ```bash
   git clone https://github.com/Konde2/laravel-catalog.git
   cd laravel-catalog
   ```

2. **Запуск контейнеров**
   ```bash
   docker compose up -d
   ```

   При первом запуске автоматически:
   - Установятся Composer зависимости
   - Установятся npm зависимости
   - Выполнятся миграции
   - Заполнится база данных

3. **Откройте приложение**
   - Приложение: http://localhost:8082
   - phpMyAdmin: http://localhost:8081

### Локальный запуск (без Docker)

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

# Настройка БД в .env
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

### Остановка контейнеров

```bash
docker compose down
```

### Остановка с удалением данных

```bash
docker compose down -v
```

### 🔧 Решение проблем

**Ошибка подключения к БД:**
```bash
# Убедитесь, что MySQL контейнер запущен
docker compose ps

# Проверьте логи MySQL
docker compose logs mysql

# Перезапустите MySQL
docker compose restart mysql
```

**Ошибка миграций:**
```bash
# Очистите и выполните миграции заново
docker compose exec app php artisan migrate:fresh --seed
```

**Пересборка образа:**
```bash
docker compose build --no-cache
docker compose up -d
```

**Ошибка прав доступа к файлам (Linux/Mac):**
```bash
sudo chown -R $USER:$USER storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Проверка работы

После запуска:

1. Откройте http://localhost:8082
2. Вы увидите:
   - Список категорий слева (с количеством товаров)
   - Сетку товаров (3 в ряд)
   - Возможность сортировки по названию/цене
   - Пагинацию (6, 12, 18 товаров на странице)
3. Кликните на категорию для просмотра подкатегорий и товаров
4. Кликните на товар для просмотра карточки с хлебными крошками

## 📊 Структура базы данных

### Таблица `groups`

| Поле | Тип | Описание |
|------|-----|----------|
| id | INT | Первичный ключ |
| id_parent | INT | ID родительской группы (0 = корневая) |
| name | VARCHAR(100) | Название группы |

### Таблица `products`

| Поле | Тип | Описание |
|------|-----|----------|
| id | INT | Первичный ключ |
| id_group | INT | Внешний ключ на groups |
| name | VARCHAR(250) | Название товара |

### Таблица `prices`

| Поле | Тип | Описание |
|------|-----|----------|
| id | INT | Первичный ключ |
| id_product | INT | Внешний ключ на products |
| price | DECIMAL(10,2) | Цена товара |

## 🌐 API методы

### Основные маршруты

| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/` | Главная страница каталога |
| GET | `/category/{id}` | Страница категории |
| GET | `/product/{id}` | Карточка товара |

### AJAX API

| Метод | URL | Параметры | Описание |
|-------|-----|-----------|----------|
| GET | `/api/products` | sort, order, per_page, page | Получение товаров (JSON) |
| GET | `/api/category/{id}/products` | sort, order, per_page, page | Товары категории (JSON) |

### Параметры сортировки

| Параметр | Значения | По умолчанию |
|----------|----------|--------------|
| sort | `name`, `price` | `name` |
| order | `asc`, `desc` | `asc` |
| per_page | `6`, `12`, `18` | `6` |

### Пример AJAX запроса

```javascript
fetch('/api/products?sort=price&order=asc&per_page=12')
    .then(response => response.json())
    .then(data => {
        // data.html - HTML сетки товаров
        // data.pagination - HTML пагинации
    });
```

## 🧪 Тестирование

### Запуск всех тестов

```bash
docker compose exec app php artisan test
```

### Запуск с покрытием кода

```bash
docker compose exec app php artisan test --coverage
```

### Запуск отдельных тестов

```bash
# Тесты модели Group
docker compose exec app php artisan test --filter GroupTest

# Тесты контроллера
docker compose exec app php artisan test --filter CatalogControllerTest
```

### Структура тестов

```
tests/Feature/
├── GroupTest.php                 # Тесты модели Group
├── ProductTest.php               # Тесты модели Product
├── GroupRepositoryTest.php       # Тесты репозитория
└── CatalogControllerTest.php     # Тесты контроллера
```

## ✅ Принятые решения

### Архитектурные решения

1. **Repository Pattern**
   - `GroupRepository` инкапсулирует логику работы с группами
   - Упрощает тестирование и поддержку кода
   - Соответствует принципу Single Responsibility (SOLID)

2. **Eager Loading**
   - Использование `with()` для предотвращения N+1 запросов
   - Оптимизация производительности при загрузке связанных моделей

3. **Компонентный подход в Blade**
   - Переиспользуемые partial-шаблоны
   - Единый layout для всех страниц
   - Соответствует принципу DRY

### UI/UX решения

1. **Сетка товаров (3 в ряд)**
   - Адаптивная верстка через Bootstrap Grid
   - На больших экранах - 3 товара в ряд
   - На средних - 2, на мобильных - 1

2. **AJAX пагинация и сортировка**
   - Без перезагрузки страницы
   - Сохранение состояния через URL параметры
   - Улучшенный пользовательский опыт

3. **Хлебные крошки**
   - Рекурсивное построение пути
   - Навигация на любом уровне вложенности

### Производительность

1. **Оптимизированные SQL запросы**
   - Использование `whereIn()` для фильтрации по группам
   - Рекурсивный подсчет ID групп вместо множественных запросов

2. **Кэширование (потенциальное)**
   - Возможность добавить кэширование для `getTotalProductsCount()`
   - Redis/Memcached готовы к подключению

## 📝 Дополнительные команды

```bash
# Очистка кэша
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan view:clear

# Генерация автозагрузки
docker compose exec app composer dump-autoload

# Проверка стиля кода
docker compose exec app php artisan pint

# Просмотр логов
docker compose logs -f app

# Остановка контейнеров
docker compose down

# Остановка с удалением volumes
docker compose down -v
```

## 📄 Лицензия

Проект создан в учебных целях.
