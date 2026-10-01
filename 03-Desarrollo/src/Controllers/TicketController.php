<?php
/**
 * Controlador de Tickets
 * Capa de Controladores (Application Controller)
 */

require_once __DIR__ . '/../Models/Ticket.php';
require_once __DIR__ . '/../Services/N8NService.php';
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
            $ticketId = $res['id'];

            // 🚀 Disparar Webhook a n8n para enviar correo automático de recepción al usuario
            N8NService::notifyTicketCreated([
                'id' => $ticketId,
                'nombre' => $nombre,
                'email' => $email,
                'empresa' => $empresa,
                'asunto' => $asunto,
                'tipo_problema' => $tipo_problema,
                'prioridad' => $prioridad,
                'mensaje' => $mensaje,
                'estado' => 'pendiente'
            ]);

            echo json_encode([
                'ok' => true,
                'id' => $ticketId,
                'mensaje' => 'Solicitud de soporte #' . $ticketId . ' registrada correctamente.'
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
            // Si el estado pasó a resuelto, notificar a n8n si está configurado
            if ($estado === 'resuelto') {
                $ticket = Ticket::getById($id);
                if ($ticket) {
                    N8NService::notifyTicketResolved($ticket);
                }
            }

            echo json_encode(['ok' => true, 'mensaje' => 'Estado actualizado a: ' . $estado]);
        } else {
            echo json_encode(['ok' => false, 'error' => $res['error'] ?? 'No se pudo actualizar el estado']);
        }
        exit;
    }

    /**
     * Procesar retroalimentación interactiva del usuario (CSAT o Devolución de Ticket)
     */
    public static function feedback() {
        $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
        $solucionado = isset($_REQUEST['solucionado']) ? trim(strtolower($_REQUEST['solucionado'])) : '';
        $token = isset($_REQUEST['token']) ? trim($_REQUEST['token']) : '';

        $ticket = Ticket::getById($id);
        if (!$ticket) {
            http_response_code(404);
            die("Solicitud de soporte no encontrada.");
        }

        // Validación de seguridad HMAC
        $expectedToken = hash_hmac('sha256', $ticket['id'] . $ticket['email'], 'techcare_csat_secret_2026');
        $tokenValido = hash_equals($expectedToken, $token);

        // Si es una petición POST (envío de formulario de calificación CSAT o detalle de devolución)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json; charset=utf-8');

            if (!$tokenValido) {
                echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido o expirado.']);
                exit;
            }

            $tipoAccion = trim($_POST['tipo_accion'] ?? 'csat');

            if ($tipoAccion === 'devolver') {
                $motivo = trim($_POST['motivo_devolucion'] ?? '');
                $resDev = Ticket::devolver($id, $motivo);
                if ($resDev['ok']) {
                    N8NService::notifyTicketDevuelto($resDev['ticket'], $motivo);
                    echo json_encode(['ok' => true, 'mensaje' => 'Ticket reabierto con prioridad alta exitosamente.']);
                } else {
                    echo json_encode(['ok' => false, 'error' => $resDev['error'] ?? 'Error al devolver ticket']);
                }
                exit;
            }

            // Calificación CSAT
            $csat = isset($_POST['calificacion_csat']) ? (int)$_POST['calificacion_csat'] : 5;
            $comentario = trim($_POST['comentario_feedback'] ?? '');

            $res = Ticket::guardarFeedback($id, $csat, $comentario);
            if ($res['ok']) {
                echo json_encode(['ok' => true, 'mensaje' => '¡Gracias por calificar nuestra atención!']);
            } else {
                echo json_encode(['ok' => false, 'error' => 'No se pudo guardar la calificación.']);
            }
            exit;
        }

        // Si es una petición GET desde el correo electrónico
        $modo = 'calificar'; // Por defecto mostrar formulario de calificación

        if ($solucionado === 'no' && $tokenValido) {
            // El usuario hizo clic en "No, sigo con el problema": se reabre y devuelve de inmediato
            $resDev = Ticket::devolver($id, 'El usuario indicó desde el correo de resolución que el problema NO fue solucionado.');
            if ($resDev['ok']) {
                $ticket = $resDev['ticket'];
                N8NService::notifyTicketDevuelto($ticket, 'Reapertura automática por el usuario desde el correo electrónico.');
            }
            $modo = 'devuelto';
        }

        // Cargar vista de feedback
        require_once __DIR__ . '/../../views/feedback.php';
        exit;
    }
}
