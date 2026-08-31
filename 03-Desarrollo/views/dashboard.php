<?php
$pageTitle = "Dashboard Gerencial & IA - TechCare Soporte TI";
$activePage = "dashboard";
require_once __DIR__ . '/partials/header.php';

// Helper para badges
function badgeClass($estado) {
    switch ($estado) {
        case 'pendiente': return 'bg-danger text-white';
        case 'en_proceso': return 'bg-warning text-dark';
        case 'resuelto': return 'bg-success text-white';
        default: return 'bg-secondary text-white';
    }
}

function getTipoBadge($tipo) {
    $map = [
        'RED' => ['bg' => 'bg-info text-dark', 'icon' => 'bi-wifi', 'label' => 'Redes / VPN'],
        'SOFTWARE' => ['bg' => 'bg-primary text-white', 'icon' => 'bi-window-stack', 'label' => 'Software'],
        'HARDWARE' => ['bg' => 'bg-warning text-dark', 'icon' => 'bi-cpu', 'label' => 'Hardware'],
        'SEGURIDAD' => ['bg' => 'bg-danger text-white', 'icon' => 'bi-shield-lock', 'label' => 'Seguridad'],
        'CLOUD_SERVIDORES' => ['bg' => 'bg-dark text-white', 'icon' => 'bi-cloud', 'label' => 'Cloud / Servidores'],
        'BASE_DE_DATOS' => ['bg' => 'bg-secondary text-white', 'icon' => 'bi-database', 'label' => 'Base de Datos']
    ];
    $item = $map[$tipo] ?? ['bg' => 'bg-secondary text-white', 'icon' => 'bi-tag', 'label' => $tipo];
    return '<span class="badge ' . $item['bg'] . ' rounded-pill px-2 py-1"><i class="bi ' . $item['icon'] . ' me-1"></i>' . $item['label'] . '</span>';
}

function getPrioridadBadge($prioridad) {
    switch ($prioridad) {
        case 'critica': return '<span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-fire me-1"></i>Crítica</span>';
        case 'alta': return '<span class="badge bg-warning text-dark px-2 py-1">Alta</span>';
        case 'media': return '<span class="badge bg-info text-dark px-2 py-1">Media</span>';
        case 'baja': return '<span class="badge bg-light text-dark border px-2 py-1">Baja</span>';
        default: return '<span class="badge bg-secondary">' . htmlspecialchars($prioridad) . '</span>';
    }
}
?>

