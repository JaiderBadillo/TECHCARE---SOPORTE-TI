-- Esquema de la base de datos: soporte_db
-- Tabla de Solicitudes de Soporte y Usuarios Administradores

CREATE DATABASE IF NOT EXISTS soporte_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE soporte_db;

-- Tabla de Solicitudes (Tickets)
CREATE TABLE IF NOT EXISTS solicitudes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    asunto VARCHAR(150) NOT NULL,
    tipo_problema ENUM('RED', 'SOFTWARE', 'HARDWARE', 'SEGURIDAD', 'CLOUD_SERVIDORES', 'BASE_DE_DATOS') NOT NULL DEFAULT 'SOFTWARE',
    prioridad ENUM('baja', 'media', 'alta', 'critica') NOT NULL DEFAULT 'media',
    mensaje TEXT NOT NULL,
    estado ENUM('pendiente', 'en_proceso', 'resuelto') DEFAULT 'pendiente',
    solucion_ia TEXT NULL,
    asignado_a VARCHAR(100) NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Usuarios Técnicos y Administradores para el Dashboard
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'tecnico') NOT NULL DEFAULT 'admin',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario administrador inicial por defecto (Contraseña: admin123)
-- Hash generado con password_hash('admin123', PASSWORD_BCRYPT)
INSERT IGNORE INTO usuarios (id, nombre, email, password, rol) 
VALUES (1, 'Administrador TI', 'admin@techcare.com', '$2y$10$tZ8g8g6m7QZcQ8K8x4J7.eR5G9BqQj0VzI4t2w8Xl7P6Z1x3M5c6q', 'admin');
