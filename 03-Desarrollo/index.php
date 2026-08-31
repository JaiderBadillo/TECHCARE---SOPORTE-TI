<?php
/**
 * Enrutador Principal / Front Controller
 * TechCare Soporte TI (Arquitectura MVC)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Models/Ticket.php';
require_once __DIR__ . '/src/Controllers/TicketController.php';
require_once __DIR__ . '/src/Controllers/IAController.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? null);
$route = $_GET['route'] ?? ($_POST['route'] ?? null);

// 1. Manejo de Acciones API (JSON)
if ($action) {
    switch ($action) {
        case 'ticket_guardar':
            TicketController::guardar();
            break;
            
        case 'ticket_estado':
            TicketController::actualizarEstado();
            break;
            
        case 'ia_solucion':
            IAController::solucionarTicket();
            break;
            
        case 'ia_analisis':
            IAController::analizarEstrategia();
            break;
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Acción no reconocida: ' . htmlspecialchars($action)]);
            exit;
    }
}

// 2. Manejo de Vistas (HTML)
if ($route === 'dashboard' || $route === 'reporte') {
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

// Vista por defecto: Formulario de Radicación
require_once __DIR__ . '/views/formulario.php';
