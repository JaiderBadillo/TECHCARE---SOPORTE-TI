<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/gemini_client.php';

$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['ok' => false, 'error' => 'Error de conexión']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
$forceRegenerate = isset($_POST['force']) && ($_POST['force'] == '1' || $_POST['force'] === 'true');
$variant = isset($_POST['variant']) ? (int)$_POST['variant'] : 0;

if ($id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'ID de solicitud inválido']);
    exit;
}

$stmt = $conn->prepare("SELECT id, nombre, email, asunto, tipo_problema, prioridad, mensaje, estado, solucion_ia, fecha_creacion FROM solicitudes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$ticket) {
    echo json_encode(['ok' => false, 'error' => 'Ticket no encontrado']);
    exit;
}

$motor = isset($_POST['motor']) ? strtolower(trim($_POST['motor'])) : (isset($_GET['motor']) ? strtolower(trim($_GET['motor'])) : 'gemini');

// Si ya tiene solución guardada y NO se pide regenerar
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

// Generación con IA (Google Gemini o Motor Local a elección del usuario)
$tipo = $ticket['tipo_problema'];
$asunto = $ticket['asunto'];
$mensaje = $ticket['mensaje'];
$prioridad = $ticket['prioridad'];

// Si se pide regenerar, calcular el siguiente índice de variante (1, 2 o 3)
$currentVariant = 1;
if (!empty($ticket['solucion_ia'])) {
    $prev = json_decode($ticket['solucion_ia'], true);
    $prevIndex = $prev['variante_index'] ?? 1;
    $currentVariant = ($prevIndex % 3) + 1;
}

if ($variant > 0) {
    $currentVariant = $variant;
}

if ($motor === 'local') {
    // Modo ahorro de tokens: 100% local sin llamada a la API
    $solucion = generarSolucionIT($tipo, $prioridad, $asunto, $mensaje, $currentVariant);
    $solucion['motor_ia'] = 'Motor Experto TI (Local - 0 Tokens)';
} else {
    // Modo Cloud: Google Gemini API con fallback local si falla
    $solucion = generarSolucionConGemini($tipo, $prioridad, $asunto, $mensaje, $currentVariant);
    if (!$solucion) {
        global $LAST_GEMINI_ERROR;
        $solucion = generarSolucionIT($tipo, $prioridad, $asunto, $mensaje, $currentVariant);
        $solucion['motor_ia'] = 'Motor Experto TI (Local)';
        $solucion['error_ia'] = $LAST_GEMINI_ERROR;
    }
}

$solucion['variante_index'] = $currentVariant;
$solucion['fecha_generacion'] = date('H:i:s');

// Guardar en la base de datos la nueva solución regenerada
$solucionJson = json_encode($solucion, JSON_UNESCAPED_UNICODE);
$stmtUpdate = $conn->prepare("UPDATE solicitudes SET solucion_ia = ? WHERE id = ?");
$stmtUpdate->bind_param("si", $solucionJson, $id);
$stmtUpdate->execute();
$stmtUpdate->close();

$conn->close();

echo json_encode([
    'ok' => true,
    'cached' => false,
    'ticket' => $ticket,
    'solucion' => $solucion,
    'variante_index' => $currentVariant,
    'mensaje' => "Diagnóstico generado con [{$solucion['motor_ia']} - Enfoque #{$currentVariant}]: {$solucion['enfoque_nombre']}"
]);

/**
 * Consulta a Google Gemini para diagnóstico personalizado
 */
function generarSolucionConGemini($tipo, $prioridad, $asunto, $mensaje, $variant = 1) {
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
  "motor_ia": "Google Gemini 1.5 Flash (Cloud)"
}
EOT;

    $res = callGeminiAPI($prompt, $systemInstruction);
    if ($res && isset($res['diagnostico'], $res['pasos_resolucion'])) {
        if (!isset($res['enfoque_nombre'])) {
            $res['enfoque_nombre'] = $nombreEnfoque;
        }
        if (!isset($res['motor_ia'])) {
            $res['motor_ia'] = 'Google Gemini 1.5 Flash (Cloud)';
        }
        return $res;
    }
    return null;
}

/**
 * Motor de Diagnóstico y Solución Técnica TI con Enfoques Alternativos (Modo Local Experto Avanzado)
 * Analiza semánticamente el contenido del ticket para detectar el dominio específico
 */
