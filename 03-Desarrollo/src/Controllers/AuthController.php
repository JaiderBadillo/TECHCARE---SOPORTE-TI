<?php
/**
 * Controlador de Autenticación y Sesiones
 * Capa de Controladores (Auth Controller)
 */

require_once __DIR__ . '/../Models/User.php';

class AuthController {

    /**
     * Iniciar sesión segura de PHP
     */
    public static function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Comprobar si hay una sesión activa
     */
    public static function isAuthenticated() {
        self::initSession();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Middleware: Proteger rutas que requieren autenticación
     */
    public static function requireAuth() {
        if (!self::isAuthenticated()) {
            if (isset($_GET['action']) || isset($_POST['action'])) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'ok' => false,
                    'error' => 'No autorizado. Debe iniciar sesión para realizar esta acción.',
                    'redirect' => 'index.php?route=login'
                ]);
                exit;
            }
            header('Location: index.php?route=login');
            exit;
        }
    }

    /**
     * Obtener el usuario autenticado actualmente
     */
    public static function getUser() {
        self::initSession();
        if (self::isAuthenticated()) {
            return [
                'id' => $_SESSION['user_id'],
                'nombre' => $_SESSION['user_nombre'] ?? 'Administrador',
                'email' => $_SESSION['user_email'] ?? '',
                'rol' => $_SESSION['user_rol'] ?? 'admin'
            ];
        }
        return null;
    }

    /**
     * Procesar inicio de sesión (POST)
     */
    public static function login() {
        self::initSession();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok' => false, 'error' => 'Método no permitido. Use POST.']);
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            echo json_encode(['ok' => false, 'error' => 'Por favor ingrese su correo y contraseña.']);
            exit;
        }

        $user = User::authenticate($email, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_rol'] = $user['rol'];

            echo json_encode([
                'ok' => true,
                'mensaje' => 'Inicio de sesión exitoso. Redirigiendo al panel...',
                'user' => [
                    'nombre' => $user['nombre'],
                    'rol' => $user['rol']
                ],
                'redirect' => 'index.php?route=dashboard'
            ]);
        } else {
            echo json_encode([
                'ok' => false,
                'error' => 'Credenciales incorrectas. Verifique su correo o contraseña.'
            ]);
        }
        exit;
    }

    /**
     * Cerrar sesión
     */
    public static function logout() {
        self::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: index.php?route=login');
        exit;
    }
}
