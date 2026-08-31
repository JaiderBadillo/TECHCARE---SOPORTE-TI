<?php
require_once __DIR__ . '/db.php';

$conn = getDBConnection();
if (!$conn) {
    die('<div style="font-family:sans-serif;padding:40px;text-align:center;"><h2>Error de conexión con la base de datos MySQL</h2><p>Verifique que el servicio MariaDB/MySQL esté iniciado en el puerto 3306 o 3307.</p></div>');
}

// 1. Métricas Generales / KPIs
$total = (int)$conn->query("SELECT COUNT(*) AS t FROM solicitudes")->fetch_assoc()['t'];
$mesActual = (int)$conn->query("SELECT COUNT(*) AS t FROM solicitudes WHERE DATE_FORMAT(fecha_creacion,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')")->fetch_assoc()['t'];
$pendientesTotal = (int)$conn->query("SELECT COUNT(*) AS t FROM solicitudes WHERE estado='pendiente'")->fetch_assoc()['t'];
$enProcesoTotal = (int)$conn->query("SELECT COUNT(*) AS t FROM solicitudes WHERE estado='en_proceso'")->fetch_assoc()['t'];
$resueltosTotal = (int)$conn->query("SELECT COUNT(*) AS t FROM solicitudes WHERE estado='resuelto'")->fetch_assoc()['t'];
$criticosTotal = (int)$conn->query("SELECT COUNT(*) AS t FROM solicitudes WHERE prioridad IN ('critica', 'alta') AND estado != 'resuelto'")->fetch_assoc()['t'];

$tasaResolucion = $total > 0 ? round(($resueltosTotal / $total) * 100, 1) : 0;

// 2. Gráfico: Totales por mes
$sqlMes = "SELECT DATE_FORMAT(fecha_creacion, '%Y-%m') AS mes, COUNT(*) AS total
           FROM solicitudes
           GROUP BY mes
           ORDER BY mes ASC";
$resMes = $conn->query($sqlMes);
$meses = [];
$totalesMes = [];
while ($row = $resMes->fetch_assoc()) {
    $meses[] = $row['mes'];
    $totalesMes[] = (int)$row['total'];
}

// 3. Gráfico: Distribución por Tipo de Problema
$sqlTipos = "SELECT tipo_problema, COUNT(*) AS total
             FROM solicitudes
             GROUP BY tipo_problema
             ORDER BY total DESC";
$resTipos = $conn->query($sqlTipos);
$tiposLabels = [];
$tiposTotales = [];
$tipoMayorDemanda = 'Sin datos';
$maxCant = 0;
while ($row = $resTipos->fetch_assoc()) {
    $tiposLabels[] = $row['tipo_problema'];
    $tiposTotales[] = (int)$row['total'];
    if ((int)$row['total'] > $maxCant) {
        $maxCant = (int)$row['total'];
        $tipoMayorDemanda = $row['tipo_problema'];
    }
}

// 4. Gráfico: Estados
$estados = ['pendiente' => $pendientesTotal, 'en_proceso' => $enProcesoTotal, 'resuelto' => $resueltosTotal];

// 5. Gráfico: Prioridades
$sqlPrio = "SELECT prioridad, COUNT(*) AS total FROM solicitudes GROUP BY prioridad";
$resPrio = $conn->query($sqlPrio);
$prioridades = ['baja' => 0, 'media' => 0, 'alta' => 0, 'critica' => 0];
while ($row = $resPrio->fetch_assoc()) {
    $prioridades[$row['prioridad']] = (int)$row['total'];
}

// 6. Filtros de la Tabla
$filtroEstado = $_GET['estado'] ?? '';
$filtroTipo = $_GET['tipo'] ?? '';
$filtroPrioridad = $_GET['prioridad'] ?? '';
$buscar = trim($_GET['buscar'] ?? '');

$whereClauses = [];

if (in_array($filtroEstado, ['pendiente', 'en_proceso', 'resuelto'], true)) {
    $whereClauses[] = "estado = '" . $conn->real_escape_string($filtroEstado) . "'";
}

$tiposValidos = ['RED', 'SOFTWARE', 'HARDWARE', 'SEGURIDAD', 'CLOUD_SERVIDORES', 'BASE_DE_DATOS'];
if (in_array($filtroTipo, $tiposValidos, true)) {
    $whereClauses[] = "tipo_problema = '" . $conn->real_escape_string($filtroTipo) . "'";
}

$prioridadesValidas = ['baja', 'media', 'alta', 'critica'];
if (in_array($filtroPrioridad, $prioridadesValidas, true)) {
    $whereClauses[] = "prioridad = '" . $conn->real_escape_string($filtroPrioridad) . "'";
}

if ($buscar !== '') {
    $buscarEsc = $conn->real_escape_string($buscar);
    $whereClauses[] = "(nombre LIKE '%$buscarEsc%' OR email LIKE '%$buscarEsc%' OR asunto LIKE '%$buscarEsc%' OR mensaje LIKE '%$buscarEsc%')";
}

$whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

$sqlLista = "SELECT id, nombre, email, asunto, tipo_problema, prioridad, mensaje, estado, solucion_ia, fecha_creacion
             FROM solicitudes
             $whereSql
             ORDER BY fecha_creacion DESC
             LIMIT 100";
$resLista = $conn->query($sqlLista);

$conn->close();

