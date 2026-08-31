-- Esquema de la base de datos: soporte_db
-- Tabla de Usuarios (Clientes y Técnicos/Administradores) y Solicitudes de Soporte

CREATE DATABASE IF NOT EXISTS soporte_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE soporte_db;

-- Tabla de Usuarios (Clientes, Técnicos y Administradores)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    empresa VARCHAR(150) NULL,
    cargo_empresa VARCHAR(100) NULL,
    rol ENUM('admin', 'tecnico', 'cliente') NOT NULL DEFAULT 'cliente',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Solicitudes (Tickets)
CREATE TABLE IF NOT EXISTS solicitudes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    empresa VARCHAR(150) NULL,
    asunto VARCHAR(150) NOT NULL,
    tipo_problema ENUM('RED', 'SOFTWARE', 'HARDWARE', 'SEGURIDAD', 'CLOUD_SERVIDORES', 'BASE_DE_DATOS') NOT NULL DEFAULT 'SOFTWARE',
    prioridad ENUM('baja', 'media', 'alta', 'critica') NOT NULL DEFAULT 'media',
    mensaje TEXT NOT NULL,
    estado ENUM('pendiente', 'en_proceso', 'resuelto') DEFAULT 'pendiente',
    solucion_ia TEXT NULL,
    asignado_a VARCHAR(100) NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario administrador inicial por defecto (Contraseña: admin123)
INSERT IGNORE INTO usuarios (id, nombre, email, password, empresa, cargo_empresa, rol) 
VALUES (1, 'Administrador TI', 'admin@techcare.com', '$2y$10$tZ8g8g6m7QZcQ8K8x4J7.eR5G9BqQj0VzI4t2w8Xl7P6Z1x3M5c6q', 'TechCare Corp', 'Director de Infraestructura TI', 'admin');

-- Usuario cliente demo de prueba (Contraseña: cliente123)
INSERT IGNORE INTO usuarios (id, nombre, email, password, empresa, cargo_empresa, rol) 
VALUES (2, 'Carlos Mendoza', 'cliente@empresa.com', '$2y$10$8g4kFw9U6Yh7m6vK5sL4.e1u9W8x0z1y2A3b4c5d6e7f8g9h0i1j2', 'Logística Global S.A.S', 'Líder de Contabilidad y Facturación', 'cliente');
