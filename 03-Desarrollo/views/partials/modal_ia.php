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
