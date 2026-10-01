<?php
/**
 * Servicio de Integración con n8n (Webhooks & Automatización)
 * Capa de Servicios de TechCare Soporte TI
 * 
 * Permite enviar eventos asíncronos y desacoplados a flujos de trabajo n8n
 * para la automatización de envíos de correo, alertas y notificaciones a usuarios.
 */

class N8NService {

    public static $lastError = null;

    /**
     * Notificar a n8n cuando un usuario radica un nuevo ticket
     * Envía payload para disparar correo de confirmación de recepción al usuario.
     * 
     * @param array $ticketData Datos del ticket recién registrado
     * @return bool True si se ejecutó correctamente o si la integración está inactiva
     */
    public static function notifyTicketCreated(array $ticketData) {
        $enabled = defined('N8N_WEBHOOK_ENABLED') ? (bool)N8N_WEBHOOK_ENABLED : false;
        $webhookUrl = defined('N8N_WEBHOOK_URL') ? trim(N8N_WEBHOOK_URL) : '';

        // Si no está habilitado o la URL está vacía, no bloquea el flujo
        if (!$enabled || empty($webhookUrl)) {
            return true;
        }

        $appUrl = defined('APP_URL') ? rtrim(APP_URL, '/') : 'http://127.0.0.1:8000';

        $payload = [
            'evento' => 'ticket_creado',
            'timestamp' => date('Y-m-d H:i:s'),
            'ticket_id' => $ticketData['id'] ?? 0,
            'nombre' => $ticketData['nombre'] ?? 'Usuario',
            'email' => $ticketData['email'] ?? '',
            'empresa' => $ticketData['empresa'] ?? 'Particular',
            'asunto' => $ticketData['asunto'] ?? 'Sin Asunto',
            'tipo_problema' => $ticketData['tipo_problema'] ?? 'SOFTWARE',
            'prioridad' => $ticketData['prioridad'] ?? 'media',
            'mensaje' => $ticketData['mensaje'] ?? '',
            'estado' => $ticketData['estado'] ?? 'pendiente',
            'url_seguimiento' => $appUrl . '/index.php?action=formulario',
            'mensaje_notificacion' => 'Tu solicitud de soporte ha sido recibida exitosamente en TechCare. Nuestro equipo técnico ya se encuentra analizando tu caso y te contactaremos en cuanto quede resuelto.'
        ];

        return self::sendWebhook($webhookUrl, $payload);
    }

    /**
     * Notificar a n8n cuando un ticket pasa a estado 'resuelto'
     * 
     * @param array $ticketData Datos del ticket resuelto
     * @return bool
     */
    public static function notifyTicketResolved(array $ticketData) {
        $enabled = defined('N8N_WEBHOOK_ENABLED') ? (bool)N8N_WEBHOOK_ENABLED : false;
        
        // Si hay URL específica de resuelto se usa, si no, se usa el webhook principal
        $webhookUrl = defined('N8N_WEBHOOK_RESOLVED_URL') && !empty(N8N_WEBHOOK_RESOLVED_URL) 
            ? trim(N8N_WEBHOOK_RESOLVED_URL) 
            : (defined('N8N_WEBHOOK_URL') ? trim(N8N_WEBHOOK_URL) : '');

        if (!$enabled || empty($webhookUrl)) {
            return true;
        }

        $appUrl = defined('APP_URL') ? rtrim(APP_URL, '/') : 'http://127.0.0.1:8000';
        $ticketId = $ticketData['id'] ?? 0;
        $userEmail = $ticketData['email'] ?? '';
        $token = hash_hmac('sha256', $ticketId . $userEmail, 'techcare_csat_secret_2026');

        $urlFeedbackSi = $appUrl . '/index.php?route=feedback&id=' . $ticketId . '&solucionado=si&token=' . $token;
        $urlFeedbackNo = $appUrl . '/index.php?route=feedback&id=' . $ticketId . '&solucionado=no&token=' . $token;

        $payload = [
            'evento' => 'ticket_resuelto',
            'timestamp' => date('Y-m-d H:i:s'),
            'ticket_id' => $ticketId,
            'nombre' => $ticketData['nombre'] ?? 'Usuario',
            'email' => $userEmail,
            'empresa' => $ticketData['empresa'] ?? 'Particular',
            'asunto' => $ticketData['asunto'] ?? '',
            'tipo_problema' => $ticketData['tipo_problema'] ?? 'SOFTWARE',
            'prioridad' => $ticketData['prioridad'] ?? 'media',
            'mensaje' => $ticketData['mensaje'] ?? '',
            'estado' => 'resuelto',
            'asignado_a' => $ticketData['asignado_a'] ?? 'Equipo de Soporte TI',
            'url_seguimiento' => $appUrl . '/index.php?action=formulario',
            'url_feedback_si' => $urlFeedbackSi,
            'url_feedback_no' => $urlFeedbackNo,
            'mensaje_notificacion' => '¡Buenas noticias! Tu solicitud de soporte técnico ha sido resuelta exitosamente por nuestro equipo de TI.'
        ];

        return self::sendWebhook($webhookUrl, $payload);
    }