<div class="container-fluid px-4 py-4">
  
  <!-- Encabezado del Dashboard -->
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
        <div class="fs-3 fw-bold text-dark"><?= $metrics['total'] ?></div>
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
        <div class="fs-3 fw-bold text-dark"><?= $metrics['esteMes'] ?></div>
        <div class="small text-muted mt-1">Registros del mes</div>
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
        <div class="fs-3 fw-bold text-danger"><?= $metrics['estados']['pendiente'] ?></div>
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
        <div class="fs-3 fw-bold text-warning-emphasis"><?= $metrics['estados']['en_proceso'] ?></div>
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
        <div class="fs-3 fw-bold text-success"><?= $metrics['tasaResolucion'] ?>%</div>
        <div class="small text-muted mt-1"><?= $metrics['estados']['resuelto'] ?> resueltos</div>
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
        <div class="fs-5 fw-bold text-dark text-truncate" title="<?= $metrics['tipoMayorDemanda'] ?>"><?= $metrics['tipoMayorDemanda'] ?></div>
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
          <i class="bi bi-hdd-network-fill text-warning"></i>
          <span>2. Inversión en Infraestructura</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="decision-pill-tab d-flex align-items-center gap-2" id="pill-capacitacion-tab" data-bs-toggle="pill" data-bs-target="#pill-capacitacion" type="button" role="tab">
          <i class="bi bi-mortarboard-fill text-success"></i>
          <span>3. Plan de Capacitación Técnica</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="decision-pill-tab d-flex align-items-center gap-2" id="pill-auto-tab" data-bs-toggle="pill" data-bs-target="#pill-auto" type="button" role="tab">
          <i class="bi bi-cpu-fill text-danger"></i>
          <span>4. Automatización & Autoservicio</span>
        </button>
      </li>
    </ul>

    <!-- Contenido de las Pestañas -->
    <div class="tab-content" id="pills-tabContent">
      <!-- 1. Contratación -->
      <div class="tab-pane fade show active" id="pill-contratacion" role="tabpanel">
        <div class="row g-3" id="iaContratacionesContainer">
          <div class="col-12 text-center py-4 text-white-50"><div class="spinner-border spinner-border-sm text-info me-2"></div> Analizando demanda de personal técnico...</div>
        </div>
      </div>

      <!-- 2. Infraestructura -->
      <div class="tab-pane fade" id="pill-infra" role="tabpanel">
        <div class="row g-3" id="iaInfraContainer">
          <div class="col-12 text-center py-4 text-white-50"><div class="spinner-border spinner-border-sm text-warning me-2"></div> Evaluando salud de infraestructura y equipamiento...</div>
        </div>
      </div>

      <!-- 3. Capacitación -->
      <div class="tab-pane fade" id="pill-capacitacion" role="tabpanel">
        <div class="row g-3" id="iaCapacitacionContainer">
          <div class="col-12 text-center py-4 text-white-50"><div class="spinner-border spinner-border-sm text-success me-2"></div> Estructurando plan de capacitación del equipo...</div>
        </div>
      </div>

      <!-- 4. Automatización -->
      <div class="tab-pane fade" id="pill-auto" role="tabpanel">
        <div class="row g-3" id="iaAutoContainer">
          <div class="col-12 text-center py-4 text-white-50"><div class="spinner-border spinner-border-sm text-danger me-2"></div> Identificando oportunidades de scripts y autoservicio...</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Gráficos Estadísticos -->
  <div class="row g-3 mb-4">
    <div class="col-lg-8">
      <div class="card p-3 shadow-sm h-100 rounded-4 border-0">
        <h6 class="fw-bold mb-3"><i class="bi bi-graph-up text-primary me-2"></i>Evolución de Solicitudes por Mes</h6>
        <div style="height: 240px;">
          <canvas id="chartMes"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card p-3 shadow-sm h-100 rounded-4 border-0">
        <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart text-info me-2"></i>Distribución por Especialidad</h6>
        <div style="height: 240px;">
          <canvas id="chartTipos"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Barra de Filtros y Búsqueda -->
  <div class="card p-3 shadow-sm rounded-4 border-0 mb-4">
    <form method="GET" action="index.php" class="row g-2 align-items-center">
      <input type="hidden" name="route" value="dashboard">
      
      <div class="col-md-3">
        <input type="text" name="q" class="form-control form-control-sm" placeholder="Buscar por nombre, correo o asunto..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
      </div>

      <div class="col-md-2">
        <select name="estado" class="form-select form-select-sm">
          <option value="">Todos los estados</option>
          <option value="pendiente" <?= ($_GET['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendientes</option>
          <option value="en_proceso" <?= ($_GET['estado'] ?? '') === 'en_proceso' ? 'selected' : '' ?>>En proceso</option>
          <option value="resuelto" <?= ($_GET['estado'] ?? '') === 'resuelto' ? 'selected' : '' ?>>Resueltos</option>
        </select>
      </div>

      <div class="col-md-3">
        <select name="tipo" class="form-select form-select-sm">
          <option value="">Todas las categorías</option>
          <option value="SOFTWARE" <?= ($_GET['tipo'] ?? '') === 'SOFTWARE' ? 'selected' : '' ?>>Software</option>
          <option value="RED" <?= ($_GET['tipo'] ?? '') === 'RED' ? 'selected' : '' ?>>Redes / VPN</option>
          <option value="HARDWARE" <?= ($_GET['tipo'] ?? '') === 'HARDWARE' ? 'selected' : '' ?>>Hardware</option>
          <option value="SEGURIDAD" <?= ($_GET['tipo'] ?? '') === 'SEGURIDAD' ? 'selected' : '' ?>>Seguridad</option>
          <option value="CLOUD_SERVIDORES" <?= ($_GET['tipo'] ?? '') === 'CLOUD_SERVIDORES' ? 'selected' : '' ?>>Cloud / Servidores</option>
          <option value="BASE_DE_DATOS" <?= ($_GET['tipo'] ?? '') === 'BASE_DE_DATOS' ? 'selected' : '' ?>>Base de Datos</option>
        </select>
      </div>

      <div class="col-md-2">
        <select name="prioridad" class="form-select form-select-sm">
          <option value="">Todas las prioridades</option>
          <option value="critica" <?= ($_GET['prioridad'] ?? '') === 'critica' ? 'selected' : '' ?>>Crítica</option>
          <option value="alta" <?= ($_GET['prioridad'] ?? '') === 'alta' ? 'selected' : '' ?>>Alta</option>
          <option value="media" <?= ($_GET['prioridad'] ?? '') === 'media' ? 'selected' : '' ?>>Media</option>
          <option value="baja" <?= ($_GET['prioridad'] ?? '') === 'baja' ? 'selected' : '' ?>>Baja</option>
        </select>
      </div>

      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill"><i class="bi bi-search me-1"></i> Filtrar</button>
        <a href="index.php?route=dashboard" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="bi bi-arrow-counterclockwise"></i></a>
      </div>
    </form>
  </div>

  <!-- Tabla de Tickets -->
  <div class="card shadow-sm rounded-4 border-0 overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-dark">
          <tr>
            <th class="ps-3">#ID</th>
            <th>Fecha</th>
            <th>Solicitante</th>
            <th>Asunto</th>
            <th>Categoría</th>
            <th>Prioridad</th>
            <th>Estado</th>
            <th class="text-center">Copiloto IA</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($tickets)): ?>
            <?php foreach ($tickets as $row): ?>
              <tr>
                <td class="ps-3 fw-bold text-muted">#<?= $row['id'] ?></td>
                <td class="small text-nowrap"><?= date('d/m/Y H:i', strtotime($row['fecha_creacion'])) ?></td>
                <td>
                  <div class="fw-semibold text-dark"><?= htmlspecialchars($row['nombre']) ?></div>
                  <div class="small text-muted"><?= htmlspecialchars($row['email']) ?></div>
                </td>
                <td>
                  <div class="fw-semibold text-truncate" style="max-width: 260px;" title="<?= htmlspecialchars($row['asunto']) ?>">
                    <?= htmlspecialchars($row['asunto']) ?>
                  </div>
                  <div class="small text-muted text-truncate" style="max-width: 260px;" title="<?= htmlspecialchars($row['mensaje']) ?>">
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
            <?php endforeach; ?>
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

<!-- Modal de Diagnóstico IA -->
<?php require_once __DIR__ . '/partials/modal_ia.php'; ?>

<!-- Datos JSON para Chart.js -->
<script>
  window.CHART_MESES_LABELS = <?= json_encode($metrics['tendenciaMeses']['labels']) ?>;
  window.CHART_MESES_DATA = <?= json_encode($metrics['tendenciaMeses']['data']) ?>;
  window.CHART_TIPOS_LABELS = <?= json_encode(array_keys($metrics['tipos'])) ?>;
  window.CHART_TIPOS_DATA = <?= json_encode(array_column($metrics['tipos'], 'cantidad')) ?>;
</script>

<script src="public/js/dashboard.js"></script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
