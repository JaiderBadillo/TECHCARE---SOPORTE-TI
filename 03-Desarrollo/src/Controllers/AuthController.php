<?php
/**
 * Controlador de Autenticación, Registro y Sesiones
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
     * Comprobar si el usuario actual es Administrador o Técnico
     */
    public static function isAdminOrTecnico() {
        self::initSession();
        if (!self::isAuthenticated()) return false;
        $rol = $_SESSION['user_rol'] ?? 'cliente';
        return in_array($rol, ['admin', 'tecnico'], true);
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
                    'error' => 'Debe iniciar sesión para realizar esta acción.',
                    'redirect' => 'index.php?route=login'
                ]);
                exit;
            }
            header('Location: index.php?route=login');
            exit;
        }
    }

    /**
     * Middleware: Proteger rutas administrativas (Dashboard)
     */
    public static function requireAdmin() {
        self::requireAuth();
        if (!self::isAdminOrTecnico()) {
            header('Location: index.php?route=formulario');
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
                'nombre' => $_SESSION['user_nombre'] ?? 'Usuario',
                'email' => $_SESSION['user_email'] ?? '',
                'empresa' => $_SESSION['user_empresa'] ?? '',
                'cargo_empresa' => $_SESSION['user_cargo'] ?? '',
                'rol' => $_SESSION['user_rol'] ?? 'cliente'
            ];
        }
        return null;
    }

    /**
     * Procesar Registro de Usuario (POST)
     */
    public static function registro() {
        self::initSession();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $empresa = trim($_POST['empresa'] ?? '');
        $cargo = trim($_POST['cargo_empresa'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $password_confirm = trim($_POST['password_confirm'] ?? '');

        if (empty($nombre) || empty($email) || empty($empresa) || empty($password)) {
            echo json_encode(['ok' => false, 'error' => 'Por favor complete todos los campos obligatorios (*).']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['ok' => false, 'error' => 'El correo electrónico ingresado no es válido.']);
            exit;
        }

        if (strlen($password) < 6) {
            echo json_encode(['ok' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres.']);
            exit;
        }

        if ($password !== $password_confirm) {
            echo json_encode(['ok' => false, 'error' => 'Las contraseñas no coinciden.']);
            exit;
        }

        $res = User::register($nombre, $email, $empresa, $cargo, $password, 'cliente');

        if ($res['ok']) {
            // Iniciar sesión automáticamente
            $u = $res['user'];
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['user_nombre'] = $u['nombre'];
            $_SESSION['user_email'] = $u['email'];
            $_SESSION['user_empresa'] = $u['empresa'];
            $_SESSION['user_cargo'] = $u['cargo_empresa'];
            $_SESSION['user_rol'] = $u['rol'];

            echo json_encode([
                'ok' => true,
                'mensaje' => '¡Cuenta creada con éxito! Bienvenido a TechCare Soporte TI.',
                'redirect' => 'index.php?route=formulario'
            ]);
        } else {
            echo json_encode(['ok' => false, 'error' => $res['error'] ?? 'No se pudo crear la cuenta.']);
        }
        exit;
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
            $_SESSION['user_empresa'] = $user['empresa'] ?? '';
            $_SESSION['user_cargo'] = $user['cargo_empresa'] ?? '';
            $_SESSION['user_rol'] = $user['rol'];

            $redirectUrl = ($user['rol'] === 'admin' || $user['rol'] === 'tecnico') 
                ? 'index.php?route=dashboard' 
                : 'index.php?route=formulario';

            echo json_encode([
                'ok' => true,
                'mensaje' => 'Inicio de sesión exitoso. Redirigiendo...',
                'user' => [
                    'nombre' => $user['nombre'],
                    'rol' => $user['rol'],
                    'empresa' => $user['empresa'] ?? ''
                ],
                'redirect' => $redirectUrl
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
