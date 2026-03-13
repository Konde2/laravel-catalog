-- Скрипт инициализации базы данных
-- Будет выполнен автоматически при первом запуске MySQL контейнера

CREATE DATABASE IF NOT EXISTS catalog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON catalog.* TO 'laravel'@'%';
FLUSH PRIVILEGES;
