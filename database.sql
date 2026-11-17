-- Створення бази даних для Лабораторної роботи №7
CREATE DATABASE IF NOT EXISTS web2_lab07
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE web2_lab07;

-- Створення таблиці для логування обчислень калькулятора
CREATE TABLE IF NOT EXISTS calculations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    num1 DOUBLE NOT NULL,
    num2 DOUBLE NOT NULL,
    operator VARCHAR(5) NOT NULL,
    result VARCHAR(100) NOT NULL,
    user_email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
