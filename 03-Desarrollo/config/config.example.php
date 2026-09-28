<?php
/**
 * Plantilla de Configuración del Sistema de Soporte TI
 * Copia este archivo como 'config/config.php' y coloca tu API Key de Google Gemini
 */

// Clave de API de Google Gemini (Obtenla en: https://aistudio.google.com/app/apikey)
define('GEMINI_API_KEY', 'TU_API_KEY_DE_GEMINI_AQUI');

// Modelo de Gemini a utilizar
define('GEMINI_MODEL', 'gemini-3.7-flash');

define('APP_ENV', 'production');
define('APP_NAME', 'TechCare Soporte TI');
define('APP_URL', 'http://127.0.0.1:8000');

// ==========================================
// Integración de Automatización con n8n
// ==========================================
// Activar o desactivar el envío de Webhooks a n8n (true para activar)
define('N8N_WEBHOOK_ENABLED', false);

// URL del Webhook en tu instancia de n8n para nuevos tickets radicados
define('N8N_WEBHOOK_URL', 'http://localhost:5678/webhook/techcare-ticket-creado');

// URL opcional de n8n para cuando un ticket es resuelto
define('N8N_WEBHOOK_RESOLVED_URL', 'http://localhost:5678/webhook/techcare-ticket-resuelto');
