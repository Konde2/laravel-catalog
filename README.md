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

```bash
# Клонировать репозиторий
git clone https://github.com/USERNAME/REPOSITORY.git
cd REPOSITORY

# Запустить контейнеры
docker compose up -d

# Выполнить миграции и заполнение данными
docker compose exec app php artisan migrate:fresh --seed
```

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
   git clone https://github.com/USERNAME/REPOSITORY.git
   cd REPOSITORY
   ```

2. **Запуск контейнеров**
   ```bash
   docker compose up -d
   ```

3. **Выполнение миграций и заполнение данными**
   ```bash
   docker compose exec app php artisan migrate:fresh --seed
   ```

### Доступ к приложению

- **Основное приложение**: http://localhost:8082
- **phpMyAdmin**: http://localhost:8081
  - Логин: `laravel`
  - Пароль: `secret`

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

# Запуск в режиме разработки
docker compose up -d
docker compose logs -f app
```

## 📄 Лицензия

Проект создан в учебных целях.