function getTipoBadge($tipo) {
    return match ($tipo) {
        'RED'              => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-hdd-network me-1"></i>RED</span>',
        'SOFTWARE'         => '<span class="badge bg-purple-subtle text-purple border border-purple-subtle"><i class="bi bi-window-sidebar me-1"></i>SOFTWARE</span>',
        'HARDWARE'         => '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="bi bi-laptop me-1"></i>HARDWARE</span>',
        'SEGURIDAD'        => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-shield-lock me-1"></i>SEGURIDAD</span>',
        'CLOUD_SERVIDORES' => '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle"><i class="bi bi-cloud-check me-1"></i>CLOUD</span>',
        'BASE_DE_DATOS'    => '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-database me-1"></i>B. DATOS</span>',
        default            => '<span class="badge bg-secondary-subtle text-secondary border">' . htmlspecialchars($tipo) . '</span>'
    };
}

function getPrioridadBadge($prio) {
    return match ($prio) {
        'critica' => '<span class="badge bg-danger text-white"><i class="bi bi-exclamation-octagon-fill me-1"></i>Crítica</span>',
        'alta'    => '<span class="badge bg-warning text-dark"><i class="bi bi-arrow-up-circle-fill me-1"></i>Alta</span>',
        'media'   => '<span class="badge bg-primary text-white"><i class="bi bi-dash-circle me-1"></i>Media</span>',
        'baja'    => '<span class="badge bg-secondary text-white"><i class="bi bi-arrow-down-circle me-1"></i>Baja</span>',
        default   => '<span class="badge bg-secondary text-white">' . htmlspecialchars($prio) . '</span>'
    };
}