function generarSolucionIT($tipo, $prioridad, $asunto, $mensaje, $variant = 1) {
    $texto = mb_strtolower($asunto . ' ' . $mensaje, 'UTF-8');

    // Nombres de los enfoques según variante
    $enfoques = [
        1 => 'Solución Rápida en Caliente & Mitigación Inmediata (N1/N2)',
        2 => 'Análisis Forense Profundo & Diagnóstico de Causa Raíz (N3)',
        3 => 'Reconfiguración Arquitectural, Endurecimiento & Prevención Definitiva'
    ];
    $nombreEnfoque = $enfoques[$variant] ?? $enfoques[1];

    // =========================================================================
    // 1. DOMINIO: MICROSOFT 365 / OFFICE / LICENCIAS / CORREO OUTLOOK
    // =========================================================================
    if (preg_match('/(365|office|word|excel|powerpoint|licencia|vencer|vencimiento|renovar|suscripci|outlook|exchange|teams|onedrive|entra id|azure ad)/i', $texto)) {
        if ($variant === 1) {
            return [
                'enfoque_nombre' => $nombreEnfoque,
                'categoria' => 'Microsoft 365 & Licenciamiento Cloud (Mitigación Rápida)',
                'diagnostico' => 'Alerta de expiración de suscripción o token de activación caducado en la suite Microsoft 365 Apps for Enterprise, dejando las aplicaciones en modo de solo lectura.',
                'causa_probable' => '1. Desincronización del token OAuth de Microsoft Entra ID (Azure AD) en el perfil de Windows. 2. Licencia no renovada o reasignada en el portal de administración. 3. Conflicto de múltiples cuentas Microsoft en Credential Manager.',
                'tiempo_estimado' => '10 - 20 minutos',
                'pasos_resolucion' => [
                    'Paso 1: Cerrar todas las aplicaciones de Office (Word, Excel, Outlook) desde el Administrador de Tareas.',
                    'Paso 2: Abrir el Administrador de Credenciales de Windows (control keymgr.cpl) y eliminar todas las credenciales genéricas que inicien por "MicrosoftOffice16_Data:".',
                    'Paso 3: Ejecutar el script OSPP.vbs para limpiar la clave de activación temporal: cscript "C:\Program Files\Microsoft Office\Office16\OSPP.VBS" /dstatus y remover con /unpkey:<5_caracteres>.',
                    'Paso 4: Verificar en el Centro de Administración de Microsoft 365 (admin.microsoft.com) que el usuario tenga asignada una licencia activa (Microsoft 365 Business Standard / E3 / E5).',
                    'Paso 5: Abrir Word, iniciar sesión con el correo corporativo y revalidar la activación.'
                ],
                'comandos_herramientas' => [
                    'cscript "C:\Program Files\Microsoft Office\Office16\OSPP.VBS" /dstatus',
                    'dsregcmd /status',
                    'control keymgr.cpl',
                    'Microsoft 365 Admin Center (admin.microsoft.com)'
                ],
                'accion_preventiva' => 'Configurar alertas automáticas de facturación con 30 días de anticipación en el portal de facturación de M365 y habilitar grupos dinámicos de licencias basadas en departamento.'
            ];
        } elseif ($variant === 2) {
            return [
                'enfoque_nombre' => $nombreEnfoque,
                'categoria' => 'Microsoft 365 & Identidad Híbrida (Análisis Forense N3)',
                'diagnostico' => 'Falla en el Primary Refresh Token (PRT) de Microsoft Entra ID o bloqueo de políticas de Acceso Condicional (Conditional Access) que impiden la renovación de tokens criptográficos.',
                'causa_probable' => '1. Estado "AzureAdJoined: NO" o certificado de dispositivo TPM corrupto. 2. Bloqueo por regla de Acceso Condicional basada en ubicación IP o cumplimiento de dispositivo Intune. 3. Desfase de sincronización en Microsoft Entra Connect.',
                'tiempo_estimado' => '30 - 45 minutos',
                'pasos_resolucion' => [
                    'Paso 1: Ejecutar "dsregcmd /status" en CMD como administrador y verificar los campos "AzureAdPrt: YES" y "DeviceId".',
                    'Paso 2: Inspeccionar los registros de inicio de sesión en el Centro de Administración de Microsoft Entra (entra.microsoft.com -> Sign-in logs) para filtrar errores 50058 o 53003 (Conditional Access Failure).',
                    'Paso 3: Si el estado PRT es negativo, desconectar y reconectar la cuenta corporativa desde Configuración -> Cuentas -> Obtener acceso a trabajo o escuela.',
                    'Paso 4: Limpiar la caché de identidad de IdentityCRL y borrar la carpeta "%localappdata%\Microsoft\OneAuth" y "%localappdata%\Microsoft\IdentityCache".',
                    'Paso 5: Ejecutar la herramienta Microsoft Support and Recovery Assistant (SaRA) con el módulo de activación de Office.'
                ],
                'comandos_herramientas' => [
                    'dsregcmd /status',
                    'dsregcmd /leave && dsregcmd /join',
                    'Microsoft Support and Recovery Assistant (SaRA)',
                    'Microsoft Entra Sign-In Logs (Error 50058/53003)'
                ],
                'accion_preventiva' => 'Implementar políticas de Acceso Condicional con período de gracia de 14 días para renovación de tokens y desplegar Microsoft Defender for Endpoint para validar el cumplimiento del dispositivo.'
            ];
        } else {
            return [
                'enfoque_nombre' => $nombreEnfoque,
                'categoria' => 'Microsoft 365 & Arquitectura de Identidad (Endurecimiento Definitivo)',
                'diagnostico' => 'Inconsistencia en el ciclo de vida de identidades y aprovisionamiento manual de licencias SaaS.',
                'causa_probable' => '1. Falta de Group-Based Licensing (licenciamiento basado en grupos). 2. Políticas de expiración de contraseñas no alineadas con NIST 800-63B que invalidan sesiones. 3. Ausencia de monitoreo centralizado de consumo de seats.',
                'tiempo_estimado' => '45 - 60 minutos',
                'pasos_resolucion' => [
                    'Paso 1: Migrar la asignación manual de licencias a "Group-Based Licensing" en Microsoft Entra ID vinculado a unidades organizativas de Active Directory.',
                    'Paso 2: Configurar una política de Acceso Condicional que exija autenticación multifactor (MFA FIDO2 / Authenticator) sin cerrar sesiones de aplicaciones de escritorio de confianza.',
                    'Paso 3: Automatizar un script en PowerShell con Microsoft.Graph para auditar semanalmente usuarios sin licencia activa o licencias asignadas inactivas por más de 60 días.',
                    'Paso 4: Desplegar mediante Intune o GPO la configuración "EnableADAL = 1" y "DisableAADWAM = 0" para forzar Modern Authentication en toda la flota.'
                ],
                'comandos_herramientas' => [
                    'Get-MgUserLicenseDetail -UserId <UserPrincipalName>',
                    'Connect-MgGraph -Scopes "User.ReadWrite.All, Directory.ReadWrite.All"',
                    'Intune Device Configuration Profiles',
                    'Group-Based Licensing (Azure AD P1)'
                ],
                'accion_preventiva' => 'Adoptar arquitectura Zero-Trust con sincronización continua de Entra Cloud Sync y alertas en Teams ante umbrales de licenciamiento por debajo del 5% disponible.'
            ];
        }
    }

    // =========================================================================
    // 2. DOMINIO: VPN / PROXY / WAN / FIREWALL / ENLACES DEDICADOS
    // =========================================================================
    if (preg_match('/(vpn|proxy|ipsec|fortinet|cisco|openvpn|wireguard|túnel|tunel|enlace|wan|gateway|caida|corte|isp|fibra)/i', $texto) || $tipo === 'RED') {
        if ($variant === 1) {
            return [
                'enfoque_nombre' => $nombreEnfoque,
                'categoria' => 'Redes, VPN & Proxy (Mitigación Inmediata N1)',
                'diagnostico' => 'Pérdida de conectividad hacia el túnel VPN corporativo o bloqueo en el servidor Proxy intermediario con solicitud repetida de credenciales.',
                'causa_probable' => '1. Sesión de túnel IPsec/SSL colgada en el Firewall perimetral. 2. Caché DNS obsoleta apuntando a gateway no disponible. 3. Configuración de Proxy manual corrupta en el navegador (WinINet / PAC file).',
                'tiempo_estimado' => '10 - 20 minutos',
                'pasos_resolucion' => [
                    'Paso 1: Forzar vaciado de caché DNS y tabla ARP local: "ipconfig /flushdns" y "arp -d *".',
                    'Paso 2: Reiniciar el adaptador de red virtual de la VPN: "netsh interface set interface name=\"VPN\" admin=disable && enable".',
                    'Paso 3: Comprobar configuración de Proxy en Windows (inetcpl.cpl -> Conexiones -> Configuración de LAN) y desactivar "Usar servidor proxy para la LAN" temporalmente.',
                    'Paso 4: Probar handshake de red con el gateway: "Test-NetConnection -ComputerName <IP_GATEWAY_VPN> -Port 443".',
                    'Paso 5: Forzar reconexión de cliente FortiClient / Cisco AnyConnect con credenciales corporativas.'
                ],
                'comandos_herramientas' => [
                    'ipconfig /flushdns',
                    'Test-NetConnection -ComputerName <IP_GATEWAY> -Port 443',
                    'netsh int ip reset',
                    'inetcpl.cpl'
                ],
                'accion_preventiva' => 'Configurar DPD (Dead Peer Detection) en el Firewall perimetral a intervalos de 15 segundos para liberar automáticamente sesiones huérfanas de túneles caídos.'
            ];
        } elseif ($variant === 2) {
            return [
                'enfoque_nombre' => $nombreEnfoque,
                'categoria' => 'Infraestructura WAN & Firewall (Análisis Forense N3)',
                'diagnostico' => 'Falla de negociación criptográfica Fase 1/2 en túnel IPsec IKEv2, problemas de fragmentación por MTU/MSS o saturación de tabla NAT en el gateway del ISP.',
                'causa_probable' => '1. Desfase en el algoritmo de cifrado (Diffie-Hellman Group o Phase 2 Proposals). 2. Atenuación óptica o pérdida de paquetes (>15%) en el carrier WAN. 3. MTU mal calculada provocando paquetes "Don\'t Fragment" descartados.',
                'tiempo_estimado' => '30 - 50 minutos',
                'pasos_resolucion' => [
                    'Paso 1: Ejecutar captura de paquetes en la interfaz WAN con Wireshark o tcpdump buscando tráfico UDP 500 / 4500 (IKE) y protocolo ESP (50).',
                    'Paso 2: Verificar niveles de potencia óptica en transceptores SFP del switch core: "show interfaces transceiver details".',
                    'Paso 3: Diagnosticar el Maximum Transmission Unit (MTU) óptimo: "ping <IP_DESTINO> -f -l 1472" reduciendo el tamaño hasta eliminar el error de fragmentación.',
                    'Paso 4: Ajustar MSS Clamping en el router perimetral: "ip tcp adjust-mss 1360" e "ip mtu 1400".',
                    'Paso 5: Auditar logs de seguridad del Firewall (FortiGate / Palo Alto / pfSense) buscando rechazos de Phase 2 Selector Mismatch.'
                ],
                'comandos_herramientas' => [
                    'Wireshark (Filtro: udp.port==500 || udp.port==4500 || esp)',
                    'ping <IP> -f -l 1472',
                    'show vpn ipsec sa / diag vpn tunnel list',
                    'tracert -d <IP_DESTINO>'
                ],
                'accion_preventiva' => 'Implementar monitoreo ICMP proactivo con alertas SNMP en Zabbix/PRTG y conmutación automática BGP / SD-WAN ante latencias superiores a 80ms.'
            ];
        } else {
            return [
                'enfoque_nombre' => $nombreEnfoque,
                'categoria' => 'Arquitectura de Red & SD-WAN (Endurecimiento Definitivo)',
                'diagnostico' => 'Vulnerabilidad por punto único de falla (SPOF) en la conectividad hacia sucursales y servicios cloud.',
                'causa_probable' => '1. Dependencia de un único enlace ISP sin Failover automático. 2. Falta de segmentación por VLANs y ausencia de QoS para tráfico crítico. 3. Enrutamiento asimétrico en retorno de paquetes.',
                'tiempo_estimado' => '45 - 90 minutos',
                'pasos_resolucion' => [
                    'Paso 1: Configurar arquitectura SD-WAN con doble proveedor (Fibra Óptica Principal + Enlace de Respaldo 5G/Radioenlace).',
                    'Paso 2: Implementar políticas de SLA de rendimiento en SD-WAN (Jitter < 15ms, Pérdida de paquetes < 1%, Latencia < 50ms).',
                    'Paso 3: Segmentar la red en VLANs aisladas (VLAN 10: Datos, VLAN 20: Servidores, VLAN 30: VoIP, VLAN 40: WiFi Invitados).',
                    'Paso 4: Habilitar Strict Priority Queueing (QoS DSCP 46 / EF) para asegurar ancho de banda a sistemas ERP y videollamadas.'
                ],
                'comandos_herramientas' => [
                    'config system sdwan',
                    'mls qos trust dscp',
                    'spanning-tree mode rapid-pvst',
                    'show ip route'
                ],
                'accion_preventiva' => 'Migrar topología a doble Firewall en clúster activo-pasivo (HA VRRP/CARP) con sincronización de sesiones en tiempo real.'
            ];
        }
    }

    // =========================================================================
    // 3. DOMINIO: CIBERSEGURIDAD / PHISHING / MALWARE / RANSOMWARE / 2FA
    // =========================================================================
    if (preg_match('/(seguridad|phishing|virus|malware|ransomware|bloqueo|hack|ataque|credencial|contraseña|password|2fa|mfa|cuenta|sospechoso)/i', $texto) || $tipo === 'SEGURIDAD') {
        if ($variant === 1) {
            return [
                'enfoque_nombre' => $nombreEnfoque,
                'categoria' => 'Ciberseguridad & Respuesta a Incidentes (Contención Inmediata N1)',
                'diagnostico' => 'Alerta de compromiso de credenciales, intento de acceso no autorizado o vector de intrusión activo en puesto de trabajo.',
                'causa_probable' => '1. Ataque de fuerza bruta (Password Spraying) sobre servicio expuesto. 2. Interacción con correo de suplantación (Phishing) con robo de token. 3. Malware ejecutándose en espacio de usuario.',
                'tiempo_estimado' => '10 - 25 minutos',
                'pasos_resolucion' => [
                    'Paso 1: Aislar inmediatamente el equipo afectado desconectando el cable Ethernet y desactivando el adaptador WiFi.',
                    'Paso 2: Forzar reseteo de contraseñas de la cuenta de usuario afectada desde Active Directory / Microsoft Entra ID.',
                    'Paso 3: Revocar todas las sesiones y tokens de autenticación activos: "Revoke-AzureADUserAllRefreshToken".',
                    'Paso 4: Bloquear las direcciones IP de origen maliciosas en el Firewall perimetral con regla de DROP.',
                    'Paso 5: Ejecutar escaneo completo en memoria y disco con el motor antivirus/EDR corporativo (Microsoft Defender / SentinelOne / CrowdStrike).'
                ],
                'comandos_herramientas' => [
                    'Revoke-AzureADUserAllRefreshToken -ObjectId <ID>',
                    'netstat -ano | findstr ESTABLISHED',
                    'taskkill /f /pid <PID>',
                    'Get-MpThreatDetection'
                ],
                'accion_preventiva' => 'Exigir de manera obligatoria Autenticación Multifactor (MFA) basada en números coincidentes (Number Matching) y deshabilitar protocolos legados (IMAP/POP/SMTP básico).'
            ];
        } else {
            return [
                'enfoque_nombre' => $nombreEnfoque,
                'categoria' => 'Ciberseguridad Forense & Threat Hunting (N3)',
                'diagnostico' => 'Persistencia de actor de amenazas, movimiento lateral o exfiltración potencial de datos.',
                'causa_probable' => '1. Tarea programada o clave de registro de inicio (Run Keys) creada por ejecutable malicioso. 2. Robo de credenciales LSASS (Pass-the-Hash/Mimikatz). 3. Consentimiento malicioso de aplicación OAuth de terceros.',
                'tiempo_estimado' => '45 - 90 minutos',
                'pasos_resolucion' => [
                    'Paso 1: Inspeccionar claves de persistencia en el Registro de Windows: "reg query HKCU\Software\Microsoft\Windows\CurrentVersion\Run" y HKLM.',
                    'Paso 2: Auditar el Visor de Eventos de Seguridad buscando ID 4624 (Inicio de sesión), 4625 (Fallo de logon), 4688 (Procesos creados con línea de comandos) y 7045 (Servicios nuevos instalados).',
                    'Paso 3: Extraer hash SHA-256 de binarios sospechosos y contrastar en VirusTotal API: "Get-FileHash <ruta> -Algorithm SHA256".',
                    'Paso 4: Auditar permisos de aplicaciones empresariales en Entra ID para revocar accesos a aplicaciones OAuth de terceros sospechosas.',
                    'Paso 5: Habilitar Credential Guard en Windows para aislar el proceso LSASS mediante virtualización (VBS).'
                ],
                'comandos_herramientas' => [
                    'Get-WinEvent -FilterHashtable @{LogName="Security";Id=4624,4625,4688,7045}',
                    'Get-FileHash -Algorithm SHA256 <archivo>',
                    'Sysinternals Autoruns & Process Explorer',
                    'VirusTotal API'
                ],
                'accion_preventiva' => 'Implementar arquitectura Zero-Trust con políticas de menor privilegio (LUA), aislamiento de redes administrativas y despliegue de EDR con respuesta automática (SOAR).'
            ];
        }
    }

    // =========================================================================
    // 4. DOMINIO: BASES DE DATOS / SQL / DEADLOCKS / REPLICACIÓN
    // =========================================================================
    if (preg_match('/(sql|mysql|mariadb|postgres|oracle|base de datos|deadlock|bloqueo|query|consulta|timeout|transaccion)/i', $texto) || $tipo === 'BASE_DE_DATOS') {
        if ($variant === 1) {
            return [
                'enfoque_nombre' => $nombreEnfoque,
                'categoria' => 'Bases de Datos Transaccionales (Mitigación Rápida N1)',
                'diagnostico' => 'Saturación en el pool de conexiones o consultas lentas (Long-Running Queries) reteniendo bloqueos de tabla e impidiendo transacciones concurrentes.',
                'causa_probable' => '1. Full Table Scan en tabla de millones de registros por falta de índice. 2. Conexión de aplicación huérfana reteniendo transacción sin COMMIT ni ROLLBACK. 3. Límite de max_connections alcanzado.',
                'tiempo_estimado' => '15 - 30 minutos',
                'pasos_resolucion' => [
                    'Paso 1: Consultar procesos activos y tiempos de ejecución: "SHOW FULL PROCESSLIST;" o "SELECT * FROM sys.processlist WHERE command != \'Sleep\' ORDER BY time DESC;".',
                    'Paso 2: Identificar y matar el hilo causante del bloqueo con mayor tiempo de ejecución: "KILL <THREAD_ID>;".',
                    'Paso 3: Verificar estado de transacciones InnoDB y bloqueos: "SHOW ENGINE INNODB STATUS\G;".',
                    'Paso 4: Revisar consumo de CPU, memoria y espacio en disco de partición /var/lib/mysql.',
                    'Paso 5: Reiniciar el pool de conexiones de la aplicación si los hilos huérfanos persisten.'
                ],
                'comandos_herramientas' => [
                    'SHOW FULL PROCESSLIST',
                    'KILL <THREAD_ID>',
                    'SHOW ENGINE INNODB STATUS\G',
                    'SELECT * FROM sys.innodb_lock_waits'
                ],
                'accion_preventiva' => 'Configurar "max_execution_time = 30000" (30 segundos) en el motor para cancelar automáticamente consultas no optimizadas que colapsen el servidor.'
            ];
        } else {
            return [
                'enfoque_nombre' => $nombreEnfoque,
                'categoria' => 'Bases de Datos & Alta Disponibilidad (Optimización N3)',
                'diagnostico' => 'Degradación de I/O de disco por fragmentación de índices B-Tree o desincronización de réplica maestro-esclavo.',
                'causa_probable' => '1. Fragmentación de índices superior al 30% generando alto uso de disco. 2. Buffer pool subdimensionado (innodb_buffer_pool_size insuficiente). 3. Latencia de red provocando desfase en binary logs de replicación.',
                'tiempo_estimado' => '40 - 75 minutos',
                'pasos_resolucion' => [
                    'Paso 1: Analizar el plan de ejecución de la consulta con "EXPLAIN FORMAT=TREE SELECT..." identificando escaneos completos.',
                    'Paso 2: Crear índices compuestos cubrientes (Covering Indexes) en columnas de WHERE, JOIN y ORDER BY.',
                    'Paso 3: Optimizar y defragmentar tablas pesadas: "OPTIMIZE TABLE <nombre_tabla>;".',
                    'Paso 4: Ajustar el tamaño del buffer pool al 70% de la RAM total disponible del servidor: "SET GLOBAL innodb_buffer_pool_size = <bytes>;".',
                    'Paso 5: Verificar la réplica con "SHOW REPLICA STATUS\G;" comprobando que Seconds_Behind_Master sea 0.'
                ],
                'comandos_herramientas' => [
                    'EXPLAIN ANALYZE <QUERY>',
                    'pt-query-digest /var/log/mysql/slow.log',
                    'SHOW REPLICA STATUS\G',
                    'mysqldump --single-transaction --quick'
                ],
                'accion_preventiva' => 'Particionar tablas históricas por rango de fechas (Partitioning by Range) y automatizar mantenimiento nocturno de estadísticas de índices.'
            ];
        }
    }

    // =========================================================================
    // 5. DOMINIO: CLOUD, DEVOPS, SERVIDORES & DOCKER / KUBERNETES
    // =========================================================================
    if (preg_match('/(aws|azure|cloud|docker|kubernetes|k8s|pod|servidor|linux|ubuntu|nginx|apache|iis|vm|maquina virtual)/i', $texto) || $tipo === 'CLOUD_SERVIDORES') {
        return [
            'enfoque_nombre' => $nombreEnfoque,
            'categoria' => 'Cloud, Servidores & DevOps (Resiliencia)',
            'diagnostico' => 'Contenedor o servicio en estado CrashLoopBackOff / Caída de servicio web por agotamiento de recursos o fallas de dependencias.',
            'causa_probable' => '1. Límite de memoria superado generando terminación por OOMKilled (Exit Code 137). 2. Disco /var/log saturado al 100% impidiendo escritura de sockets. 3. Fallo en health check (Liveness / Readiness Probe) de Nginx/Apache.',
            'tiempo_estimado' => '20 - 45 minutos',
            'pasos_resolucion' => [
                'Paso 1: Inspeccionar logs de caída del contenedor o servicio: "kubectl logs <pod> --previous" o "journalctl -u nginx -e --no-pager".',
                'Paso 2: Verificar espacio en disco y uso de inodos: "df -h" y "df -i".',
                'Paso 3: Purgar contenedores e imágenes huérfanas: "docker system prune -af --volumes".',
                'Paso 4: Ajustar los límites de memoria (Limits / Requests de RAM) en el manifiesto YAML o redimensionar la instancia VM.',
                'Paso 5: Reiniciar el servicio web recargando configuraciones: "systemctl reload nginx" o "kubectl rollout restart deployment <nombre>".'
            ],
            'comandos_herramientas' => [
                'kubectl describe pod <nombre> -n prod',
                'journalctl -xeu nginx.service',
                'docker system prune -f',
                'htop / top -o %MEM'
            ],
            'accion_preventiva' => 'Configurar Horizontal Pod Autoscaler (HPA), rotación de logs con logrotate (máximo 7 días) y alertas en AWS CloudWatch / Azure Monitor ante CPU > 80%.'
        ];
    }

    // =========================================================================
    // 6. DOMINIO: HARDWARE & SOPORTE FÍSICO / PANTALLA AZUL / DISCO
    // =========================================================================
    if (preg_match('/(hardware|disco|ssd|hdd|memoria|ram|pantalla azul|bsod|impresora|fuente|calienta|apaga|monitor|teclado)/i', $texto) || $tipo === 'HARDWARE') {
        return [
            'enfoque_nombre' => $nombreEnfoque,
            'categoria' => 'Hardware & Soporte Físico (Diagnóstico Avanzado)',
            'diagnostico' => 'Falla de componentes físicos, degradación de celdas de almacenamiento SSD/HDD o corrupción de memoria RAM provocando inestabilidad o Kernel Panic / BSOD.',
            'causa_probable' => '1. Sectores reasignados o degradación SMART en la unidad de almacenamiento. 2. Fallo de módulo de memoria RAM en frecuencias altas. 3. Sobrecalentamiento térmico por pasta térmica seca o ventilador obstruido.',
            'tiempo_estimado' => '30 - 60 minutos',
            'pasos_resolucion' => [
                'Paso 1: Verificar el estado de salud SMART del disco con CrystalDiskInfo o comando PowerShell: "Get-PhysicalDisk | Get-StorageReliabilityCounter".',
                'Paso 2: Ejecutar comprobación y reparación de archivos del sistema de archivos: "chkdsk C: /f /r".',
                'Paso 3: Programar diagnóstico de memoria de Windows en el siguiente reinicio: "mdsched.exe" o utilizar MemTest86.',
                'Paso 4: Monitorear temperaturas de CPU/GPU con HWMonitor asegurando que no superen los 85°C bajo carga.',
                'Paso 5: Reemplazar el cable SATA/fuente de poder y realizar mantenimiento físico interno con aire comprimido.'
            ],
            'comandos_herramientas' => [
                'Get-PhysicalDisk | Select FriendlyName, HealthStatus, OperationalStatus',
                'chkdsk C: /f /r',
                'mdsched.exe',
                'CrystalDiskInfo / HWMonitor'
            ],
            'accion_preventiva' => 'Programar mantenimiento preventivo semestral, conectar equipos críticos a UPS con regulador de voltaje y programar copias de seguridad de imagen de disco con Veeam/Acronis.'
        ];
    }

    // =========================================================================
    // 7. DOMINIO: SOFTWARE GENERAL / ERP / SISTEMAS EMPRESARIALES
    // =========================================================================
    if ($variant === 1) {
        return [
            'enfoque_nombre' => $nombreEnfoque,
            'categoria' => 'Software & Aplicaciones Empresariales (Mitigación Rápida)',
            'diagnostico' => 'Bloqueo en la capa de interfaz de usuario, corrupción de caché de sesión local o conflicto de procesos en segundo plano.',
            'causa_probable' => '1. Fuga de memoria (Memory Leak) en el proceso de la aplicación. 2. Archivos temporales corruptos en AppData. 3. Servicio dependiente en estado detenido.',
            'tiempo_estimado' => '10 - 20 minutos',
            'pasos_resolucion' => [
                'Paso 1: Finalizar el árbol de procesos bloqueados: "taskkill /f /im <proceso>.exe /t".',
                'Paso 2: Purgar archivos temporales del usuario: "del /q /f /s %temp%\*" y limpiar "%localappdata%\Temp".',
                'Paso 3: Validar que los servicios dependientes en "services.msc" se encuentren en ejecución (Running) con inicio Automático.',
                'Paso 4: Ejecutar la aplicación con privilegios elevados (Ejecutar como Administrador).',
                'Paso 5: Reparar componentes de Microsoft Visual C++ Redistributable (2015-2022 x86/x64) y .NET Framework desde Panel de Control.'
            ],
            'comandos_herramientas' => [
                'taskkill /f /im app.exe /t',
                'del /q /f %temp%\*',
                'services.msc',
                'appwiz.cpl'
            ],
            'accion_preventiva' => 'Configurar tarea programada en PowerShell para limpieza semanal automatizada de temporales de usuario y registro de errores de aplicaciones.'
        ];
    } else {
        return [
            'enfoque_nombre' => $nombreEnfoque,
            'categoria' => 'Software & Arquitectura de Sistemas (Análisis Forense N3)',
            'diagnostico' => 'Excepción no controlada (Unhandled Exception), corrupción en librerías DLL dinámicas o incompatibilidad con actualización reciente de Windows.',
            'causa_probable' => '1. Conflicto de versiones de librerías .NET / C++ Runtime (DLL Hell). 2. Error de lectura de claves en el Registro de Windows (HKLM\Software). 3. Bloqueo de antivirus por falso positivo en módulo ejecutable.',
            'tiempo_estimado' => '30 - 50 minutos',
            'pasos_resolucion' => [
                'Paso 1: Abrir el Visor de Eventos de Windows (eventvwr.msc -> Registros de Windows -> Aplicación) e identificar el evento ID 1000 (Application Error) con el módulo con error (Faulting module name).',
                'Paso 2: Ejecutar reparación profunda de la imagen de Windows: "DISM /Online /Cleanup-Image /RestoreHealth" seguido de "sfc /scannow".',
                'Paso 3: Validar con Sysinternals Process Explorer las librerías DLL cargadas por el ejecutable buscando errores de acceso (Access Denied).',
                'Paso 4: Añadir exclusión controlada en Windows Defender para el directorio de instalación del software empresarial.',
                'Paso 5: Reinstalar la aplicación en modo limpio tras purgar claves obsoletas del registro con permisos de Administrador.'
            ],
            'comandos_herramientas' => [
                'eventvwr.msc (Filtro Event ID 1000/1002)',
                'sfc /scannow',
                'DISM /Online /Cleanup-Image /RestoreHealth',
                'Sysinternals Process Monitor (ProcMon)'
            ],
            'accion_preventiva' => 'Desplegar ambiente de pruebas Staging (UAT) antes de aplicar actualizaciones de parches de Windows en la flota corporativa.'
        ];
    }
}
