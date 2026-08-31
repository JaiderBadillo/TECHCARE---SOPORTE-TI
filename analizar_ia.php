<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/gemini_client.php';

$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['ok' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

// 1. Obtener métricas y agregaciones reales de la base de datos
$totalGeneral = (int)$conn->query("SELECT COUNT(*) AS c FROM solicitudes")->fetch_assoc()['c'];

if ($totalGeneral === 0) {
    echo json_encode([
        'ok' => true,
        'insights' => [
            'resumen_ejecutivo' => 'La base de datos está limpia e iniciando desde cero (0 solicitudes registradas). Registre nuevos tickets desde el formulario para que la IA genere recomendaciones personalizadas según las incidencias.',
            'metricas_clave' => [
                'total_tickets' => 0,
                'pendientes' => 0,
                'en_proceso' => 0,
                'resueltos' => 0,
                'tasa_resolucion' => '0%',
                'categoria_mayor_demanda' => 'Sin datos',
                'tickets_criticos_altos' => 0
            ],
            'distribucion_categorias' => [],
            'decision_contratacion' => [
                [
                    'perfil' => 'En espera de solicitudes de clientes',
                    'especialidad' => 'GENERAL',
                    'seniority' => 'Técnico Nivel 1',
                    'prioridad_contratacion' => 'Informativa',
                    'certificaciones_clave' => ['ITIL 4 Foundation', 'CompTIA A+'],
                    'justificacion' => 'El sistema se encuentra listo desde cero. Tan pronto ingresen incidentes clasificados (RED, SOFTWARE, HARDWARE, etc.), la IA analizará la carga y sugerirá perfiles exactos a contratar.',
                    'impacto_esperado' => 'Monitoreo predictivo de contrataciones activado.'
                ]
            ],
            'decision_infraestructura' => [
                [
                    'inversion' => 'Plataforma de Monitoreo Proactivo (Zabbix / PRTG)',
                    'categoria' => 'Monitoreo NOC',
                    'costo_estimado' => 'Bajo-Medio',
                    'justificacion' => 'Recomendación base para detectar saturación de enlaces y servidores antes del reporte de clientes.',
                    'retorno_inversion' => 'Alta disponibilidad de infraestructura.'
                ]
            ],
            'decision_capacitacion' => [
                [
                    'tema' => 'Protocolos de Mesa de Ayuda y Triaje Técnico Inicial',
                    'audiencia' => 'Equipo de Soporte',
                    'duracion' => '8 Horas',
                    'objetivo' => 'Estandarizar la atención y categorización de tickets desde el primer contacto.'
                ]
            ],
            'decision_automatizacion' => [
                [
                    'iniciativa' => 'Scripting Base de Auto-Diagnóstico de Red y Sistema',
                    'alcance' => 'Diagnóstico rápido',
                    'ahorro_tiempo' => '~15 horas/mes proyectado',
                    'descripcion' => 'Comandos automatizados para agilizar la toma de logs y estado de red del cliente.'
                ]
            ]
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Agrupación por tipo de problema
$resTipos = $conn->query("SELECT tipo_problema, COUNT(*) as cantidad, 
                          SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                          SUM(CASE WHEN prioridad = 'critica' THEN 1 ELSE 0 END) as criticos,
                          SUM(CASE WHEN prioridad = 'alta' THEN 1 ELSE 0 END) as altos
                          FROM solicitudes 
                          GROUP BY tipo_problema 
                          ORDER BY cantidad DESC");

$datosTipos = [];
while ($row = $resTipos->fetch_assoc()) {
    $tipo = $row['tipo_problema'];
    $cant = (int)$row['cantidad'];
    $pend = (int)$row['pendientes'];
    $crit = (int)$row['criticos'];
    $alt = (int)$row['altos'];
    $pct = round(($cant / $totalGeneral) * 100, 1);
    $datosTipos[$tipo] = [
        'tipo' => $tipo,
        'cantidad' => $cant,
        'porcentaje' => $pct,
        'pendientes' => $pend,
        'criticos' => $crit,
        'altos' => $alt
    ];
}

// Agrupación por prioridad
$resPrio = $conn->query("SELECT prioridad, COUNT(*) as cantidad FROM solicitudes GROUP BY prioridad");
$datosPrio = [];
while ($row = $resPrio->fetch_assoc()) {
    $datosPrio[$row['prioridad']] = (int)$row['cantidad'];
}

// Agrupación por estado
$resEst = $conn->query("SELECT estado, COUNT(*) as cantidad FROM solicitudes GROUP BY estado");
$datosEst = ['pendiente' => 0, 'en_proceso' => 0, 'resuelto' => 0];
while ($row = $resEst->fetch_assoc()) {
    $datosEst[$row['estado']] = (int)$row['cantidad'];
}

// Muestra de últimos 15 tickets para contexto cualitativo
$resTickets = $conn->query("SELECT id, asunto, mensaje, tipo_problema, prioridad, estado FROM solicitudes ORDER BY fecha_creacion DESC LIMIT 15");
$ticketsRecientes = [];
while ($row = $resTickets->fetch_assoc()) {
    $ticketsRecientes[] = $row;
}

$conn->close();

// 2. Generar Recomendaciones de Decisión Estratégica
$motor = isset($_GET['motor']) ? strtolower(trim($_GET['motor'])) : (isset($_POST['motor']) ? strtolower(trim($_POST['motor'])) : 'gemini');

if ($motor === 'local') {
    // Modo Ahorro: 100% Local sin consumir tokens
    $insights = generarDecisionesEstrategicas($datosTipos, $datosPrio, $datosEst, $totalGeneral, $ticketsRecientes);
    $insights['motor_ia'] = 'Motor de Inteligencia de Negocio IT (Local - 0 Tokens)';
} else {
    // Modo Cloud: Google Gemini API con fallback local
    $insights = analizarEstrategiaConGemini($datosTipos, $datosPrio, $datosEst, $totalGeneral, $ticketsRecientes);
    if (!$insights) {
        $insights = generarDecisionesEstrategicas($datosTipos, $datosPrio, $datosEst, $totalGeneral, $ticketsRecientes);
        $insights['motor_ia'] = 'Motor de Inteligencia de Negocio IT (Local)';
    }
}

echo json_encode([
    'ok' => true,
    'insights' => $insights
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

/**
 * Análisis Estratégico Ejecutivo con Google Gemini
 */
function analizarEstrategiaConGemini($tipos, $prios, $estados, $total, $tickets) {
    $systemInstruction = "Eres un Director de Tecnología (CTO) y Consultor ITIL Senior. Analiza métricas de mesa de ayuda y genera decisiones ejecutivas de negocio para la empresa en formato JSON estricto.";

    $resumenMetricas = json_encode([
        'total_tickets' => $total,
        'distribucion_por_tipo' => $tipos,
        'prioridades' => $prios,
        'estados' => $estados,
        'muestra_tickets' => $tickets
    ], JSON_UNESCAPED_UNICODE);

    $prompt = <<<EOT
A continuación tienes las métricas reales del sistema de soporte técnico:
{$resumenMetricas}

Genera un plan de acción ejecutivo en formato JSON con la siguiente estructura exacta:
{
  "resumen_ejecutivo": "Texto conciso resumiendo el estado operativo, principales riesgos y prioridades inmediatas",
  "decision_contratacion": [
    {
      "perfil": "Título del puesto técnico sugerido a contratar",
      "especialidad": "RED / SEGURIDAD / SOFTWARE / etc.",
      "seniority": "Junior / Semi-Senior / Senior",
      "prioridad_contratacion": "Urgente / Alta / Media",
      "certificaciones_clave": ["Certificación 1", "Certificación 2"],
      "justificacion": "Por qué es necesario según el volumen de tickets analizado",
      "impacto_esperado": "Beneficio proyectado para la operación"
    }
  ],
  "decision_infraestructura": [
    {
      "inversion": "Herramienta o equipamiento tecnológico",
      "categoria": "Monitoreo / Hardware / Seguridad / etc.",
      "costo_estimado": "Bajo / Medio / Alto",
      "justificacion": "Justificación basada en los incidentes registrados",
      "retorno_inversion": "Impacto en SLA y disponibilidad"
    }
  ],
  "decision_capacitacion": [
    {
      "tema": "Tema del taller o curso técnico",
      "audiencia": "Público objetivo",
      "duracion": "X Horas",
      "objetivo": "Objetivo pedagógico para reducir escalamientos"
    }
  ],
  "decision_automatizacion": [
    {
      "iniciativa": "Nombre del script o portal",
      "alcance": "Alcance de la solución",
      "ahorro_tiempo": "Ahorro estimado en horas/mes",
      "descripcion": "Descripción funcional de la automatización"
    }
  ],
  "motor_ia": "Google Gemini 1.5 Flash (Cloud)"
}
EOT;

    $res = callGeminiAPI($prompt, $systemInstruction);
    if ($res && isset($res['resumen_ejecutivo'], $res['decision_contratacion'])) {
        $res['metricas_clave'] = [
            'total_tickets' => $total,
            'pendientes' => $estados['pendiente'],
            'en_proceso' => $estados['en_proceso'],
            'resueltos' => $estados['resuelto'],
            'tasa_resolucion' => $total > 0 ? round(($estados['resuelto'] / $total) * 100, 1) . '%' : '0%',
            'categoria_mayor_demanda' => !empty($tipos) ? array_key_first($tipos) : 'GENERAL',
            'tickets_criticos_altos' => ($prios['critica'] ?? 0) + ($prios['alta'] ?? 0)
        ];
        $res['distribucion_categorias'] = array_values($tipos);
        if (!isset($res['motor_ia'])) {
            $res['motor_ia'] = 'Google Gemini 1.5 Flash (Cloud)';
        }
        return $res;
    }
    return null;
}

/**
 * Motor de Decisiones Estratégicas para Empresa de Soporte TI (Modo Local Experto Avanzado)
 */
function generarDecisionesEstrategicas($tipos, $prios, $estados, $total, $tickets) {
    $tipoLider = !empty($tipos) ? array_key_first($tipos) : 'SOFTWARE';
    $maxTickets = -1;
    $tipoMayorPendiente = null;
    $maxPendientes = -1;
    $tipoMayorCritico = null;
    $maxCriticos = -1;

    foreach ($tipos as $tipo => $d) {
        if ($d['cantidad'] > $maxTickets) {
            $maxTickets = $d['cantidad'];
            $tipoLider = $tipo;
        }
        if ($d['pendientes'] > $maxPendientes) {
            $maxPendientes = $d['pendientes'];
            $tipoMayorPendiente = $tipo;
        }
        if (($d['criticos'] + $d['altos']) > $maxCriticos) {
            $maxCriticos = $d['criticos'] + $d['altos'];
            $tipoMayorCritico = $tipo;
        }
    }

    $pendientesTotal = $estados['pendiente'];
    $enProcesoTotal = $estados['en_proceso'];
    $resueltosTotal = $estados['resuelto'];
    $criticosTotal = ($prios['critica'] ?? 0) + ($prios['alta'] ?? 0);
    $tasaResolucion = $total > 0 ? round(($resueltosTotal / $total) * 100, 1) : 0;

    // 1. Decisión de Contratación según Especialidad & Criticidad
    $contrataciones = [];
    
    if (isset($tipos['RED']) && ($tipos['RED']['porcentaje'] >= 15 || $tipos['RED']['pendientes'] >= 1)) {
        $contrataciones[] = [
            'perfil' => 'Ingeniero Senior de Redes, WAN & Conectividad Cloud (NOC/SD-WAN)',
            'especialidad' => 'RED',
            'seniority' => 'Senior (5+ años exp.)',
            'prioridad_contratacion' => 'Urgente / Crítica',
            'certificaciones_clave' => ['Cisco CCNP Enterprise / CCNA', 'Fortinet NSE 4/7', 'MikroTik MTCRE'],
            'justificacion' => 'El área de Redes representa el ' . ($tipos['RED']['porcentaje'] ?? 0) . '% de los incidentes totales (' . ($tipos['RED']['cantidad'] ?? 0) . ' tickets). Se observan caídas de túneles VPN y degradación de enlaces que paralizan la operación.',
            'impacto_esperado' => 'Reducción del 65% en el MTTR de enlaces caídos, diseño de topologías redundantes BGP/SD-WAN y disponibilidad de red superior al 99.95%.'
        ];
    }
    
    if (isset($tipos['SEGURIDAD']) && ($tipos['SEGURIDAD']['cantidad'] >= 1)) {
        $contrataciones[] = [
            'perfil' => 'Especialista en Ciberseguridad, Identidad & SOC Analyst (Nivel 2/3)',
            'especialidad' => 'SEGURIDAD',
            'seniority' => 'Semi-Senior / Senior',
            'prioridad_contratacion' => 'Alta Prioridad',
            'certificaciones_clave' => ['CompTIA Security+ / CySA+', 'Certified Ethical Hacker (CEH)', 'Microsoft Certified: Security Operations Analyst (SC-200)'],
            'justificacion' => 'Se registran ' . ($tipos['SEGURIDAD']['cantidad'] ?? 0) . ' incidentes de seguridad que involucran suplantación de identidad (phishing), bloqueos de cuentas y riesgos de intrusión.',
            'impacto_esperado' => 'Monitoreo preventivo de amenazas 24/7, blindaje de identidad híbrida en Entra ID y contención de vectores de ransomware en menos de 15 minutos.'
        ];
    }

    if (isset($tipos['SOFTWARE']) && ($tipos['SOFTWARE']['cantidad'] >= 1)) {
        $contrataciones[] = [
            'perfil' => 'Ingeniero de Soporte de Aplicaciones, Licenciamiento SaaS & ERP (L2/L3)',
            'especialidad' => 'SOFTWARE',
            'seniority' => 'Semi-Senior (3+ años exp.)',
            'prioridad_contratacion' => 'Alta',
            'certificaciones_clave' => ['Microsoft Certified: Endpoint Administrator (MD-102)', 'ITIL 4 Specialist', 'AWS / Azure Fundamentals'],
            'justificacion' => 'El software corporativo y licenciamiento SaaS (M365, ERP) concentran ' . ($tipos['SOFTWARE']['cantidad'] ?? 0) . ' solicitudes que requieren gestión de sesiones, credenciales y resolución de bugs.',
            'impacto_esperado' => 'Despacho ágil de tickets de productividad en primer contacto y automatización de aprovisionamiento de software vía Intune/GPO.'
        ];
    }

    if (isset($tipos['CLOUD_SERVIDORES']) || isset($tipos['BASE_DE_DATOS'])) {
        $contrataciones[] = [
            'perfil' => 'Ingeniero Cloud DevOps & Administrador de Bases de Datos (DBA / SRE)',
            'especialidad' => 'CLOUD / BASE DE DATOS',
            'seniority' => 'Senior',
            'prioridad_contratacion' => 'Estratégica',
            'certificaciones_clave' => ['AWS Solutions Architect Associate', 'Kubernetes Administrator (CKA)', 'Oracle / MySQL Certified DBA'],
            'justificacion' => 'La persistencia de datos y servicios en contenedores/VMs requieren optimización de consultas SQL (deadlocks) y alta disponibilidad.',
            'impacto_esperado' => 'Eliminación de cuellos de botella en bases de datos e infraestructura Cloud con auto-escalado horizontal (HPA).'
        ];
    }

    if (empty($contrataciones)) {
        $contrataciones[] = [
            'perfil' => 'Ingeniero de Mesa de Ayuda Nivel 2 Especialista en ' . $tipoLider,
            'especialidad' => $tipoLider,
            'seniority' => 'Semi-Senior',
            'prioridad_contratacion' => 'Media - Alta',
            'certificaciones_clave' => ['ITIL 4 Foundation', 'CompTIA A+ / Network+'],
            'justificacion' => 'Refuerzo de capacidad operativa en el área de ' . $tipoLider . ' para acelerar la tasa de resolución global.',
            'impacto_esperado' => 'Disminución de tickets en estado pendiente y descongestión de la cola de trabajo.'
        ];
    }

    // 2. Decisión de Inversión en Infraestructura y Herramientas
    $infraestructura = [
        [
            'inversion' => 'Plataforma de Monitoreo Proactivo de Red & Servidores (Zabbix Enterprise / Datadog / PRTG)',
            'categoria' => 'Monitoreo & NOC',
            'costo_estimado' => 'USD $1,200 - $2,500 / año',
            'justificacion' => 'Permite detectar saturación de CPU, caídas de enlaces WAN o expiración de certificados SSL antes de que los usuarios finales reporten la incidencia.',
            'retorno_inversion' => 'Aumento del SLA de disponibilidad al 99.9% y reducción del 40% en incidentes no planificados.'
        ],
        [
            'inversion' => 'Renovación de Equipamiento Core: Switches Gestionables PoE+ y Gateway SD-WAN Redundante',
            'categoria' => 'Hardware de Red & Telecomunicaciones',
            'costo_estimado' => 'USD $3,500 - $7,000 (Inversión Única)',
            'justificacion' => 'Elimina el punto único de falla (SPOF) en la conexión a Internet mediante doble proveedor de fibra óptica con balanceo y conmutación automática.',
            'retorno_inversion' => 'Cero tiempo de inactividad por cortes de ISP y soporte de Quality of Service (QoS) para telefonía y ERP.'
        ],
        [
            'inversion' => 'Suite de Protección de Endpoints EDR & Protección de Identidad Microsoft Defender / CrowdStrike',
            'categoria' => 'Seguridad Informática & Ciberdefensa',
            'costo_estimado' => 'USD $4.50 por usuario / mes',
            'justificacion' => 'Mitiga ataques de phishing, robo de credenciales y ejecución de ejecutables sospechosos en equipos de escritorio y laptops.',
            'retorno_inversion' => 'Aislamiento automático de hosts infectados en < 2 minutos y cumplimiento de normativas de protección de datos (ISO 27001).'
        ]
    ];

    // 3. Decisión de Plan de Capacitación Técnica Interna (Upskilling)
    $capacitacion = [
        [
            'tema' => 'Diagnóstico Forense de Redes, Túneles IPsec & Enrutamiento para Soporte L1/L2',
            'audiencia' => 'Técnicos de Mesa de Ayuda (L1) y Soporte en Sitio',
            'duracion' => '20 Horas Prácticas',
            'objetivo' => 'Estandarizar el uso de herramientas como Wireshark, netsh, tracert y diagnóstico de MTU/DNS para resolver el 80% de fallas sin escalar a especialistas Senior.'
        ],
        [
            'tema' => 'Administración de Identidades en Microsoft Entra ID (Azure AD), MFA & Acceso Condicional',
            'audiencia' => 'Administradores de Sistemas y Técnicos de Soporte N2',
            'duracion' => '16 Horas',
            'objetivo' => 'Dominar la gestión de tokens PRT, resolución de licencias de Office 365, políticas de MFA y reseteo seguro de credenciales.'
        ],
        [
            'tema' => 'Optimización de Consultas SQL, Monitoreo de Transacciones y Resolución de Deadlocks',
            'audiencia' => 'Ingenieros de Software, DBAs y Especialistas de ERP',
            'duracion' => '12 Horas',
            'objetivo' => 'Capacitar en el análisis de planes de ejecución (EXPLAIN ANALYZE), creación de índices compuestos y mitigación de bloqueos concurrentes.'
        ]
    ];

    // 4. Decisión de Automatización y Autoservicio (Deflexión Operativa)
    $automatizacion = [
        [
            'iniciativa' => 'Script de Auto-Reparación y Limpieza de Puestos de Trabajo (TechCare QuickFix PowerShell)',
            'alcance' => 'Incidentes de Software, temporales, caché de M365 y adaptadores de red',
            'ahorro_tiempo' => '~35 horas/mes del equipo técnico',
            'descripcion' => 'Herramienta ejecutable en 1 clic que realiza flushdns, reinicio de Click-to-Run de Office, purga de %temp% y verificación de certificados digitales de forma desatendida.'
        ],
        [
            'iniciativa' => 'Portal de Autoservicio con Asistente Virtual Inteligente para Triaje y Preguntas Frecuentes',
            'alcance' => 'Solicitudes rutinarias L1 (desbloqueo de cuentas, configuración de correo, guías de VPN)',
            'ahorro_tiempo' => '~50 horas/mes de mesa de ayuda',
            'descripcion' => 'Despliegue de base de conocimientos interactiva con resolución guiada que reduce el volumen de tickets entrantes en un 30%.'
        ]
    ];

    // 5. Resumen Ejecutivo
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
