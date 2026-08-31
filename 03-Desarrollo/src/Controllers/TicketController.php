<?php
/**
 * Controlador de Tickets
 * Capa de Controladores (Application Controller)
 */

require_once __DIR__ . '/../Models/Ticket.php';
require_once __DIR__ . '/AuthController.php';

class TicketController {

    /**
     * Procesar registro de nuevo ticket (POST)
     */
    public static function guardar() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok' => false, 'error' => 'Método no permitido. Use POST.']);
            exit;
        }

        AuthController::initSession();
        $currentUser = AuthController::getUser();
        $usuario_id = $currentUser ? $currentUser['id'] : null;

        $nombre = trim($_POST['nombre'] ?? ($currentUser['nombre'] ?? ''));
        $email = trim($_POST['email'] ?? ($currentUser['email'] ?? ''));
        $empresa = trim($_POST['empresa'] ?? ($currentUser['empresa'] ?? ''));
        $asunto = trim($_POST['asunto'] ?? '');
        $tipo_problema = trim($_POST['tipo_problema'] ?? 'SOFTWARE');
        $prioridad = trim($_POST['prioridad'] ?? 'media');
        $mensaje = trim($_POST['mensaje'] ?? '');

        // Validaciones
        if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
            echo json_encode(['ok' => false, 'error' => 'Por favor complete todos los campos obligatorios.']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['ok' => false, 'error' => 'El correo electrónico ingresado no es válido.']);
            exit;
        }

        $res = Ticket::create($nombre, $email, $asunto, $tipo_problema, $prioridad, $mensaje, $empresa, $usuario_id);

        if ($res['ok']) {
            echo json_encode([
                'ok' => true,
                'id' => $res['id'],
                'mensaje' => 'Solicitud de soporte #' . $res['id'] . ' registrada correctamente.'
            ]);
        } else {
            echo json_encode([
                'ok' => false,
                'error' => 'Error al registrar la solicitud: ' . ($res['error'] ?? 'Desconocido')
            ]);
        }
        exit;
    }

    /**
     * Actualizar estado del ticket (POST)
     */
    public static function actualizarEstado() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
            exit;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';

        if ($id <= 0 || empty($estado)) {
            echo json_encode(['ok' => false, 'error' => 'Parámetros inválidos']);
            exit;
        }

        $res = Ticket::updateStatus($id, $estado);

        if ($res['ok']) {
            echo json_encode(['ok' => true, 'mensaje' => 'Estado actualizado a: ' . $estado]);
        } else {
            echo json_encode(['ok' => false, 'error' => $res['error'] ?? 'No se pudo actualizar el estado']);
        }
        exit;
    }
}
