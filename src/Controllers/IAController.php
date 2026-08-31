<?php
/**
 * Controlador de Inteligencia Artificial
 * Capa de Controladores (IA Controller)
 */

require_once __DIR__ . '/../Models/Ticket.php';
require_once __DIR__ . '/../Services/GeminiService.php';
require_once __DIR__ . '/../Services/LocalExpertService.php';

class IAController {

    /**
     * Generar o recuperar diagnóstico para un ticket
     */
    public static function solucionarTicket() {
        header('Content-Type: application/json; charset=utf-8');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
        $forceRegenerate = isset($_POST['force']) && ($_POST['force'] == '1' || $_POST['force'] === 'true');
        $variant = isset($_POST['variant']) ? (int)$_POST['variant'] : 0;
        $motor = isset($_POST['motor']) ? strtolower(trim($_POST['motor'])) : (isset($_GET['motor']) ? strtolower(trim($_GET['motor'])) : 'gemini');

        if ($id <= 0) {
            echo json_encode(['ok' => false, 'error' => 'ID de solicitud inválido']);
            exit;
        }

        $ticket = Ticket::getById($id);
        if (!$ticket) {
            echo json_encode(['ok' => false, 'error' => 'Ticket no encontrado']);
            exit;
        }

        // Si ya tiene solución en caché y no se pide regenerar
        if (!empty($ticket['solucion_ia']) && !$forceRegenerate) {
            $solucionData = json_decode($ticket['solucion_ia'], true);
            if ($solucionData) {
                echo json_encode([
                    'ok' => true,
                    'cached' => true,
                    'ticket' => $ticket,
                    'solucion' => $solucionData,
                    'variante_index' => $solucionData['variante_index'] ?? 1
                ]);
                exit;
            }
        }

        $tipo = $ticket['tipo_problema'];
        $asunto = $ticket['asunto'];
        $mensaje = $ticket['mensaje'];
        $prioridad = $ticket['prioridad'];

        // Siguiente variante
        $currentVariant = 1;
        if (!empty($ticket['solucion_ia'])) {
            $prev = json_decode($ticket['solucion_ia'], true);
            $prevIndex = $prev['variante_index'] ?? 1;
            $currentVariant = ($prevIndex % 3) + 1;
        }
        if ($variant > 0) $currentVariant = $variant;

        if ($motor === 'local') {
            // Motor 100% Local (0 Tokens)
            $solucion = LocalExpertService::diagnoseTicket($tipo, $prioridad, $asunto, $mensaje, $currentVariant);
        } else {
            // Google Gemini Cloud con fallback local
            $solucion = GeminiService::generateTicketSolution($tipo, $prioridad, $asunto, $mensaje, $currentVariant);
            if (!$solucion) {
                $solucion = LocalExpertService::diagnoseTicket($tipo, $prioridad, $asunto, $mensaje, $currentVariant);
                $solucion['motor_ia'] = 'Motor Experto TI (Local)';
                $solucion['error_ia'] = GeminiService::$lastError;
            }
        }

        $solucion['variante_index'] = $currentVariant;
        $solucion['fecha_generacion'] = date('H:i:s');

        // Persistir en MySQL
        Ticket::saveIASolution($id, $solucion);

        echo json_encode([
            'ok' => true,
            'cached' => false,
            'ticket' => $ticket,
            'solucion' => $solucion,
            'variante_index' => $currentVariant,
            'mensaje' => "Diagnóstico generado con [{$solucion['motor_ia']} - Enfoque #{$currentVariant}]"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Generar análisis estratégico directivo con IA
     */
    public static function analizarEstrategia() {
        header('Content-Type: application/json; charset=utf-8');

        $motor = isset($_GET['motor']) ? strtolower(trim($_GET['motor'])) : (isset($_POST['motor']) ? strtolower(trim($_POST['motor'])) : 'gemini');

        $metrics = Ticket::getMetrics();
        $total = $metrics['total'];
        $estados = $metrics['estados'];
        $prios = $metrics['prioridades'];
        $tipos = $metrics['tipos'];
        
        $ticketsRecientes = Ticket::getAll();
        $muestraReciente = array_slice($ticketsRecientes, 0, 10);

        if ($motor === 'local') {
            $insights = LocalExpertService::generateStrategicInsights($tipos, $prios, $estados, $total);
        } else {
            $insights = GeminiService::generateStrategicAnalysis($tipos, $prios, $estados, $total, $muestraReciente);
            if (!$insights) {
                $insights = LocalExpertService::generateStrategicInsights($tipos, $prios, $estados, $total);
                $insights['motor_ia'] = 'Motor de Inteligencia de Negocio IT (Local)';
            }
        }

        echo json_encode([
            'ok' => true,
            'insights' => $insights
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
