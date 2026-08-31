<?php
/**
 * Servicio de Inteligencia Artificial Local (Motor Experto Heurístico - 0 Tokens)
 * Capa de Servicios Locales
 */

class LocalExpertService {

    /**
     * Diagnóstico técnico local basado en análisis semántico NLP
     */
    public static function diagnoseTicket($tipo, $prioridad, $asunto, $mensaje, $variant = 1) {
        $texto = mb_strtolower($asunto . ' ' . $mensaje, 'UTF-8');

        $enfoques = [
            1 => 'Solución Rápida en Caliente & Mitigación Inmediata (N1/N2)',
            2 => 'Análisis Forense Profundo & Diagnóstico de Causa Raíz (N3)',
            3 => 'Reconfiguración Arquitectural, Endurecimiento & Prevención Definitiva'
        ];
        $nombreEnfoque = $enfoques[$variant] ?? $enfoques[1];

        // 1. Dominio: Microsoft 365 / Office / Licencias
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
                    'accion_preventiva' => 'Configurar alertas automáticas de facturación con 30 días de anticipación en el portal de facturación de M365 y habilitar grupos dinámicos de licencias basadas en departamento.',
                    'motor_ia' => 'Motor Experto TI (Local - 0 Tokens)'
                ];
            } else {
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
                    'accion_preventiva' => 'Implementar políticas de Acceso Condicional con período de gracia de 14 días para renovación de tokens y desplegar Microsoft Defender for Endpoint para validar el cumplimiento del dispositivo.',
                    'motor_ia' => 'Motor Experto TI (Local - 0 Tokens)'
                ];
            }
        }

        // 2. Dominio: Redes / VPN / Proxy
        if (preg_match('/(vpn|proxy|ipsec|fortinet|cisco|openvpn|wireguard|túnel|tunel|enlace|wan|gateway|caida|corte|isp|fibra)/i', $texto) || $tipo === 'RED') {
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
                'accion_preventiva' => 'Configurar DPD (Dead Peer Detection) en el Firewall perimetral a intervalos de 15 segundos para liberar automáticamente sesiones huérfanas de túneles caídos.',
                'motor_ia' => 'Motor Experto TI (Local - 0 Tokens)'
            ];
        }

        // 3. Dominio: Seguridad / Phishing
        if (preg_match('/(seguridad|phishing|virus|malware|ransomware|bloqueo|hack|ataque|credencial|contraseña|password|2fa|mfa|cuenta)/i', $texto) || $tipo === 'SEGURIDAD') {
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
                    'Paso 5: Ejecutar escaneo completo en memoria y disco con el motor antivirus/EDR corporativo.'
                ],
                'comandos_herramientas' => [
                    'Revoke-AzureADUserAllRefreshToken -ObjectId <ID>',
                    'netstat -ano | findstr ESTABLISHED',
                    'taskkill /f /pid <PID>',
                    'Get-MpThreatDetection'
                ],
                'accion_preventiva' => 'Exigir de manera obligatoria Autenticación Multifactor (MFA) basada en números coincidentes (Number Matching) y deshabilitar protocolos legados.',
                'motor_ia' => 'Motor Experto TI (Local - 0 Tokens)'
            ];
        }

        // 4. Dominio: Bases de Datos
        if (preg_match('/(sql|mysql|mariadb|postgres|oracle|base de datos|deadlock|bloqueo|query|consulta|timeout)/i', $texto) || $tipo === 'BASE_DE_DATOS') {
            return [
                'enfoque_nombre' => $nombreEnfoque,
                'categoria' => 'Bases de Datos Transaccionales (Mitigación Rápida N1)',
                'diagnostico' => 'Saturación en el pool de conexiones o consultas lentas (Long-Running Queries) reteniendo bloqueos de tabla e impidiendo transacciones concurrentes.',
                'causa_probable' => '1. Full Table Scan en tabla de millones de registros por falta de índice. 2. Conexión de aplicación huérfana reteniendo transacción sin COMMIT ni ROLLBACK. 3. Límite de max_connections alcanzado.',
                'tiempo_estimado' => '15 - 30 minutos',
                'pasos_resolucion' => [
                    'Paso 1: Consultar procesos activos y tiempos de ejecución: "SHOW FULL PROCESSLIST;".',
                    'Paso 2: Identificar y matar el hilo causante del bloqueo con mayor tiempo de ejecución: "KILL <THREAD_ID>;".',
                    'Paso 3: Verificar estado de transacciones InnoDB y bloqueos: "SHOW ENGINE INNODB STATUS\G;".',
                    'Paso 4: Revisar consumo de CPU, memoria y espacio en disco de partición de base de datos.',
                    'Paso 5: Reiniciar el pool de conexiones de la aplicación si los hilos huérfanos persisten.'
                ],
                'comandos_herramientas' => [
                    'SHOW FULL PROCESSLIST',
                    'KILL <THREAD_ID>',
                    'SHOW ENGINE INNODB STATUS\G',
                    'SELECT * FROM sys.innodb_lock_waits'
                ],
                'accion_preventiva' => 'Configurar "max_execution_time = 30000" (30 segundos) en el motor para cancelar automáticamente consultas no optimizadas.',
                'motor_ia' => 'Motor Experto TI (Local - 0 Tokens)'
            ];
        }

        // 5. Dominio: Software General / ERP (Default)
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
                'Paso 5: Reparar componentes de Microsoft Visual C++ Redistributable y .NET Framework desde Panel de Control.'
            ],
            'comandos_herramientas' => [
                'taskkill /f /im app.exe /t',
                'del /q /f %temp%\*',
                'services.msc',
                'appwiz.cpl'
            ],
            'accion_preventiva' => 'Configurar tarea programada en PowerShell para limpieza semanal automatizada de temporales de usuario y registro de errores.',
            'motor_ia' => 'Motor Experto TI (Local - 0 Tokens)'
        ];
    }

    /**
     * Análisis Estratégico Local de Negocio
     */
    public static function generateStrategicInsights($tipos, $prios, $estados, $total) {
        $tipoLider = !empty($tipos) ? array_key_first($tipos) : 'SOFTWARE';
        $pendientesTotal = $estados['pendiente'] ?? 0;
        $enProcesoTotal = $estados['en_proceso'] ?? 0;
        $resueltosTotal = $estados['resuelto'] ?? 0;
        $criticosTotal = ($prios['critica'] ?? 0) + ($prios['alta'] ?? 0);
        $tasaResolucion = $total > 0 ? round(($resueltosTotal / $total) * 100, 1) : 0;

        $contrataciones = [
            [
                'perfil' => 'Ingeniero de Redes, WAN & Conectividad Cloud (NOC/SD-WAN)',
                'especialidad' => 'RED',
                'seniority' => 'Senior (5+ años exp.)',
                'prioridad_contratacion' => 'Urgente / Crítica',
                'certificaciones_clave' => ['Cisco CCNP Enterprise', 'Fortinet NSE 4/7', 'MikroTik MTCRE'],
                'justificacion' => 'El área de Redes concentra incidentes de caídas de túneles VPN y degradación de enlaces que impactan la operación.',
                'impacto_esperado' => 'Reducción del 65% en el MTTR de enlaces caídos y disponibilidad de red superior al 99.95%.'
            ],
            [
                'perfil' => 'Especialista en Ciberseguridad, Identidad & SOC Analyst (L2/L3)',
                'especialidad' => 'SEGURIDAD',
                'seniority' => 'Semi-Senior / Senior',
                'prioridad_contratacion' => 'Alta Prioridad',
                'certificaciones_clave' => ['CompTIA Security+', 'Certified Ethical Hacker (CEH)', 'Microsoft Certified: SC-200'],
                'justificacion' => 'Incidentes de seguridad que involucran suplantación de identidad (phishing) y bloqueos de cuentas.',
                'impacto_esperado' => 'Monitoreo preventivo 24/7 y contención de vectores de ataque en menos de 15 minutos.'
            ],
            [
                'perfil' => 'Ingeniero de Soporte de Aplicaciones, Licenciamiento SaaS & ERP',
                'especialidad' => 'SOFTWARE',
                'seniority' => 'Semi-Senior',
                'prioridad_contratacion' => 'Alta',
                'certificaciones_clave' => ['Microsoft Certified: MD-102', 'ITIL 4 Specialist', 'Azure Fundamentals'],
                'justificacion' => 'El software corporativo y licenciamiento SaaS (M365, ERP) concentran solicitudes de productividad.',
                'impacto_esperado' => 'Despacho ágil en primer contacto y automatización de aprovisionamiento de software.'
            ]
        ];

        $infraestructura = [
            [
                'inversion' => 'Plataforma de Monitoreo Proactivo de Red & Servidores (Zabbix Enterprise / Datadog / PRTG)',
                'categoria' => 'Monitoreo & NOC',
                'costo_estimado' => 'USD $1,200 - $2,500 / año',
                'justificacion' => 'Detecta saturación de CPU, caídas de enlaces WAN o expiración de certificados antes de reportes de usuarios.',
                'retorno_inversion' => 'Aumento del SLA de disponibilidad al 99.9% y reducción del 40% en incidentes no planificados.'
            ],
            [
                'inversion' => 'Renovación de Equipamiento Core: Switches Gestionables PoE+ y Gateway SD-WAN Redundante',
                'categoria' => 'Hardware de Red & Telecomunicaciones',
                'costo_estimado' => 'USD $3,500 - $7,000',
                'justificacion' => 'Elimina el punto único de falla (SPOF) en la conexión a Internet mediante doble proveedor de fibra óptica.',
                'retorno_inversion' => 'Cero tiempo de inactividad por cortes de ISP y soporte de QoS para telefonía y ERP.'
            ],
            [
                'inversion' => 'Suite de Protección de Endpoints EDR & Protección de Identidad Microsoft Defender',
                'categoria' => 'Seguridad Informática & Ciberdefensa',
                'costo_estimado' => 'USD $4.50 por usuario / mes',
                'justificacion' => 'Mitiga ataques de phishing, robo de credenciales y ejecución de archivos sospechosos.',
                'retorno_inversion' => 'Aislamiento automático de hosts infectados en < 2 minutos y cumplimiento normativo (ISO 27001).'
            ]
        ];

        $capacitacion = [
            [
                'tema' => 'Diagnóstico Forense de Redes, Túneles IPsec & Enrutamiento para Soporte L1/L2',
                'audiencia' => 'Técnicos de Mesa de Ayuda (L1) y Soporte en Sitio',
                'duracion' => '20 Horas Prácticas',
                'objetivo' => 'Estandarizar el uso de herramientas como Wireshark, netsh, tracert y diagnóstico de MTU/DNS para resolver el 80% de fallas.'
            ],
            [
                'tema' => 'Administración de Identidades en Microsoft Entra ID (Azure AD), MFA & Acceso Condicional',
                'audiencia' => 'Administradores de Sistemas y Técnicos de Soporte N2',
                'duracion' => '16 Horas',
                'objetivo' => 'Dominar la gestión de tokens PRT, resolución de licencias de Office 365 y políticas de MFA.'
            ],
            [
                'tema' => 'Optimización de Consultas SQL, Monitoreo de Transacciones y Resolución de Deadlocks',
                'audiencia' => 'Ingenieros de Software, DBAs y Especialistas de ERP',
                'duracion' => '12 Horas',
                'objetivo' => 'Capacitar en el análisis de planes de ejecución (EXPLAIN ANALYZE) y mitigación de bloqueos concurrentes.'
            ]
        ];

        $automatizacion = [
            [
                'iniciativa' => 'Script de Auto-Reparación y Limpieza de Puestos de Trabajo (TechCare QuickFix PowerShell)',
                'alcance' => 'Incidentes de Software, temporales, caché de M365 y adaptadores de red',
                'ahorro_tiempo' => '~35 horas/mes del equipo técnico',
                'descripcion' => 'Herramienta ejecutable en 1 clic que realiza flushdns, reinicio de Click-to-Run de Office y purga de temporales.'
            ],
            [
                'iniciativa' => 'Portal de Autoservicio con Asistente Virtual Inteligente para Triaje y Preguntas Frecuentes',
                'alcance' => 'Solicitudes rutinarias L1 (desbloqueo de cuentas, configuración de correo, guías de VPN)',
                'ahorro_tiempo' => '~50 horas/mes de mesa de ayuda',
                'descripcion' => 'Base de conocimientos interactiva con resolución guiada que reduce el volumen de tickets entrantes en un 30%.'
            ]
        ];

        $resumenEjecutivo = "El análisis integral sobre las {$total} solicitudes registradas revela que la mayor carga operativa recae en el área de {$tipoLider} (" . ($tipos[$tipoLider]['porcentaje'] ?? 0) . "% del volumen total). " .
            "Actualmente se mantienen {$pendientesTotal} tickets pendientes y {$criticosTotal} casos de alta criticidad que requieren atención prioritaria. " .
            "La tasa de resolución global se sitúa en {$tasaResolucion}%. " .
            "Se recomienda priorizar la contratación de especialistas en {$tipoLider} y Ciberseguridad, acompañada del despliegue del script de automatización QuickFix y equipamiento de monitoreo para descongestionar el equipo técnico en un 40%.";

        return [
            'resumen_ejecutivo' => $resumenEjecutivo,
            'metricas_clave' => [
                'total_tickets' => $total,
                'pendientes' => $pendientesTotal,
                'en_proceso' => $enProcesoTotal,
                'resueltos' => $resueltosTotal,
                'tasa_resolucion' => $tasaResolucion . '%',
                'categoria_mayor_demanda' => $tipoLider,
                'tickets_criticos_altos' => $criticosTotal
            ],
            'decision_contratacion' => $contrataciones,
            'decision_infraestructura' => $infraestructura,
            'decision_capacitacion' => $capacitacion,
            'decision_automatizacion' => $automatizacion,
            'distribucion_categorias' => array_values($tipos),
            'motor_ia' => 'Motor de Inteligencia de Negocio IT (Local - 0 Tokens)'
        ];
    }
}
