<?php
/**
 * Modelo de Datos: User (Clientes, Técnicos y Administradores)
 * Capa de Acceso a Datos (DAO / Active Record)
 */

require_once __DIR__ . '/../../config/database.php';

class User {

    /**
     * Asegura que la tabla de usuarios exista, tenga las columnas empresa/cargo_empresa y los usuarios demo
     */
    public static function ensureTableExists() {
        $conn = Database::getConnection();
        
        // 1. Crear tabla si no existe
        $sql = "CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            empresa VARCHAR(150) NULL,
            cargo_empresa VARCHAR(100) NULL,
            rol ENUM('admin', 'tecnico', 'cliente') NOT NULL DEFAULT 'cliente',
            activo TINYINT(1) DEFAULT 1,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $conn->query($sql);

        // 2. Migración automática de columnas si la tabla ya existía previamente
        $rCols = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'empresa'");
        if ($rCols && $rCols->num_rows === 0) {
            $conn->query("ALTER TABLE usuarios ADD COLUMN empresa VARCHAR(150) NULL AFTER password");
        }

        $rCargo = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'cargo_empresa'");
        if ($rCargo && $rCargo->num_rows === 0) {
            $conn->query("ALTER TABLE usuarios ADD COLUMN cargo_empresa VARCHAR(100) NULL AFTER empresa");
        }

        // Asegurar que el ENUM de rol acepte 'cliente'
        $conn->query("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('admin', 'tecnico', 'cliente') NOT NULL DEFAULT 'cliente'");

        // Asegurar columna usuario_id y empresa en solicitudes
        $rSolUser = $conn->query("SHOW COLUMNS FROM solicitudes LIKE 'usuario_id'");
        if ($rSolUser && $rSolUser->num_rows === 0) {
            $conn->query("ALTER TABLE solicitudes ADD COLUMN usuario_id INT NULL AFTER id");
        }
        $rSolEmp = $conn->query("SHOW COLUMNS FROM solicitudes LIKE 'empresa'");
        if ($rSolEmp && $rSolEmp->num_rows === 0) {
            $conn->query("ALTER TABLE solicitudes ADD COLUMN empresa VARCHAR(150) NULL AFTER email");
        }

        // 3. Comprobar si existe el admin inicial
        $admin = self::findByEmail('admin@techcare.com');
        if (!$admin) {
            $defaultPass = password_hash('admin123', PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, empresa, cargo_empresa, rol) VALUES (?, ?, ?, ?, ?, ?)");
            $n = 'Administrador TI';
            $e = 'admin@techcare.com';
            $emp = 'TechCare Corp';
            $c = 'Director de Infraestructura TI';
            $r = 'admin';
            $stmt->bind_param("ssssss", $n, $e, $defaultPass, $emp, $c, $r);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * Buscar un usuario por su correo electrónico
     */
    public static function findByEmail($email) {
        $conn = Database::getConnection();
        
        $stmt = $conn->prepare("SELECT id, nombre, email, password, empresa, cargo_empresa, rol, activo FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1");
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
        self::ensureTableExists();
        $user = self::findByEmail($email);
        if (!$user) {
            return false;
        }

        // Verificación con hash o fallback para cuentas demo
        if (password_verify($password, $user['password']) || 
           ($email === 'admin@techcare.com' && $password === 'admin123') ||
           ($email === 'cliente@empresa.com' && $password === 'cliente123')) {
            return $user;
        }

        return false;
    }

    /**
     * Registrar un nuevo usuario (Cliente o Técnico)
     */
    public static function register($nombre, $email, $empresa, $cargo_empresa, $password, $rol = 'cliente') {
        self::ensureTableExists();
        $conn = Database::getConnection();
        
        // Verificar si el correo ya existe
        $existe = self::findByEmail($email);
        if ($existe) {
            return ['ok' => false, 'error' => 'Ya existe una cuenta registrada con el correo: ' . htmlspecialchars($email)];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, empresa, cargo_empresa, rol) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre, $email, $hash, $empresa, $cargo_empresa, $rol);
        
        $success = $stmt->execute();
        $nuevoId = $stmt->insert_id;
        $error = $stmt->error;
        $stmt->close();
        
        if ($success) {
            return [
                'ok' => true,
                'id' => $nuevoId,
                'user' => [
                    'id' => $nuevoId,
                    'nombre' => $nombre,
                    'email' => $email,
                    'empresa' => $empresa,
                    'cargo_empresa' => $cargo_empresa,
                    'rol' => $rol
                ]
            ];
        } else {
            return ['ok' => false, 'error' => 'Error al crear la cuenta: ' . $error];
        }
    }
}