function badgeClass($estado) {
    return match ($estado) {
        'pendiente'  => 'bg-danger text-white',
        'en_proceso' => 'bg-warning text-dark',
        'resuelto'   => 'bg-success text-white',
        default      => 'bg-secondary text-white'
    };
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Gerencial & Hub de Decisiones IA | Soporte TI</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

  <style>
    :root {
      --primary: #4361ee;
      --primary-dark: #3a0ca3;
      --primary-light: #4cc9f0;
      --bg-dark: #0f172a;
      --card-bg: #ffffff;
      --text-main: #1e293b;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #f1f5f9;
      color: var(--text-main);
    }

    h1, h2, h3, h4, h5, h6, .brand-title {
      font-family: 'Outfit', sans-serif;
    }

    .glass-nav {
      background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
      color: white;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .kpi-card {
      border: none;
      border-radius: 16px;
      background: white;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }

    .kpi-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .kpi-icon-box {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
    }

    .chart-card {
      border: none;
      border-radius: 16px;
      background: white;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    /* Estilos IA Hub */
    .ia-decision-hub {
      background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
      border-radius: 20px;
      color: white;
      border: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 20px 30px rgba(15, 23, 42, 0.25);
    }

    .decision-pill-tab {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #e2e8f0;
      border-radius: 10px;
      padding: 0.6rem 1.2rem;
      font-weight: 500;
      font-size: 0.9rem;
      transition: all 0.2s;
      cursor: pointer;
    }

    .decision-pill-tab:hover, .decision-pill-tab.active {
      background: #4361ee !important;
      color: white !important;
      border-color: #4361ee !important;
      box-shadow: 0 4px 12px rgba(67, 97, 238, 0.4);
    }

    .decision-item-card {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 14px;
      padding: 1.2rem;
      transition: all 0.2s;
    }

    .decision-item-card:hover {
      background: rgba(255, 255, 255, 0.09);
      border-color: rgba(67, 97, 238, 0.5);
    }

    .text-purple { color: #8b5cf6 !important; }
    .bg-purple-subtle { background-color: #f3e8ff !important; }
    .border-purple-subtle { border-color: #d8b4fe !important; }

    .select-estado {
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.8rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .pulse-animation {
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
      0% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.85; transform: scale(1.02); }
      100% { opacity: 1; transform: scale(1); }
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark glass-nav sticky-top py-3">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="reporte.php">
      <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center shadow" style="width: 40px; height: 40px;">
        <i class="bi bi-cpu-fill fs-5"></i>
      </div>
      <div>
        <span class="brand-title fw-bold fs-5 tracking-tight">TechCare <span class="text-info">IA Executive Hub</span></span>
        <span class="d-block small text-light opacity-75" style="font-size: 0.7rem;">Soporte TI Inteligente & Toma de Decisiones</span>
      </div>
    </a>
    <div class="d-flex align-items-center gap-2">
      <a href="formulario.html" class="btn btn-primary btn-sm px-3 rounded-pill d-flex align-items-center gap-2 shadow">
        <i class="bi bi-plus-circle-fill"></i>
        <span>Nueva Solicitud</span>
      </a>
    </div>
  </div>
</nav>

<div class="container py-4">

  <!-- Header & Botón Generar IA -->
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-1">Panel de Control Gerencial & Decisiones IA</h3>
      <p class="text-muted small mb-0">Monitoreo en tiempo real de incidentes y recomendaciones estratégicas de negocio.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <button id="btnEjecutarAnalisisGemini" type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-pill">
        <i class="bi bi-stars"></i>
        <span class="fw-semibold">Análisis con Gemini</span>
      </button>
      <button id="btnEjecutarAnalisisLocal" type="button" class="btn btn-outline-dark d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-pill">
        <i class="bi bi-cpu"></i>
        <span class="fw-semibold">Análisis Local (0 Tokens)</span>
      </button>
    </div>
  </div>

  <!-- KPIs Generales -->
  <div class="row g-3 mb-4">
    <!-- Total Histórico -->
    <div class="col-sm-6 col-xl-2">
      <div class="card kpi-card p-3 h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Total Tickets</span>
          <div class="kpi-icon-box bg-primary-subtle text-primary">
            <i class="bi bi-ticket-perforated"></i>
          </div>
        </div>
        <div class="fs-3 fw-bold text-dark"><?= $total ?></div>
        <div class="small text-muted mt-1"><i class="bi bi-calendar-check me-1"></i>Histórico global</div>
      </div>
    </div>

    <!-- Solicitudes Este Mes -->
    <div class="col-sm-6 col-xl-2">
      <div class="card kpi-card p-3 h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Este Mes</span>
          <div class="kpi-icon-box bg-info-subtle text-info">
            <i class="bi bi-calendar-event"></i>
          </div>
        </div>
        <div class="fs-3 fw-bold text-info"><?= $mesActual ?></div>
        <div class="small text-muted mt-1">Actividad mensual</div>
      </div>
    </div>

    <!-- Pendientes -->
    <div class="col-sm-6 col-xl-2">
      <div class="card kpi-card p-3 h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Pendientes</span>
          <div class="kpi-icon-box bg-danger-subtle text-danger">
            <i class="bi bi-clock-history"></i>
          </div>
        </div>
        <div class="fs-3 fw-bold text-danger"><?= $pendientesTotal ?></div>
        <div class="small text-danger mt-1"><i class="bi bi-exclamation-circle me-1"></i>Requieren atención</div>
      </div>
    </div>

    <!-- En Proceso -->
    <div class="col-sm-6 col-xl-2">
      <div class="card kpi-card p-3 h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">En Proceso</span>
          <div class="kpi-icon-box bg-warning-subtle text-warning-emphasis">
            <i class="bi bi-gear-wide-connected"></i>
          </div>
        </div>
        <div class="fs-3 fw-bold text-warning-emphasis"><?= $enProcesoTotal ?></div>
        <div class="small text-muted mt-1">En diagnóstico técnico</div>
      </div>
    </div>

    <!-- Tasa de Resolución -->
    <div class="col-sm-6 col-xl-2">
      <div class="card kpi-card p-3 h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Tasa Resolución</span>
          <div class="kpi-icon-box bg-success-subtle text-success">
            <i class="bi bi-check2-circle"></i>
          </div>
        </div>
        <div class="fs-3 fw-bold text-success"><?= $tasaResolucion ?>%</div>
        <div class="small text-muted mt-1"><?= $resueltosTotal ?> resueltos</div>
      </div>
    </div>

    <!-- Mayor Demanda -->
    <div class="col-sm-6 col-xl-2">
      <div class="card kpi-card p-3 h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Top Especialidad</span>
          <div class="kpi-icon-box bg-dark text-white">
            <i class="bi bi-bar-chart-fill"></i>
          </div>
        </div>
        <div class="fs-5 fw-bold text-dark text-truncate" title="<?= $tipoMayorDemanda ?>"><?= $tipoMayorDemanda ?></div>
        <div class="small text-muted mt-1">Mayor carga operativa</div>
      </div>
    </div>
  </div>

  <!-- SECCIÓN CENTRAL: HUB DE TOMA DE DECISIONES CON INTELIGENCIA ARTIFICIAL -->
  <div class="ia-decision-hub p-4 p-md-5 mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
      <div>
        <div class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2 shadow-sm">
          <i class="bi bi-robot me-1"></i> IA Prescriptiva & Asistente Gerencial
        </div>
        <h4 class="fw-bold text-white mb-1">Módulo de Decisiones Estratégicas para Soporte TI</h4>
        <p class="text-light opacity-75 small mb-0">La IA analiza la demanda real de tickets y prescribe decisiones de alto impacto en contratación, infraestructura y capacitación.</p>
      </div>
      <div class="d-flex flex-column align-items-end gap-2" id="iaStatusBadge">
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
          <i class="bi bi-lightning-charge-fill me-1"></i> Análisis Activo
        </span>
        <span class="badge rounded-pill bg-secondary" id="iaHubMotorBadge">
          <i class="bi bi-cpu me-1"></i>Cargando motor IA...
        </span>
      </div>
    </div>

    <!-- Resumen Ejecutivo -->
    <div class="alert bg-white bg-opacity-10 border border-white border-opacity-20 text-white rounded-4 p-3 mb-4 shadow-sm">
      <div class="d-flex gap-3 align-items-start">
        <i class="bi bi-chat-quote-fill fs-2 text-info flex-shrink-0"></i>
        <div>
          <strong class="d-block text-info mb-1 fw-bold">Dictamen Ejecutivo de la IA:</strong>
          <p id="iaResumenEjecutivo" class="mb-0 small text-light opacity-90 leading-relaxed">
            Cargando insights de inteligencia artificial basados en los incidentes registrados...
          </p>
        </div>
      </div>
    </div>

    <!-- Pestañas de Decisiones -->
    <ul class="nav nav-pills gap-2 mb-4" id="pills-decisiones" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="decision-pill-tab active d-flex align-items-center gap-2" id="pill-contratacion-tab" data-bs-toggle="pill" data-bs-target="#pill-contratacion" type="button" role="tab">
          <i class="bi bi-person-badge-fill text-info"></i>
          <span>1. Contratación de Ingenieros</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="decision-pill-tab d-flex align-items-center gap-2" id="pill-infra-tab" data-bs-toggle="pill" data-bs-target="#pill-infra" type="button" role="tab">
          <i class="bi bi-server text-warning"></i>
          <span>2. Inversión en Infraestructura</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="decision-pill-tab d-flex align-items-center gap-2" id="pill-capacitacion-tab" data-bs-toggle="pill" data-bs-target="#pill-capacitacion" type="button" role="tab">
          <i class="bi bi-mortarboard-fill text-success"></i>
          <span>3. Plan de Capacitación (Upskilling)</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="decision-pill-tab d-flex align-items-center gap-2" id="pill-auto-tab" data-bs-toggle="pill" data-bs-target="#pill-auto" type="button" role="tab">
          <i class="bi bi-cpu-fill text-danger"></i>
          <span>4. Automatización & Deflexión</span>
        </button>
      </li>
    </ul>

    <!-- Contenido de las Pestañas -->
    <div class="tab-content" id="pills-decisionesContent">
      
      <!-- 1. Contratación -->
      <div class="tab-pane fade show active" id="pill-contratacion" role="tabpanel">
        <h6 class="text-white-50 text-uppercase tracking-wider small fw-bold mb-3">Recomendaciones de Talento Técnico según Demanda</h6>
        <div id="iaContratacionesContainer" class="row g-3">
          <div class="text-center py-4 text-white-50"><div class="spinner-border spinner-border-sm me-2"></div> Cargando perfiles...</div>
        </div>
      </div>

      <!-- 2. Infraestructura -->
      <div class="tab-pane fade" id="pill-infra" role="tabpanel">
        <h6 class="text-white-50 text-uppercase tracking-wider small fw-bold mb-3">Inversiones Prioritarias en Equipamiento y Herramientas</h6>
        <div id="iaInfraContainer" class="row g-3">
          <div class="text-center py-4 text-white-50"><div class="spinner-border spinner-border-sm me-2"></div> Cargando inversiones...</div>
        </div>
      </div>

      <!-- 3. Capacitación -->
      <div class="tab-pane fade" id="pill-capacitacion" role="tabpanel">
        <h6 class="text-white-50 text-uppercase tracking-wider small fw-bold mb-3">Plan de Entrenamiento Interno para Disminución de Escalamientos</h6>
        <div id="iaCapacitacionContainer" class="row g-3">
          <div class="text-center py-4 text-white-50"><div class="spinner-border spinner-border-sm me-2"></div> Cargando plan de capacitación...</div>
        </div>
      </div>

      <!-- 4. Automatización -->
      <div class="tab-pane fade" id="pill-auto" role="tabpanel">
        <h6 class="text-white-50 text-uppercase tracking-wider small fw-bold mb-3">Oportunidades de Auto-Resolución y Ahorro de Horas Hombre</h6>
        <div id="iaAutoContainer" class="row g-3">
          <div class="text-center py-4 text-white-50"><div class="spinner-border spinner-border-sm me-2"></div> Cargando automatizaciones...</div>
        </div>
      </div>

    </div>
  </div>

  <!-- Gráficas Estadísticas -->
  <div class="row g-4 mb-4">
    <!-- Solicitudes por Mes -->
    <div class="col-lg-7">
      <div class="card chart-card p-4 h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-graph-up text-primary me-2"></i>Evolución Mensual de Incidentes</h6>
          <span class="badge bg-light text-muted border">Histórico</span>
        </div>
        <div style="position: relative; height: 260px;">
          <canvas id="chartMes"></canvas>
        </div>
      </div>
    </div>

    <!-- Distribución por Tipo de Problema -->
    <div class="col-lg-5">
      <div class="card chart-card p-4 h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart-fill text-info me-2"></i>Demanda por Especialidad</h6>
          <span class="badge bg-light text-muted border">Tipología</span>
        </div>
        <div style="position: relative; height: 260px;">
          <canvas id="chartTipos"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabla Interactiva de Solicitudes -->
  <div class="card chart-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
      <div>
        <h5 class="fw-bold text-dark mb-0">Gestión de Tickets & Asistente Técnico IA</h5>
        <span class="text-muted small">Haz clic en <strong>"💡 Solución IA"</strong> en cualquier ticket para ver diagnósticos y comandos de resolución paso a paso.</span>
      </div>
      
      <!-- Filtros Rápidos -->
      <form method="GET" action="reporte.php" class="d-flex flex-wrap gap-2 align-items-center">
        <!-- Filtro Estado -->
        <select name="estado" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
          <option value="">Todos los Estados</option>
          <option value="pendiente" <?= $filtroEstado === 'pendiente' ? 'selected' : '' ?>>Pendientes</option>
          <option value="en_proceso" <?= $filtroEstado === 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
          <option value="resuelto" <?= $filtroEstado === 'resuelto' ? 'selected' : '' ?>>Resueltos</option>
        </select>

        <!-- Filtro Tipo -->
        <select name="tipo" class="form-select form-select-sm" style="width: 160px;" onchange="this.form.submit()">
          <option value="">Todas las Áreas</option>
          <option value="RED" <?= $filtroTipo === 'RED' ? 'selected' : '' ?>>RED</option>
          <option value="SOFTWARE" <?= $filtroTipo === 'SOFTWARE' ? 'selected' : '' ?>>SOFTWARE</option>
          <option value="HARDWARE" <?= $filtroTipo === 'HARDWARE' ? 'selected' : '' ?>>HARDWARE</option>
          <option value="SEGURIDAD" <?= $filtroTipo === 'SEGURIDAD' ? 'selected' : '' ?>>SEGURIDAD</option>
          <option value="CLOUD_SERVIDORES" <?= $filtroTipo === 'CLOUD_SERVIDORES' ? 'selected' : '' ?>>CLOUD / SERVIDORES</option>
          <option value="BASE_DE_DATOS" <?= $filtroTipo === 'BASE_DE_DATOS' ? 'selected' : '' ?>>BASE DE DATOS</option>
        </select>

        <!-- Filtro Prioridad -->
        <select name="prioridad" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
          <option value="">Toda Prioridad</option>
          <option value="critica" <?= $filtroPrioridad === 'critica' ? 'selected' : '' ?>>Crítica</option>
          <option value="alta" <?= $filtroPrioridad === 'alta' ? 'selected' : '' ?>>Alta</option>
          <option value="media" <?= $filtroPrioridad === 'media' ? 'selected' : '' ?>>Media</option>
          <option value="baja" <?= $filtroPrioridad === 'baja' ? 'selected' : '' ?>>Baja</option>
        </select>

        <!-- Búsqueda -->
        <div class="input-group input-group-sm" style="width: 220px;">
          <input type="text" name="buscar" class="form-control" placeholder="Buscar..." value="<?= htmlspecialchars($buscar) ?>">
          <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
        </div>

        <?php if ($filtroEstado || $filtroTipo || $filtroPrioridad || $buscar): ?>
          <a href="reporte.php" class="btn btn-sm btn-outline-danger" title="Limpiar filtros"><i class="bi bi-x-circle"></i></a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-dark">
          <tr>
            <th style="width: 55px;">#</th>
            <th style="width: 130px;">Fecha</th>
            <th>Cliente / Solicitante</th>
            <th>Asunto / Incidencia</th>
            <th>Especialidad</th>
            <th>Prioridad</th>
            <th style="width: 150px;">Estado</th>
            <th style="width: 130px;" class="text-center">Asistente IA</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($resLista && $resLista->num_rows > 0): ?>
            <?php while ($row = $resLista->fetch_assoc()): ?>
              <tr id="fila-<?= $row['id'] ?>">
                <td class="fw-bold text-muted"><?= $row['id'] ?></td>
                <td class="small text-nowrap text-muted"><?= date('d/m/Y H:i', strtotime($row['fecha_creacion'])) ?></td>
                <td>
                  <div class="fw-semibold"><?= htmlspecialchars($row['nombre']) ?></div>
                  <div class="small text-muted"><?= htmlspecialchars($row['email']) ?></div>
                </td>
                <td>
                  <div class="fw-semibold text-dark"><?= htmlspecialchars($row['asunto']) ?></div>
                  <div class="small text-muted text-truncate" style="max-width: 320px;" title="<?= htmlspecialchars($row['mensaje']) ?>">
                    <?= htmlspecialchars($row['mensaje']) ?>
                  </div>
                </td>
                <td><?= getTipoBadge($row['tipo_problema']) ?></td>
                <td><?= getPrioridadBadge($row['prioridad']) ?></td>
                <td>
                  <select class="form-select form-select-sm select-estado <?= badgeClass($row['estado']) ?>"
                          data-id="<?= $row['id'] ?>"
                          data-anterior="<?= $row['estado'] ?>">
                    <option value="pendiente" <?= $row['estado'] === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="en_proceso" <?= $row['estado'] === 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
                    <option value="resuelto" <?= $row['estado'] === 'resuelto' ? 'selected' : '' ?>>Resuelto</option>
                  </select>
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-solucion-ia" 
                          data-id="<?= $row['id'] ?>"
                          data-asunto="<?= htmlspecialchars($row['asunto']) ?>">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i> Solución IA
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                No se encontraron solicitudes con los filtros aplicados.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- Modal Solución Técnica con IA -->
<div class="modal fade" id="modalSolucionIA" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-dark text-white p-4">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-primary text-white rounded-3 p-2">
            <i class="bi bi-robot fs-5"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold" id="modalIATitle">Diagnóstico y Solución Técnica con IA</h5>
            <span class="small text-white-50" id="modalIASubtitle">Copilot de Soporte para Ingenieros</span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4" id="modalIABody">
        <div class="text-center py-4" id="modalIALoader">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2 text-muted small" id="modalLoaderText">Generando diagnóstico técnico y guía de solución paso a paso...</p>
        </div>
        <div id="modalIAContent" class="d-none">
          
          <!-- Banner de notificación de regeneración -->
          <div id="iaModalToast" class="alert alert-success border-0 rounded-3 py-2 px-3 small d-none mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <span id="iaModalToastText">Diagnóstico actualizado exitosamente.</span>
          </div>

          <!-- Alerta de Error de API / Modo Local -->
          <div id="iaModalWarning" class="alert alert-warning border-0 rounded-3 py-2 px-3 small d-none mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning flex-shrink-0"></i>
            <div>
              <strong>Aviso de Conexión IA:</strong>
              <span id="iaModalWarningText"></span>
            </div>
          </div>

          <!-- Diagnóstico y Enfoque -->
          <div class="card bg-light border-0 rounded-3 p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
              <div class="d-flex flex-wrap gap-1 align-items-center">
                <span class="badge bg-primary text-white px-3 py-1 rounded-pill" id="iaModalEnfoqueBadge">Enfoque 1</span>
                <span class="badge bg-secondary text-white px-2 py-1 rounded-pill" id="iaModalTimeBadge"></span>
                <span class="badge rounded-pill bg-secondary" id="iaMotorBadge"><i class="bi bi-cpu me-1"></i>Motor IA</span>
              </div>
              <span class="badge bg-dark text-white px-3 py-1 rounded-pill" id="iaModalTiempo">~20 min</span>
            </div>
            <h6 class="fw-bold text-dark mb-1 mt-2"><i class="bi bi-activity text-primary me-2"></i><span id="iaModalDiag"></span></h6>
            <div class="small mt-2"><strong>Causa Raíz Probable:</strong> <span class="text-muted" id="iaModalCausa"></span></div>
          </div>

          <!-- Pasos de Resolución -->
          <h6 class="fw-bold text-dark mb-2"><i class="bi bi-list-check text-success me-2"></i>Plan de Acción Técnico (Paso a Paso)</h6>
          <ol class="list-group list-group-numbered mb-3 small" id="iaModalPasos">
          </ol>

          <!-- Comandos y Herramientas -->
          <div class="card bg-dark text-light border-0 rounded-3 p-3 mb-3">
            <h6 class="text-info fw-bold mb-2 small"><i class="bi bi-terminal-fill me-2"></i>Comandos / Herramientas Sugeridas:</h6>
            <div id="iaModalComandos" class="d-flex flex-wrap gap-2"></div>
          </div>

          <!-- Prevención -->
          <div class="alert alert-info border-0 rounded-3 small mb-0">
            <strong><i class="bi bi-shield-check me-1"></i> Medida Preventiva Recomendada:</strong>
            <span id="iaModalPreventiva" class="d-block mt-1"></span>
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light p-3 d-flex justify-content-between flex-wrap gap-2">
        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cerrar</button>
        <div class="d-flex flex-wrap gap-2">
          <button type="button" id="btnRegenerarLocal" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm fw-semibold" title="Genera solución inmediata usando reglas expertas sin gastar tokens">
            <i class="bi bi-cpu me-1"></i> Solución Local (0 Tokens)
          </button>
          <button type="button" id="btnRegenerarGemini" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-semibold" title="Consulta a Google Gemini Cloud para diagnóstico personalizado">
            <i class="bi bi-stars me-1"></i> Diagnóstico Gemini (Cloud)
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Inicializar gráficos
const meses = <?= json_encode($meses) ?>;
const totalesMes = <?= json_encode($totalesMes) ?>;
const tiposLabels = <?= json_encode($tiposLabels) ?>;
const tiposTotales = <?= json_encode($tiposTotales) ?>;

const ctxMes = document.getElementById('chartMes');
if (ctxMes) {
  new Chart(ctxMes, {
    type: 'bar',
    data: {
      labels: meses,
      datasets: [{
        label: 'Solicitudes',
        data: totalesMes,
        backgroundColor: '#4361ee',
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { precision: 0 } },
        x: { grid: { display: false } }
      }
    }
  });
}

const ctxTipos = document.getElementById('chartTipos');
if (ctxTipos) {
  new Chart(ctxTipos, {
    type: 'doughnut',
    data: {
      labels: tiposLabels,
      datasets: [{
        data: tiposTotales,
        backgroundColor: ['#4361ee', '#8b5cf6', '#f59e0b', '#ef4444', '#06b6d4', '#10b981']
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
      }
    }
  });
}

// Cambio de estado de tickets
const clasesBadge = {
  pendiente: 'bg-danger text-white',
  en_proceso: 'bg-warning text-dark',
  resuelto: 'bg-success text-white'
};

function actualizarClaseSelect(select) {
  select.classList.remove('bg-danger', 'bg-warning', 'text-dark', 'bg-success', 'text-white', 'bg-secondary');
  const clases = (clasesBadge[select.value] || 'bg-secondary text-white').split(' ');
  clases.forEach(c => select.classList.add(c));
}

document.querySelectorAll('.select-estado').forEach(select => {
  select.addEventListener('change', async function () {
    const id = this.dataset.id;
    const estado = this.value;
    const anterior = this.dataset.anterior || estado;

    const data = new FormData();
    data.append('id', id);
    data.append('estado', estado);

    try {
      const res = await fetch('actualizar_estado.php', { method: 'POST', body: data });
      const json = await res.json();
      if (json.ok) {
        actualizarClaseSelect(this);
        this.dataset.anterior = estado;
      } else {
        alert('Error: ' + json.error);
        this.value = anterior;
      }
    } catch (e) {
      alert('Error de conexión al actualizar estado.');
      this.value = anterior;
    }
  });
});

// Carga del motor de Decisiones Estratégicas de IA
async function cargarDecisionesIA(motor = 'gemini') {
  const btnGemini = document.getElementById('btnEjecutarAnalisisGemini');
  const btnLocal = document.getElementById('btnEjecutarAnalisisLocal');

  if (btnGemini) btnGemini.disabled = true;
  if (btnLocal) btnLocal.disabled = true;

  if (motor === 'local') {
    if (btnLocal) btnLocal.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Analizando Local...';
  } else {
    if (btnGemini) btnGemini.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Consultando Gemini...';
  }

  try {
    const res = await fetch('analizar_ia.php?motor=' + encodeURIComponent(motor));
    const json = await res.json();

    if (!json.ok) {
      document.getElementById('iaResumenEjecutivo').textContent = 'Error al cargar análisis de IA: ' + json.error;
      return;
    }

    const ins = json.insights;
    document.getElementById('iaResumenEjecutivo').textContent = ins.resumen_ejecutivo || '';

    // Badge del motor IA usado en el Hub Ejecutivo
    const hubMotorBadge = document.getElementById('iaHubMotorBadge');
    if (hubMotorBadge && ins.motor_ia) {
      const isGemini = ins.motor_ia.toLowerCase().includes('gemini');
      hubMotorBadge.className = 'badge rounded-pill ' + (isGemini ? 'bg-success text-white' : 'bg-secondary text-white');
      hubMotorBadge.innerHTML = (isGemini ? '<i class="bi bi-stars me-1"></i>' : '<i class="bi bi-cpu me-1"></i>') + ins.motor_ia;
    }

    // 1. Contratación
    const cCont = document.getElementById('iaContratacionesContainer');
    if (cCont) {
      cCont.innerHTML = '';
      (ins.decision_contratacion || []).forEach(c => {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4';
        col.innerHTML = `
          <div class="decision-item-card h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-primary text-white">${c.especialidad}</span>
                <span class="badge bg-warning text-dark">${c.prioridad_contratacion}</span>
              </div>
              <h6 class="fw-bold text-white mb-1">${c.perfil}</h6>
              <div class="small text-info mb-2"><i class="bi bi-award me-1"></i>Seniority: ${c.seniority}</div>
              <p class="small text-light opacity-75 mb-3">${c.justificacion}</p>
              
              <div class="mb-3">
                <strong class="d-block small text-white-50 mb-1">Certificaciones Clave:</strong>
                <div class="d-flex flex-wrap gap-1">
                  ${(c.certificaciones_clave || []).map(cert => `<span class="badge bg-secondary bg-opacity-50 text-light border border-secondary">${cert}</span>`).join('')}
                </div>
              </div>
            </div>
            <div class="alert bg-success bg-opacity-10 border border-success border-opacity-25 text-success small mb-0 p-2 rounded-3">
              <i class="bi bi-graph-up-arrow me-1"></i> <strong>Impacto:</strong> ${c.impacto_esperado}
            </div>
          </div>
        `;
        cCont.appendChild(col);
      });
    }

    // 2. Infraestructura
    const iCont = document.getElementById('iaInfraContainer');
    if (iCont) {
      iCont.innerHTML = '';
      (ins.decision_infraestructura || []).forEach(inf => {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4';
        col.innerHTML = `
          <div class="decision-item-card h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-warning text-dark">${inf.categoria}</span>
                <span class="badge bg-light text-dark">Costo: ${inf.costo_estimado}</span>
              </div>
              <h6 class="fw-bold text-white mb-2">${inf.inversion}</h6>
              <p class="small text-light opacity-75 mb-3">${inf.justificacion}</p>
            </div>
            <div class="alert bg-info bg-opacity-10 border border-info border-opacity-25 text-info small mb-0 p-2 rounded-3">
              <i class="bi bi-shield-check me-1"></i> <strong>Retorno:</strong> ${inf.retorno_inversion}
            </div>
          </div>
        `;
        iCont.appendChild(col);
      });
    }

    // 3. Capacitación
    const capCont = document.getElementById('iaCapacitacionContainer');
    if (capCont) {
      capCont.innerHTML = '';
      (ins.decision_capacitacion || []).forEach(cap => {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4';
        col.innerHTML = `
          <div class="decision-item-card h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-success text-white"><i class="bi bi-clock me-1"></i>${cap.duracion}</span>
                <span class="badge bg-secondary text-light">${cap.audiencia}</span>
              </div>
              <h6 class="fw-bold text-white mb-2">${cap.tema}</h6>
              <p class="small text-light opacity-75 mb-3">${cap.objetivo}</p>
            </div>
          </div>
        `;
        capCont.appendChild(col);
      });
    }

    // 4. Automatización
    const aCont = document.getElementById('iaAutoContainer');
    if (aCont) {
      aCont.innerHTML = '';
      (ins.decision_automatizacion || []).forEach(aut => {
        const col = document.createElement('div');
        col.className = 'col-md-6';
        col.innerHTML = `
          <div class="decision-item-card h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-danger text-white">${aut.alcance}</span>
                <span class="badge bg-success text-white"><i class="bi bi-lightning-charge me-1"></i>${aut.ahorro_tiempo}</span>
              </div>
              <h6 class="fw-bold text-white mb-2">${aut.iniciativa}</h6>
              <p class="small text-light opacity-75 mb-3">${aut.descripcion}</p>
            </div>
          </div>
        `;
        aCont.appendChild(col);
      });
    }

  } catch (err) {
    document.getElementById('iaResumenEjecutivo').textContent = 'Error de conexión con el motor de IA.';
  } finally {
    if (btnGemini) {
      btnGemini.disabled = false;
      btnGemini.innerHTML = '<i class="bi bi-stars"></i> <span class="fw-semibold">Análisis con Gemini</span>';
    }
    if (btnLocal) {
      btnLocal.disabled = false;
      btnLocal.innerHTML = '<i class="bi bi-cpu"></i> <span class="fw-semibold">Análisis Local (0 Tokens)</span>';
    }
  }
}

// Modal Asistente Solución IA por Ticket
let currentTicketId = 0;
let modalIAInstance = null;

function abrirModalSolucion(ticketId, ticketAsunto) {
  currentTicketId = ticketId;
  document.getElementById('modalIASubtitle').textContent = 'Ticket #' + currentTicketId + ': ' + ticketAsunto;
  
  // Ocultar avisos anteriores
  document.getElementById('iaModalToast').classList.add('d-none');
  const warningBox = document.getElementById('iaModalWarning');
  if (warningBox) warningBox.classList.add('d-none');

  const modalEl = document.getElementById('modalSolucionIA');
  if (!modalIAInstance) {
    modalIAInstance = new bootstrap.Modal(modalEl);
  }
  modalIAInstance.show();
  obtenerSolucionTicket(currentTicketId, false, 'gemini');
}

document.querySelectorAll('.btn-solucion-ia').forEach(btn => {
  btn.addEventListener('click', function (e) {
    e.preventDefault();
    const id = this.dataset.id;
    const asunto = this.dataset.asunto || '';
    abrirModalSolucion(id, asunto);
  });
});

document.getElementById('btnRegenerarGemini')?.addEventListener('click', function () {
  if (currentTicketId > 0) {
    obtenerSolucionTicket(currentTicketId, true, 'gemini');
  }
});

document.getElementById('btnRegenerarLocal')?.addEventListener('click', function () {
  if (currentTicketId > 0) {
    obtenerSolucionTicket(currentTicketId, true, 'local');
  }
});

async function obtenerSolucionTicket(id, force, motor = 'gemini') {
  const loader = document.getElementById('modalIALoader');
  const loaderText = document.getElementById('modalLoaderText');
  const content = document.getElementById('modalIAContent');
  const btnGemini = document.getElementById('btnRegenerarGemini');
  const btnLocal = document.getElementById('btnRegenerarLocal');
  const toastBox = document.getElementById('iaModalToast');
  const toastText = document.getElementById('iaModalToastText');

  if (btnGemini) btnGemini.disabled = true;
  if (btnLocal) btnLocal.disabled = true;

  if (motor === 'local') {
    loaderText.textContent = 'Generando diagnóstico técnico con el Motor Experto Local (0 Tokens)...';
  } else {
    loaderText.textContent = 'Consultando Google Gemini Cloud para diagnóstico técnico personalizado...';
  }

  loader.classList.remove('d-none');
  content.classList.add('d-none');

  try {
    const data = new FormData();
    data.append('id', id);
    data.append('motor', motor);
    if (force) data.append('force', '1');

    const res = await fetch('solucionar_ticket_ia.php', { method: 'POST', body: data });
    const json = await res.json();

    if (!json.ok) {
      alert('Error: ' + (json.error || 'No se pudo generar la solución.'));
      if (modalIAInstance) modalIAInstance.hide();
      return;
    }

    const sol = json.solucion;
    const vIndex = json.variante_index || 1;
    const motorIA = sol.motor_ia || 'Motor IA';

    document.getElementById('iaModalEnfoqueBadge').textContent = '✨ ' + (sol.enfoque_nombre || 'Enfoque #' + vIndex);
    document.getElementById('iaModalTimeBadge').textContent = sol.fecha_generacion ? 'Actualizado: ' + sol.fecha_generacion : 'Recién generado';
    document.getElementById('iaModalTiempo').textContent = 'Est. ' + (sol.tiempo_estimado || '30 min');
    document.getElementById('iaModalDiag').textContent = sol.diagnostico || '';
    document.getElementById('iaModalCausa').textContent = sol.causa_probable || '';

    // Badge del motor IA usado
    const motorBadgeEl = document.getElementById('iaMotorBadge');
    if (motorBadgeEl) {
      const isGemini = motorIA.toLowerCase().includes('gemini');
      motorBadgeEl.className = 'badge rounded-pill ' + (isGemini ? 'bg-success text-white' : 'bg-secondary text-white');
      motorBadgeEl.innerHTML = (isGemini ? '<i class="bi bi-stars me-1"></i>' : '<i class="bi bi-cpu me-1"></i>') + motorIA;
    }

    const pasosList = document.getElementById('iaModalPasos');
    pasosList.innerHTML = '';
    (sol.pasos_resolucion || []).forEach(p => {
      const li = document.createElement('li');
      li.className = 'list-group-item';
      li.textContent = p;
      pasosList.appendChild(li);
    });

    const cmdsCont = document.getElementById('iaModalComandos');
    cmdsCont.innerHTML = '';
    (sol.comandos_herramientas || []).forEach(cmd => {
      const code = document.createElement('code');
      code.className = 'bg-secondary bg-opacity-25 text-info p-1 px-2 rounded border border-secondary';
      code.textContent = cmd;
      cmdsCont.appendChild(code);
    });

    document.getElementById('iaModalPreventiva').textContent = sol.accion_preventiva || '';

    const warningBox = document.getElementById('iaModalWarning');
    const warningText = document.getElementById('iaModalWarningText');
    if (warningBox && warningText) {
      if (sol.error_ia) {
        warningText.textContent = `${sol.error_ia}. Se activó el Motor Experto Local de contingencia.`;
        warningBox.classList.remove('d-none');
      } else {
        warningBox.classList.add('d-none');
      }
    }

    if (force) {
      toastText.textContent = json.mensaje || '¡Diagnóstico generado exitosamente!';
      toastBox.classList.remove('d-none');
    } else {
      toastBox.classList.add('d-none');
    }

    loader.classList.add('d-none');
    content.classList.remove('d-none');

  } catch (err) {
    alert('Error al comunicar con el asistente de IA.');
    if (modalIAInstance) modalIAInstance.hide();
  } finally {
    if (btnGemini) btnGemini.disabled = false;
    if (btnLocal) btnLocal.disabled = false;
  }
}

// Evento para pestañas del HUB IA (garantiza el cambio de pestaña sin fallas)
document.querySelectorAll('.decision-pill-tab').forEach(tabBtn => {
  tabBtn.addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelectorAll('.decision-pill-tab').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    const targetSelector = this.getAttribute('data-bs-target');
    document.querySelectorAll('.tab-pane').forEach(p => {
      p.classList.remove('show', 'active');
    });
    const targetPane = document.querySelector(targetSelector);
    if (targetPane) {
      targetPane.classList.add('show', 'active');
    }
  });
});

document.getElementById('btnEjecutarAnalisisGemini')?.addEventListener('click', () => cargarDecisionesIA('gemini'));
document.getElementById('btnEjecutarAnalisisLocal')?.addEventListener('click', () => cargarDecisionesIA('local'));
document.addEventListener('DOMContentLoaded', () => cargarDecisionesIA('gemini'));
</script>
</body>
</html>
