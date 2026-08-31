<?php
require_once __DIR__ . '/config.php';

// Variable global para capturar el último error de la API
$GLOBALS['LAST_GEMINI_ERROR'] = null;

/**
 * Cliente HTTP para invocar Google Gemini API con auto-selección de modelos
 *
 * @param string $prompt Prompt con las instrucciones y datos
 * @param string $systemInstruction Rol del sistema (opcional)
 * @return array|null Datos decodificados en JSON o null si hay error
 */
function callGeminiAPI($prompt, $systemInstruction = null) {
    global $LAST_GEMINI_ERROR;
    $LAST_GEMINI_ERROR = null;

    if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY) || GEMINI_API_KEY === 'TU_API_KEY_AQUI') {
        $LAST_GEMINI_ERROR = "No se ha configurado la API Key de Gemini en config.php";
        return null;
    }

    $apiKey = trim(GEMINI_API_KEY);

    // Modelos a probar en orden de preferencia (catálogo activo 2026)
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
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
                
                // Limpiar delimitadores markdown si existen
                $cleanJson = trim($rawText);
                if (str_starts_with($cleanJson, '```json')) {
                    $cleanJson = substr($cleanJson, 7);
                }
                if (str_starts_with($cleanJson, '```')) {
                    $cleanJson = substr($cleanJson, 3);
                }
                if (str_ends_with($cleanJson, '```')) {
                    $cleanJson = substr($cleanJson, 0, -3);
                }
                $cleanJson = trim($cleanJson);

                $parsed = json_decode($cleanJson, true);
                if (is_array($parsed)) {
                    $parsed['motor_ia'] = "Google Gemini ($model - Cloud)";
                    return $parsed;
                }
            }
        }

        // Si dio error HTTP, extraer el mensaje de Google
        $respJson = json_decode($response, true);
        $msg = $respJson['error']['message'] ?? "HTTP $httpCode";
        $lastErrorText = "Google API (HTTP $httpCode): $msg";

        // Si el error es de clave inválida (400 o 403), no seguir probando otros modelos
        if ($httpCode === 400 || $httpCode === 403) {
            break;
        }
    }

    $LAST_GEMINI_ERROR = $lastErrorText;
    error_log("Gemini API Error: " . $lastErrorText);
    return null;
}

