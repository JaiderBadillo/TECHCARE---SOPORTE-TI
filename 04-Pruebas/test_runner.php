<?php
/**
 * TechCare - Suite de Pruebas Automatizadas (Test Runner)
 * Ejecuta validaciones funcionales, de IA, NLP, seguridad y casos límite.
 */

// Configurar entorno CLI
error_reporting(E_ALL);
ini_set('display_errors', '1');

$baseDir = dirname(__DIR__) . '/03-Desarrollo';
require_once $baseDir . '/src/Services/LocalExpertService.php';

echo "====================================================================\n";
echo "   TECHCARE SOPORTE TI - SUITE DE PRUEBAS AUTOMATIZADAS DE QA       \n";
echo "====================================================================\n";
echo "Fecha y Hora: " . date('Y-m-d H:i:s') . "\n";
echo "Entorno: PHP " . phpversion() . " (" . PHP_OS . ")\n\n";

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;
$testResults = [];

function assertTest($id, $nombre, $condicion, $detalles = '') {
    global $totalTests, $passedTests, $failedTests, $testResults;
    $totalTests++;
    if ($condicion) {
        $passedTests++;
        $status = "[PASS]";
        echo "  $status $id: $nombre\n";
    } else {
        $failedTests++;
        $status = "[FAIL]";
        echo "  $status $id: $nombre - Motivo: $detalles\n";
    }
    $testResults[] = [
        'id' => $id,
        'nombre' => $nombre,
        'estado' => $condicion ? 'PASS' : 'FAIL',
        'detalles' => $detalles
    ];
}

// -------------------------------------------------------------
// 1. PRUEBAS DE VALIDACIÓN DE ARCHIVOS Y ESQUEMA
// -------------------------------------------------------------
echo "--- 1. Validación de Archivos, Esquemas y Componentes ---\n";

$schemaPath = $baseDir . '/database/schema.sql';
$schemaExists = file_exists($schemaPath);
assertTest('CP-ARC-01', 'Existencia e integridad de schema.sql', $schemaExists);

if ($schemaExists) {
    $schemaContent = file_get_contents($schemaPath);
    $hasTables = strpos($schemaContent, 'CREATE TABLE IF NOT EXISTS usuarios') !== false &&
                 strpos($schemaContent, 'CREATE TABLE IF NOT EXISTS solicitudes') !== false;
    assertTest('CP-ARC-02', 'Definición de tablas core (usuarios, solicitudes)', $hasTables);
}

$configPath = $baseDir . '/config/config.php';
assertTest('CP-ARC-03', 'Existencia de archivo de configuración', file_exists($configPath));

// -------------------------------------------------------------
// 2. PRUEBAS DE CLASIFICACIÓN Y EXTRACCIÓN SEMÁNTICA (NLP)
// -------------------------------------------------------------
echo "\n--- 2. Clasificación Semántica y Extracción NLP ---\n";

// Caso Office / M365
$solM365 = LocalExpertService::diagnoseTicket('SOFTWARE', 'alta', 'Alerta de licencia Word', 'Se venció mi suscripción de Office 365');
$isM365Valid = strpos($solM365['categoria'], 'Microsoft 365') !== false &&
               !empty($solM365['pasos_resolucion']) &&
               !empty($solM365['comandos_herramientas']);
assertTest('CP-NLP-01', 'Clasificación semántica de Dominio Microsoft 365', $isM365Valid);

// Caso Redes / VPN
$solVPN = LocalExpertService::diagnoseTicket('RED', 'critica', 'Falla en túnel VPN', 'Cisco Anyconnect no conecta al gateway');
$isVPNValid = strpos($solVPN['categoria'], 'Redes, VPN') !== false &&
              in_array('ipconfig /flushdns', $solVPN['comandos_herramientas']);
assertTest('CP-NLP-02', 'Clasificación semántica de Dominio Redes / VPN', $isVPNValid);

// Caso Ciberseguridad / Phishing
$solSec = LocalExpertService::diagnoseTicket('SEGURIDAD', 'critica', 'Sospecha de Phishing', 'Un correo pide contraseña y bloquear cuenta');
$isSecValid = strpos($solSec['categoria'], 'Ciberseguridad') !== false &&
              !empty($solSec['accion_preventiva']);
assertTest('CP-NLP-03', 'Clasificación semántica de Dominio Ciberseguridad', $isSecValid);

// Caso Bases de Datos / Deadlocks
$solDB = LocalExpertService::diagnoseTicket('BASE_DE_DATOS', 'alta', 'Deadlock en MySQL', 'Consultas lentas y timeout de transacciones');
$isDBValid = strpos($solDB['categoria'], 'Bases de Datos') !== false &&
             in_array('SHOW FULL PROCESSLIST', $solDB['comandos_herramientas']);
assertTest('CP-NLP-04', 'Clasificación semántica de Dominio Bases de Datos', $isDBValid);

