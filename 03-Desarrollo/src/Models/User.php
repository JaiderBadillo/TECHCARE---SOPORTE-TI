<?php
/**
 * Modelo de Datos: User (Técnicos y Administradores del Sistema)
 * Capa de Acceso a Datos (DAO / Active Record)
 */

require_once __DIR__ . '/../../config/database.php';

class User {

    /**
     * Asegura que la tabla de usuarios exista y tenga el administrador por defecto
     */
    public static function ensureTableExists() {
        $conn = Database::getConnection();
        
        $sql = "CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            rol ENUM('admin', 'tecnico') NOT NULL DEFAULT 'admin',
            activo TINYINT(1) DEFAULT 1,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $conn->query($sql);

        // Comprobar si existe al menos un usuario
        $res = $conn->query("SELECT COUNT(*) as total FROM usuarios");
        $row = $res ? $res->fetch_assoc() : null;
        
        if (!$row || (int)$row['total'] === 0) {
            $defaultPass = password_hash('admin123', PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
            $nombre = 'Administrador TI';
            $email = 'admin@techcare.com';
            $rol = 'admin';
            $stmt->bind_param("ssss", $nombre, $email, $defaultPass, $rol);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * Buscar un usuario por su correo electrónico
     */
    public static function findByEmail($email) {
        self::ensureTableExists();
        $conn = Database::getConnection();
        
        $stmt = $conn->prepare("SELECT id, nombre, email, password, rol, activo FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        return $user;
    }

    /**
     * Autenticar credenciales
     */
    public static function authenticate($email, $password) {
        $user = self::findByEmail($email);
        if (!$user) {
            return false;
        }

        // Validación con password_verify o fallback para claves demo
        if (password_verify($password, $user['password']) || ($email === 'admin@techcare.com' && $password === 'admin123')) {
            return $user;
        }

        return false;
    }

    /**
     * Crear un nuevo usuario
     */
    public static function create($nombre, $email, $password, $rol = 'tecnico') {
        self::ensureTableExists();
        $conn = Database::getConnection();
        
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $email, $hash, $rol);
        $success = $stmt->execute();
        $nuevoId = $stmt->insert_id;
        $stmt->close();
        
        return ['ok' => $success, 'id' => $nuevoId];
    }
}
