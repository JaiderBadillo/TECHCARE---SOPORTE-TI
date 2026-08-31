<?php
/**
 * Enrutador Principal / Front Controller
 * TechCare Soporte TI (Arquitectura MVC con Autenticación Dual y RBAC)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Models/Ticket.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Controllers/TicketController.php';
require_once __DIR__ . '/src/Controllers/IAController.php';
require_once __DIR__ . '/src/Controllers/AuthController.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? null);
$route = $_GET['route'] ?? ($_POST['route'] ?? null);

// 1. Manejo de Acciones de Autenticación y API (JSON)
if ($action) {
    switch ($action) {
        // Acciones Públicas
        case 'registro':
            AuthController::registro();
            break;

        case 'login':
            AuthController::login();
            break;
            
        case 'logout':
            AuthController::logout();
            break;

        case 'ticket_guardar':
            TicketController::guardar();
            break;

        // Acciones Protegidas para Técnicos y Administradores
        case 'ticket_estado':
            AuthController::requireAdmin();
            TicketController::actualizarEstado();
            break;
            
        case 'ia_solucion':
            AuthController::requireAdmin();
            IAController::solucionarTicket();
            break;
            
        case 'ia_analisis':
            AuthController::requireAdmin();
            IAController::analizarEstrategia();
            break;
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Acción no reconocida: ' . htmlspecialchars($action)]);
            exit;
    }
}

// 2. Manejo de Vistas (HTML)

// Vista de Registro de Usuario
if ($route === 'registro') {
    if (AuthController::isAuthenticated()) {
        header('Location: index.php?route=formulario');
        exit;
    }
    require_once __DIR__ . '/views/registro.php';
    exit;
}

// Vista de Login
if ($route === 'login') {
    if (AuthController::isAuthenticated()) {
        $u = AuthController::getUser();
        $dest = ($u['rol'] === 'admin' || $u['rol'] === 'tecnico') ? 'dashboard' : 'formulario';
        header('Location: index.php?route=' . $dest);
        exit;
    }
    require_once __DIR__ . '/views/login.php';
    exit;
}

// Vista del Dashboard (PROTEGIDA SOLO PARA ADMIN / TÉCNICOS)
if ($route === 'dashboard' || $route === 'reporte') {
    AuthController::requireAdmin();

    // Parámetros de filtrado
    $filtroEstado = trim($_GET['estado'] ?? '');
    $filtroTipo = trim($_GET['tipo'] ?? '');
    $filtroPrioridad = trim($_GET['prioridad'] ?? '');
    $filtroBusqueda = trim($_GET['q'] ?? '');

    $metrics = Ticket::getMetrics();
    $tickets = Ticket::getAll($filtroEstado, $filtroTipo, $filtroPrioridad, $filtroBusqueda);

    require_once __DIR__ . '/views/dashboard.php';
    exit;
}

// Vista por defecto: Portal de Radicación e Historial de Solicitudes
require_once __DIR__ . '/views/formulario.php';
