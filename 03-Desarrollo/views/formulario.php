<?php
$pageTitle = "Radicar Solicitud - TechCare Soporte TI";
$activePage = "formulario";
require_once __DIR__ . '/partials/header.php';

// Si el usuario está autenticado, obtener sus tickets previos
$userTickets = [];
if ($isAuth && $currentUser) {
    $userTickets = Ticket::getByUser($currentUser['id'], $currentUser['email']);
}

function getEstadoBadgeCliente($estado) {
    switch ($estado) {
        case 'pendiente': return '<span class="badge bg-danger text-white rounded-pill px-2 py-1"><i class="bi bi-clock me-1"></i>Pendiente</span>';
        case 'en_proceso': return '<span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="bi bi-gear-wide-connected me-1"></i>En Revisión</span>';
        case 'resuelto': return '<span class="badge bg-success text-white rounded-pill px-2 py-1"><i class="bi bi-check-circle me-1"></i>Resuelto</span>';
        default: return '<span class="badge bg-secondary">' . htmlspecialchars($estado) . '</span>';
    }
}
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
      
      <!-- Pestañas de Navegación del Usuario -->
      <ul class="nav nav-pills nav-fill bg-white p-2 rounded-4 shadow-sm mb-4 border" id="pills-userTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active rounded-pill fw-bold py-2" id="tab-radicar-tab" data-bs-toggle="pill" data-bs-target="#tab-radicar" type="button" role="tab">
            <i class="bi bi-pencil-square me-1"></i> 1. Radicar Nueva Solicitud
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link rounded-pill fw-bold py-2" id="tab-historial-tab" data-bs-toggle="pill" data-bs-target="#tab-historial" type="button" role="tab">
            <i class="bi bi-clock-history me-1"></i> 2. Mis Solicitudes Registradas (<?= count($userTickets) ?>)
          </button>
        </li>
      </ul>

      <div class="tab-content" id="pills-userContent">
        
        <!-- PESTAÑA 1: FORMULARIO DE RADICACIÓN -->
        <div class="tab-pane fade show active" id="tab-radicar" role="tabpanel">
          <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Cabecera del Formulario -->
            <div class="bg-primary bg-gradient text-white p-4 p-md-5 text-center position-relative">
              <div class="badge bg-white bg-opacity-20 text-white px-3 py-1 rounded-pill mb-2 small fw-semibold">
                <i class="bi bi-headset me-1"></i> Mesa de Ayuda TI
              </div>
              <h2 class="fw-bold mb-2">Reportar Incidente Técnico</h2>
              <p class="mb-0 opacity-90 small">Nuestro equipo de soporte y el copiloto de IA diagnosticarán su problema para una pronta solución.</p>
              
              <?php if ($isAuth): ?>
                <div class="mt-3 badge bg-dark bg-opacity-30 border border-white border-opacity-20 text-light py-2 px-3 rounded-pill">
                  <i class="bi bi-building me-1 text-warning"></i> <strong>Empresa:</strong> <?= htmlspecialchars($currentUser['empresa'] ?: 'Sin empresa') ?> &nbsp;|&nbsp; 
                  <i class="bi bi-person-badge me-1 text-info"></i> <strong>Cargo:</strong> <?= htmlspecialchars($currentUser['cargo_empresa'] ?: 'Colaborador') ?>
                </div>
              <?php endif; ?>
            </div>

            <div class="card-body p-4 p-md-5">
              <!-- Alerta de Invitación a Registrarse (Si no está autenticado) -->
              <?php if (!$isAuth): ?>
                <div class="alert alert-info border-0 rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                  <div class="small">
                    <i class="bi bi-info-circle-fill me-1 fs-5 align-middle"></i>
                    <strong>¿Desea hacer seguimiento a sus tickets?</strong> Inicie sesión o regístrese para ver el historial y diagnósticos en tiempo real.
                  </div>
                  <div class="d-flex gap-2">
                    <a href="index.php?route=login" class="btn btn-sm btn-outline-primary rounded-pill px-3">Iniciar Sesión</a>
                    <a href="index.php?route=registro" class="btn btn-sm btn-primary rounded-pill px-3">Registrarme</a>
                  </div>
                </div>
              <?php endif; ?>

              <!-- Alerta de Notificación -->
              <div id="alertaRespuesta" class="alert d-none rounded-3 py-3 px-4 mb-4" role="alert"></div>

              <form id="formTicket" novalidate>
                <div class="row g-3">
                  
                  <!-- Nombre Completo -->
                  <div class="col-md-6">
                    <label for="nombre" class="form-label fw-semibold small text-muted">Nombre Completo <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                      <input type="text" class="form-control border-start-0" id="nombre" name="nombre" value="<?= htmlspecialchars($currentUser['nombre'] ?? '') ?>" placeholder="Ej. Carlos Mendoza" required <?= $isAuth ? 'readonly' : '' ?>>
                    </div>
                  </div>

                  <!-- Correo Corporativo -->
                  <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold small text-muted">Correo Corporativo <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                      <input type="email" class="form-control border-start-0" id="email" name="email" value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>" placeholder="usuario@empresa.com" required <?= $isAuth ? 'readonly' : '' ?>>
                    </div>
                  </div>

                  <!-- Empresa / Organización -->
                  <div class="col-md-6">
                    <label for="empresa" class="form-label fw-semibold small text-muted">Empresa / Organización <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text bg-light border-end-0"><i class="bi bi-building text-muted"></i></span>
                      <input type="text" class="form-control border-start-0" id="empresa" name="empresa" value="<?= htmlspecialchars($currentUser['empresa'] ?? '') ?>" placeholder="Ej. Logística Global S.A.S" required>
                    </div>
                  </div>

                  <!-- Categoría del Problema -->
                  <div class="col-md-6">
                    <label for="tipo_problema" class="form-label fw-semibold small text-muted">Categoría del Problema <span class="text-danger">*</span></label>
                    <select class="form-select" id="tipo_problema" name="tipo_problema" required>
                      <option value="SOFTWARE" selected>💻 Software & Aplicaciones (Office, ERP)</option>
                      <option value="RED">🌐 Redes, WiFi, VPN & Conectividad</option>
                      <option value="HARDWARE">🖥️ Hardware, Equipos & Periféricos</option>
                      <option value="SEGURIDAD">🔒 Seguridad, Cuentas & Phishing</option>
                      <option value="CLOUD_SERVIDORES">☁️ Cloud, Servidores & DevOps</option>
                      <option value="BASE_DE_DATOS">🗄️ Bases de Datos & Transacciones</option>
                    </select>
                  </div>

                  <!-- Nivel de Prioridad -->
                  <div class="col-12">
                    <label for="prioridad" class="form-label fw-semibold small text-muted">Nivel de Criticidad <span class="text-danger">*</span></label>
                    <select class="form-select" id="prioridad" name="prioridad" required>
                      <option value="baja">🟢 Baja (No interrumpe el trabajo inmediato)</option>
                      <option value="media" selected>🟡 Media (Problema individual manejable)</option>
                      <option value="alta">🟠 Alta (Afecta operaciones clave del área)</option>
                      <option value="critica">🔴 Crítica (Sistema detenido o impacto general)</option>
                    </select>
                  </div>

                  <!-- Asunto / Título -->
                  <div class="col-12">
                    <label for="asunto" class="form-label fw-semibold small text-muted">Asunto del Problema <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="asunto" name="asunto" placeholder="Ej. Aviso de vencimiento de licencia Microsoft 365" required>
                  </div>

                  <!-- Descripción Detallada -->
                  <div class="col-12">
                    <label for="mensaje" class="form-label fw-semibold small text-muted">Descripción Detallada del Incidente <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="mensaje" name="mensaje" rows="4" placeholder="Describa los síntomas, mensajes de error en pantalla o pasos que provocaron la falla..." required></textarea>
                  </div>

                  <!-- Botón Enviar -->
                  <div class="col-12 mt-4">
                    <button type="submit" id="btnEnviar" class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                      <i class="bi bi-send-fill"></i>
                      <span>Enviar Solicitud de Soporte</span>
                    </button>
                  </div>

                </div>
              </form>

            </div>
          </div>
        </div>

        <!-- PESTAÑA 2: HISTORIAL DE MIS SOLICITUDES -->
        <div class="tab-pane fade" id="tab-historial" role="tabpanel">
          <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-header bg-dark text-white p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-folder-check text-info me-2"></i>Historial de Solicitudes</h5>
                <span class="small text-white-50">Consulte el progreso y la retroalimentación técnica de sus reportes</span>
              </div>
              <button onclick="location.reload()" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
              </button>
            </div>

            <div class="card-body p-4">
              <?php if (!$isAuth): ?>
                <div class="text-center py-5">
                  <i class="bi bi-shield-lock text-muted fs-1 d-block mb-3"></i>
                  <h5 class="fw-bold">Inicie sesión para ver sus solicitudes</h5>
                  <p class="text-muted small mb-3">Para asociar sus reportes y consultar el historial debe autenticarse en su cuenta.</p>
                  <div class="d-flex justify-content-center gap-2">
                    <a href="index.php?route=login" class="btn btn-primary rounded-pill px-4">Iniciar Sesión</a>
                    <a href="index.php?route=registro" class="btn btn-outline-secondary rounded-pill px-4">Crear Cuenta</a>
                  </div>
                </div>
              <?php elseif (!empty($userTickets)): ?>
                <div class="d-flex flex-column gap-3">
                  <?php foreach ($userTickets as $t): ?>
                    <div class="card border border-secondary border-opacity-25 rounded-4 p-3 shadow-sm bg-light">
                      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                        <div>
                          <span class="badge bg-dark text-white rounded-pill px-2 py-1 me-1">Ticket #<?= $t['id'] ?></span>
                          <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-1"><?= htmlspecialchars($t['tipo_problema']) ?></span>
                          <span class="text-muted small ms-2"><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y H:i', strtotime($t['fecha_creacion'])) ?></span>
                        </div>
                        <div><?= getEstadoBadgeCliente($t['estado']) ?></div>
                      </div>

                      <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($t['asunto']) ?></h6>
                      <p class="small text-muted mb-2"><?= htmlspecialchars($t['mensaje']) ?></p>

                      <?php if (!empty($t['solucion_ia'])): ?>
                        <?php $solData = json_decode($t['solucion_ia'], true); ?>
                        <?php if ($solData): ?>
                          <div class="alert alert-success border-0 rounded-3 p-3 mb-0 small">
                            <strong class="text-success d-block mb-1"><i class="bi bi-robot me-1"></i> Diagnóstico Preliminar de IA:</strong>
                            <p class="mb-2 text-dark"><?= htmlspecialchars($solData['diagnostico'] ?? '') ?></p>
                            <?php if (!empty($solData['pasos_resolucion'])): ?>
                              <strong>Pasos Sugeridos:</strong>
                              <ul class="mb-0 ps-3 mt-1 text-muted">
                                <?php foreach (array_slice($solData['pasos_resolucion'], 0, 3) as $p): ?>
                                  <li><?= htmlspecialchars($p) ?></li>
                                <?php endforeach; ?>
                              </ul>
                            <?php endif; ?>
                          </div>
                        <?php endif; ?>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="text-center py-5 text-muted">
                  <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                  No tiene solicitudes registradas aún. Utilice la pestaña anterior para radicar su primer ticket.
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>

<script src="public/js/form.js"></script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
