<?php
$pageTitle = "Radicar Solicitud - TechCare Soporte TI";
$activePage = "formulario";
require_once __DIR__ . '/partials/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
      
      <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <!-- Cabecera del Formulario -->
        <div class="bg-primary bg-gradient text-white p-4 p-md-5 text-center position-relative">
          <div class="badge bg-white bg-opacity-20 text-white px-3 py-1 rounded-pill mb-2 small fw-semibold">
            <i class="bi bi-headset me-1"></i> Mesa de Ayuda TI
          </div>
          <h2 class="fw-bold mb-2">Reportar Incidente Técnico</h2>
          <p class="mb-0 opacity-90 small">Nuestro equipo y el copiloto de IA diagnosticarán su problema para una pronta solución.</p>
        </div>

        <div class="card-body p-4 p-md-5">
          <!-- Alerta de Notificación -->
          <div id="alertaRespuesta" class="alert d-none rounded-3 py-3 px-4 mb-4" role="alert"></div>

          <form id="formTicket" novalidate>
            <div class="row g-3">
              
              <!-- Nombre Completo -->
              <div class="col-md-6">
                <label for="nombre" class="form-label fw-semibold small text-muted">Nombre Completo <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                  <input type="text" class="form-control border-start-0" id="nombre" name="nombre" placeholder="Ej. Carlos Mendoza" required>
                </div>
              </div>

              <!-- Correo Corporativo -->
              <div class="col-md-6">
                <label for="email" class="form-label fw-semibold small text-muted">Correo Corporativo <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                  <input type="email" class="form-control border-start-0" id="email" name="email" placeholder="usuario@empresa.com" required>
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
              <div class="col-md-6">
                <label for="prioridad" class="form-label fw-semibold small text-muted">Nivel de Criticidad <span class="text-danger">*</span></label>
                <select class="form-select" id="prioridad" name="prioridad" required>
                  <option value="baja">🟢 Baja (No interrumpe el trabajo)</option>
                  <option value="media" selected>🟡 Media (Problema individual manejable)</option>
                  <option value="alta">🟠 Alta (Afecta operaciones clave)</option>
                  <option value="critica">🔴 Crítica (Sistema o sucursal caída)</option>
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
  </div>
</div>

<script src="public/js/form.js"></script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