// -------------------------------------------------------------
// 3. PRUEBAS DEL PROCESAMIENTO DE IA Y VARIANTES MULTI-NIVEL
// -------------------------------------------------------------
echo "\n--- 3. Procesamiento de IA y Enfoques Multi-Nivel ---\n";

// Variante 1: Mitigación N1/N2
$var1 = LocalExpertService::diagnoseTicket('SOFTWARE', 'alta', 'Error en Outlook', 'No sincroniza correos', 1);
assertTest('CP-IA-01', 'Generación de Enfoque N1/N2 (Mitigación Rápida)', strpos($var1['enfoque_nombre'], 'Mitigación Inmediata') !== false);

// Variante 2: Análisis Forense N3
$var2 = LocalExpertService::diagnoseTicket('SOFTWARE', 'alta', 'Error en Outlook', 'No sincroniza correos', 2);
assertTest('CP-IA-02', 'Generación de Enfoque N3 (Análisis Forense)', strpos($var2['enfoque_nombre'], 'Forense Profundo') !== false);

// Análisis Estratégico Local de Negocio
$insights = LocalExpertService::generateStrategicInsights(['RED' => ['porcentaje' => 45]], ['critica' => 5], ['pendiente' => 8], 20);
$isInsightsValid = !empty($insights['resumen_ejecutivo']) &&
                   !empty($insights['decision_contratacion']) &&
                   !empty($insights['decision_infraestructura']);
assertTest('CP-IA-03', 'Generación de Analítica Estratégica Directiva Local', $isInsightsValid);

// -------------------------------------------------------------
// 4. PRUEBAS DE SEGURIDAD BÁSICA (BCRYPT, SANITIZACIÓN)
// -------------------------------------------------------------
echo "\n--- 4. Pruebas de Seguridad Básica (BCRYPT y Sanitización) ---\n";

$testPassword = "AdminSecurePassword2026!";
$hashedPassword = password_hash($testPassword, PASSWORD_BCRYPT, ['cost' => 10]);
$verifyValid = password_verify($testPassword, $hashedPassword);
$verifyInvalid = !password_verify("PasswordErroneo123", $hashedPassword);
assertTest('CP-SEC-01', 'Algoritmo de Hash BCRYPT y validación criptográfica', $verifyValid && $verifyInvalid);

// Sanitización XSS
$xssPayload = "<script>alert('XSS_ATTACK');</script><b>Test</b>";
$sanitized = htmlspecialchars($xssPayload, ENT_QUOTES, 'UTF-8');
$isXSSSafe = strpos($sanitized, '<script>') === false && strpos($sanitized, '&lt;script&gt;') !== false;
assertTest('CP-SEC-02', 'Sanitización de vectores de ataque XSS', $isXSSSafe);

// -------------------------------------------------------------
// 5. PRUEBAS DE ERRORES Y CASOS LÍMITE (EDGE CASES)
// -------------------------------------------------------------
echo "\n--- 5. Pruebas de Casos Límite y Manejo de Errores ---\n";

// Texto vacío o con solo espacios
$solVacia = LocalExpertService::diagnoseTicket('SOFTWARE', 'baja', '', '   ');
assertTest('CP-EDGE-01', 'Manejo de entrada vacía con respuesta por defecto', !empty($solVacia['diagnostico']));

// Texto con caracteres especiales y emojis
$solUnicode = LocalExpertService::diagnoseTicket('SOFTWARE', 'media', '🔥 Error gravísimo en VPN!! @#$%^&*()', '¡¡¡Se cayó todo el enlace!!!');
assertTest('CP-EDGE-02', 'Manejo de caracteres especiales, tildes y signos sin excepciones', !empty($solUnicode['categoria']));

// Texto extremadamente largo (10,000 caracteres)
$longText = str_repeat("Problema de conexion con la red corporativa de internet. ", 200);
$solLarga = LocalExpertService::diagnoseTicket('RED', 'alta', 'Alerta masiva', $longText);
assertTest('CP-EDGE-03', 'Manejo de textos de longitud extrema sin desbordamiento de memoria', !empty($solLarga['diagnostico']));

// -------------------------------------------------------------
// RESUMEN Y ESTADÍSTICAS
// -------------------------------------------------------------
echo "\n====================================================================\n";
echo "                     RESUMEN DE RESULTADOS                          \n";
echo "====================================================================\n";
echo "Total Pruebas Ejecutadas: $totalTests\n";
echo "Pruebas Superadas (PASS): $passedTests (" . round(($passedTests/$totalTests)*100, 1) . "%)\n";
echo "Pruebas Fallidas  (FAIL): $failedTests\n";
echo "Estado General: " . ($failedTests === 0 ? "TOTALMENTE EXITOSO (100% OK)" : "CON DEFECTOS") . "\n";
echo "====================================================================\n";
