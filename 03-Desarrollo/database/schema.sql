-- Esquema de la base de datos: soporte_db
-- Tabla de Solicitudes de Soporte

CREATE DATABASE IF NOT EXISTS soporte_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE soporte_db;

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