    /**
     * Notificar a n8n cuando un usuario devuelve / reabre un ticket no resuelto
     * Dispara alerta de prioridad urgente para el equipo de soporte
     * 
     * @param array $ticketData Datos del ticket devuelto
     * @param string $motivo Motivo u observación indicada por el usuario
     * @return bool
     */
    public static function notifyTicketDevuelto(array $ticketData, $motivo = '') {
        $enabled = defined('N8N_WEBHOOK_ENABLED') ? (bool)N8N_WEBHOOK_ENABLED : false;
        $webhookUrl = defined('N8N_WEBHOOK_URL') ? trim(N8N_WEBHOOK_URL) : '';

        if (!$enabled || empty($webhookUrl)) {
            return true;
        }

        $appUrl = defined('APP_URL') ? rtrim(APP_URL, '/') : 'http://127.0.0.1:8000';

        $payload = [
            'evento' => 'ticket_devuelto',
            'timestamp' => date('Y-m-d H:i:s'),
            'ticket_id' => $ticketData['id'] ?? 0,
            'nombre' => $ticketData['nombre'] ?? 'Usuario',
            'email' => $ticketData['email'] ?? '',
            'empresa' => $ticketData['empresa'] ?? 'Particular',
            'asunto' => $ticketData['asunto'] ?? '',
            'tipo_problema' => $ticketData['tipo_problema'] ?? 'SOFTWARE',
            'prioridad' => $ticketData['prioridad'] ?? 'alta',
            'motivo_devolucion' => $motivo,
            'estado' => 'en_proceso',
            'devuelto' => 1,
            'url_dashboard' => $appUrl . '/index.php?route=dashboard&estado=devuelto',
            'mensaje_alerta' => 'ALERTA: El usuario ha devuelto el ticket #' . ($ticketData['id'] ?? 0) . ' indicando que el problema NO fue resuelto. Se ha escalado a prioridad ALTA.'
        ];

        return self::sendWebhook($webhookUrl, $payload);
    }

    /**
     * Despacho HTTP POST seguro hacia n8n con cURL y timeout estricto
     */
    private static function sendWebhook($url, array $payload) {
        try {
            $ch = curl_init($url);
            $jsonData = json_encode($payload, JSON_UNESCAPED_UNICODE);

            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $jsonData,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonData),
                    'User-Agent: TechCare-Soporte-TI/2.2'
                ],
                CURLOPT_TIMEOUT => 3,        // Máximo 3 segundos para no ralentizar la interfaz
                CURLOPT_CONNECTTIMEOUT => 2, // 2 segundos de conexión
                CURLOPT_SSL_VERIFYPEER => false
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                self::$lastError = "Error de conexión cURL: " . $curlError;
                error_log("[TechCare n8n] Fallo de conexión: " . $curlError);
                return false;
            }

            if ($httpCode >= 200 && $httpCode < 300) {
                return true;
            }

            self::$lastError = "HTTP Code: {$httpCode} - Respuesta: " . $response;
            error_log("[TechCare n8n] Webhook retornó código {$httpCode}: " . $response);
            return false;

        } catch (Exception $e) {
            self::$lastError = $e->getMessage();
            error_log("[TechCare n8n] Excepción capturada: " . $e->getMessage());
            return false;
        }
    }
}
