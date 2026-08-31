<?php
/**
 * Servicio de Inteligencia Artificial: Google Gemini Cloud
 * Capa de Servicios Externos
 */

require_once __DIR__ . '/../../config/config.php';

class GeminiService {
    public static $lastError = null;

    /**
     * Realiza la llamada HTTP a la API de Google Gemini con auto-recuperación de modelos
     */
    public static function generateContent($prompt, $systemInstruction = null) {
        self::$lastError = null;

        if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY) || GEMINI_API_KEY === 'TU_API_KEY_AQUI') {
            self::$lastError = "No se ha configurado la clave GEMINI_API_KEY en config/config.php";
            return null;
        }

        $apiKey = trim(GEMINI_API_KEY);

        // Catálogo de modelos activos ordenados por preferencia
        $candidateModels = [
            defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-3.7-flash',
            'gemini-3.7-flash',
            'gemini-3.5-flash',
            'gemini-3.1-pro-preview',
            'gemini-3-flash-preview',
            'gemini-3.1-flash-lite'
        ];
        $candidateModels = array_unique($candidateModels);

        $body = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'responseMimeType' => 'application/json'
            ]
        ];

        if ($systemInstruction) {
            $body['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ];
        }

        $lastErrorText = '';

        foreach ($candidateModels as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_UNICODE));
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                $lastErrorText = "Error de red cURL: $curlError";
                continue;
            }

            if ($httpCode === 200 && $response) {
                $json = json_decode($response, true);
                if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                    $rawText = $json['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Limpieza de delimitadores markdown
                    $cleanJson = trim($rawText);
                    if (str_starts_with($cleanJson, '```json')) $cleanJson = substr($cleanJson, 7);
                    if (str_starts_with($cleanJson, '```')) $cleanJson = substr($cleanJson, 3);
                    if (str_ends_with($cleanJson, '```')) $cleanJson = substr($cleanJson, 0, -3);
                    $cleanJson = trim($cleanJson);

                    $parsed = json_decode($cleanJson, true);
                    if (is_array($parsed)) {
                        $parsed['motor_ia'] = "Google Gemini ($model - Cloud)";
                        return $parsed;
                    }
                }
            }

            // Capturar mensaje de error
            $respJson = json_decode($response, true);
            $msg = $respJson['error']['message'] ?? "HTTP $httpCode";
            $lastErrorText = "Google API (HTTP $httpCode): $msg";

            if ($httpCode === 400 || $httpCode === 403) {
                break;
            }
        }

        self::$lastError = $lastErrorText;
        error_log("GeminiService Error: " . $lastErrorText);
        return null;
    }

    /**
     * Generar solución y diagnóstico técnico con Gemini
     */
    public static function generateTicketSolution($tipo, $prioridad, $asunto, $mensaje, $variant = 1) {
        $enfoques = [
            1 => 'Solución Rápida en Caliente & Mitigación Inmediata (N1/N2)',
            2 => 'Análisis Forense Profundo & Diagnóstico de Causa Raíz (N3)',
            3 => 'Reconfiguración Arquitectural, Endurecimiento & Prevención Definitiva'
        ];
        $nombreEnfoque = $enfoques[$variant] ?? $enfoques[1];

        $systemInstruction = "Eres un Ingeniero Principal de Soporte TI, Ciberseguridad y Redes. Diagnostica solicitudes de soporte y responde estrictamente en JSON válido.";

        $prompt = <<<EOT
Analiza el siguiente incidente de soporte técnico y genera una solución técnica estructurada:
- Asunto: {$asunto}
- Descripción: {$mensaje}
- Categoría: {$tipo}
- Prioridad: {$prioridad}
- Nivel de Enfoque requerido: Variante #{$variant} ({$nombreEnfoque})

Debes responder ÚNICAMENTE con un JSON con los siguientes campos exactos en español:
{
  "enfoque_nombre": "{$nombreEnfoque}",
  "categoria": "Categoría descriptiva del problema",
  "diagnostico": "Explicación clara y profesional de lo que está sucediendo técnica y operativamente",
  "causa_probable": "Causa raíz más probable que originó la falla",
  "tiempo_estimado": "Ejemplo: 15 - 30 minutos",
  "pasos_resolucion": [
    "Paso 1: Detalle técnico...",
    "Paso 2: Detalle técnico...",
    "Paso 3: Detalle técnico...",
    "Paso 4: Detalle técnico..."
  ],
  "comandos_herramientas": [
    "Comando o herramienta 1",
    "Comando o herramienta 2"
  ],
  "accion_preventiva": "Recomendación para evitar que el incidente vuelva a suceder",
  "motor_ia": "Google Gemini 3.x Flash (Cloud)"
}
EOT;

        $res = self::generateContent($prompt, $systemInstruction);
        if ($res && isset($res['diagnostico'], $res['pasos_resolucion'])) {
            if (!isset($res['enfoque_nombre'])) $res['enfoque_nombre'] = $nombreEnfoque;
            return $res;
        }
        return null;
    }

    /**
     * Generar análisis estratégico directivo con Gemini
     */
    public static function generateStrategicAnalysis($tipos, $prios, $estados, $total, $ticketsRecientes) {
        $systemInstruction = "Eres el Director Global de Tecnología (CTO) y Consultor Senior en Gestión de Servicios TI (ITIL 4 / COBIT). Analiza métricas de mesa de ayuda y prescribe decisiones estratégicas de negocio en JSON.";

        $contextoJson = json_encode([
            'total_tickets' => $total,
            'distribucion_estados' => $estados,
            'distribucion_prioridades' => $prios,
            'distribucion_tipos' => $tipos,
            'muestra_tickets' => $ticketsRecientes
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $prompt = <<<EOT
Analiza los datos agregados de incidentes técnicos y genera un dictamen ejecutivo y plan de decisiones en formato JSON:
{$contextoJson}

Debes responder ÚNICAMENTE con un objeto JSON válido con los campos:
{
  "resumen_ejecutivo": "Dictamen global de la situación técnica del área...",
  "decision_contratacion": [
    {
      "perfil": "Título del cargo",
      "especialidad": "RED / SOFTWARE / SEGURIDAD...",
      "seniority": "Junior / Semi-Senior / Senior",
      "prioridad_contratacion": "Urgente / Alta / Media",
      "certificaciones_clave": ["Cert 1", "Cert 2"],
      "justificacion": "Por qué se necesita...",
      "impacto_esperado": "Resultado en KPIs..."
    }
  ],
  "decision_infraestructura": [
    {
      "inversion": "Nombre del equipamiento o software",
      "categoria": "Categoría",
      "costo_estimado": "Rango de costo en USD",
      "justificacion": "Motivo técnico...",
      "retorno_inversion": "Beneficio tangible..."
    }
  ],
  "decision_capacitacion": [
    {
      "tema": "Nombre del curso o taller",
      "audiencia": "A quién está dirigido",
      "duracion": "Ej: 16 Horas",
      "objetivo": "Objetivo técnico pedagógico..."
    }
  ],
  "decision_automatizacion": [
    {
      "iniciativa": "Nombre del proyecto de automatización",
      "alcance": "Alcance",
      "ahorro_tiempo": "Horas/mes estimadas",
      "descripcion": "Descripción funcional..."
    }
  ]
}
EOT;

        $res = self::generateContent($prompt, $systemInstruction);
        if ($res && isset($res['resumen_ejecutivo'], $res['decision_contratacion'])) {
            return $res;
        }
        return null;
    }
}
