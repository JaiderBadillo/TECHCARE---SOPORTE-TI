/**
 * Dashboard & IA Hub Client Scripts
 * TechCare Soporte TI
 */

document.addEventListener('DOMContentLoaded', () => {

  // 1. Inicializar Gráficos con Chart.js
  const ctxMes = document.getElementById('chartMes');
  if (ctxMes && window.CHART_MESES_LABELS) {
    new Chart(ctxMes, {
      type: 'bar',
      data: {
        labels: window.CHART_MESES_LABELS,
        datasets: [{
          label: 'Solicitudes',
          data: window.CHART_MESES_DATA,
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
  if (ctxTipos && window.CHART_TIPOS_LABELS) {
    new Chart(ctxTipos, {
      type: 'doughnut',
      data: {
        labels: window.CHART_TIPOS_LABELS,
        datasets: [{
          data: window.CHART_TIPOS_DATA,
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

  // 2. Cambio en vivo de estados de tickets (AJAX)
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
        const res = await fetch('index.php?action=ticket_estado', { method: 'POST', body: data });
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

  // 3. Hub Estratégico IA: Carga de Decisiones
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
      const res = await fetch('index.php?action=ia_analisis&motor=' + encodeURIComponent(motor));
      const json = await res.json();

      if (!json.ok) {
        document.getElementById('iaResumenEjecutivo').textContent = 'Error al cargar análisis de IA: ' + json.error;
        return;
      }

      const ins = json.insights;
      document.getElementById('iaResumenEjecutivo').textContent = ins.resumen_ejecutivo || '';

      // Badge del motor IA
      const hubMotorBadge = document.getElementById('iaHubMotorBadge');
      if (hubMotorBadge && ins.motor_ia) {
        const isGemini = ins.motor_ia.toLowerCase().includes('gemini');
        hubMotorBadge.className = 'badge rounded-pill ' + (isGemini ? 'bg-success text-white' : 'bg-secondary text-white');
        hubMotorBadge.innerHTML = (isGemini ? '<i class="bi bi-stars me-1"></i>' : '<i class="bi bi-cpu me-1"></i>') + ins.motor_ia;
      }

      // Contratación
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

      // Infraestructura
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

      // Capacitación
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

      // Automatización
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

  // 4. Modal Copiloto IA por Ticket
  let currentTicketId = 0;
  let modalIAInstance = null;

  function abrirModalSolucion(ticketId, ticketAsunto) {
    currentTicketId = ticketId;
    document.getElementById('modalIASubtitle').textContent = 'Ticket #' + currentTicketId + ': ' + ticketAsunto;
    
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

  function abrirModalSolucion(ticketId, ticketAsunto) {
    currentTicketId = ticketId;
    document.getElementById('modalIASubtitle').textContent = 'Ticket #' + currentTicketId + ': ' + ticketAsunto;
    
    document.getElementById('iaModalToast').classList.add('d-none');
    const warningBox = document.getElementById('iaModalWarning');
    if (warningBox) warningBox.classList.add('d-none');

    const modalEl = document.getElementById('modalSolucionIA');
    if (!modalIAInstance) {
      modalIAInstance = new bootstrap.Modal(modalEl);
    }
    modalIAInstance.show();
    // Por defecto usa el motor local rápido (0 tokens). Si el usuario quiere Gemini, pulsa el botón Gemini Cloud.
    obtenerSolucionTicket(currentTicketId, false, 'local');
  }

  document.getElementById('btnRegenerarGemini')?.addEventListener('click', () => {
    if (currentTicketId > 0) obtenerSolucionTicket(currentTicketId, true, 'gemini');
  });

  document.getElementById('btnRegenerarLocal')?.addEventListener('click', () => {
    if (currentTicketId > 0) obtenerSolucionTicket(currentTicketId, true, 'local');
  });

  async function obtenerSolucionTicket(id, force, motor = 'local') {
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
      loaderText.textContent = 'Generando diagnóstico instantáneo con el Motor Experto Local (0 Tokens)...';
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

      const res = await fetch('index.php?action=ia_solucion', { method: 'POST', body: data });
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

  // Pestañas Hub IA
  document.querySelectorAll('.decision-pill-tab').forEach(tabBtn => {
    tabBtn.addEventListener('click', function(e) {
      e.preventDefault();
      document.querySelectorAll('.decision-pill-tab').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      const targetSelector = this.getAttribute('data-bs-target');
      document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('show', 'active'));
      const targetPane = document.querySelector(targetSelector);
      if (targetPane) targetPane.classList.add('show', 'active');
    });
  });

  document.getElementById('btnEjecutarAnalisisGemini')?.addEventListener('click', () => cargarDecisionesIA('gemini'));
  document.getElementById('btnEjecutarAnalisisLocal')?.addEventListener('click', () => cargarDecisionesIA('local'));
  
  // Cargar análisis inicial en MODO LOCAL (Ultra-Rápido, 0 Tokens)
  cargarDecisionesIA('local');
});
