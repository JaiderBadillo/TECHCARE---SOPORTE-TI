<?php
/**
 * Vista de Retroalimentación del Usuario y Encuesta CSAT / Devolución de Ticket
 * TechCare Soporte TI
 * Desarrollador: Jaider Augusto Niño Badillo
 */

$pageTitle = "Satisfacción y Estado del Ticket - TechCare Soporte TI";
$activePage = "feedback";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --tc-primary: #1e3a8a;
      --tc-accent: #3b82f6;
      --tc-success: #059669;
      --tc-danger: #dc2626;
      --tc-bg: #f8fafc;
    }
    body {
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      background: radial-gradient(circle at top right, #e0f2fe, #f8fafc 40%), radial-gradient(circle at bottom left, #ecfdf5, #f8fafc 40%);
      color: #1e293b;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .feedback-card {
      background: #ffffff;
      border: 1px solid rgba(226, 232, 240, 0.9);
      border-radius: 20px;
      box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
      overflow: hidden;
    }
    .star-rating {
      display: inline-flex;
      flex-direction: row-reverse;
      gap: 8px;
    }
    .star-rating input {
      display: none;
    }
    .star-rating label {
      font-size: 2.4rem;
      color: #cbd5e1;
      cursor: pointer;
      transition: color 0.15s ease, transform 0.15s ease;
    }
    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
      color: #f59e0b;
      transform: scale(1.1);
    }
    .badge-devuelto {
      background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
      color: #ffffff;
    }
    .badge-resuelto {
      background: linear-gradient(135deg, #10b981 0%, #047857 100%);
      color: #ffffff;
    }
  </style>
</head>
<body>

  <!-- Barra de Navegación Simple -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3 shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="index.php?route=formulario">
        <i class="bi bi-shield-check text-primary fs-4"></i>
        <span>TechCare <span class="badge bg-primary rounded-pill small">Soporte TI</span></span>
      </a>
      <a href="index.php?route=formulario" class="btn btn-outline-light btn-sm rounded-pill px-3">
        <i class="bi bi-ticket-detailed me-1"></i> Ir al Portal
      </a>
    </div>
  </nav>

  <!-- Contenido Principal -->
  <div class="container py-5 my-auto">
    <div class="row justify-content-center">
      <div class="col-md-9 col-lg-7">

        <div class="feedback-card p-4 p-md-5">

          <?php if ($modo === 'devuelto'): ?>
            <!-- ========================================== -->
            <!-- VISTA: TICKET DEVUELTO Y REABIERTO -->
            <!-- ========================================== -->
            <div class="text-center mb-4">
              <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 p-3" style="background: #fee2e2; width: 75px; height: 75px;">
                <i class="bi bi-arrow-counterclockwise text-danger fs-1"></i>
              </div>
              <span class="badge badge-devuelto rounded-pill px-3 py-1 mb-2 fs-6">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> Ticket Reabierto y Devuelto
              </span>
              <h3 class="fw-bold text-dark mt-2 mb-1">Tu ticket #<?= htmlspecialchars($ticket['id']) ?> ha sido devuelto</h3>
              <p class="text-muted small">Prioridad escalada automáticamente a: <strong class="text-danger text-uppercase"><?= htmlspecialchars($ticket['prioridad']) ?></strong></p>
            </div>

            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 rounded-4 p-3 mb-4">
              <div class="d-flex gap-3">
                <i class="bi bi-info-circle-fill text-danger fs-3 flex-shrink-0"></i>
                <div class="small text-danger-emphasis">
                  <strong>Lamentamos que la solución anterior no haya sido satisfactoria.</strong><br>
                  Hemos reabierto tu caso y notificado al equipo sénior de ingenieros. Tu solicitud ya tiene prioridad alta en nuestra cola de atención.
                </div>
              </div>
            </div>

            <!-- Resumen del Ticket -->
            <div class="bg-light p-3 rounded-4 mb-4 border">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold">Asunto del ticket:</span>
                <span class="badge bg-secondary rounded-pill"><?= htmlspecialchars($ticket['tipo_problema']) ?></span>
              </div>
              <div class="fw-bold text-dark"><?= htmlspecialchars($ticket['asunto']) ?></div>
            </div>

            <!-- Formulario para agregar detalle de lo que sigue fallando -->
            <form id="formDevolverDetalle" class="mb-4">
              <input type="hidden" name="action" value="ticket_feedback">
              <input type="hidden" name="tipo_accion" value="devolver">
              <input type="hidden" name="id" value="<?= htmlspecialchars($ticket['id']) ?>">
              <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

              <div class="mb-3">
                <label for="motivo_devolucion" class="form-label fw-semibold text-dark small">
                  <i class="bi bi-chat-text text-danger me-1"></i> ¿Puedes indicarnos qué sigue fallando o qué ocurrió? (Opcional pero recomendado):
                </label>
                <textarea class="form-control rounded-3" id="motivo_devolucion" name="motivo_devolucion" rows="3" placeholder="Ej: Intenté reiniciar el servicio y sigue mostrando el error 500 al autenticar..."></textarea>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-danger py-2 rounded-pill fw-semibold shadow-sm" id="btnEnviarMotivo">
                  <i class="bi bi-send-fill me-1"></i> Enviar Información al Ingeniero
                </button>
              </div>
            </form>

            <div class="text-center pt-2 border-top">
              <a href="index.php?route=formulario" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver a Mis Solicitudes
              </a>
            </div>

          <?php else: ?>
            <!-- ========================================== -->
            <!-- VISTA: ENCUESTA DE SATISFACCIÓN CSAT -->
            <!-- ========================================== -->
            <div class="text-center mb-4">
              <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 p-3" style="background: #d1fae5; width: 75px; height: 75px;">
                <i class="bi bi-stars text-success fs-1"></i>
              </div>
              <span class="badge badge-resuelto rounded-pill px-3 py-1 mb-2 fs-6">
                <i class="bi bi-check-circle-fill me-1"></i> Solicitud #<?= htmlspecialchars($ticket['id']) ?> Resuelta
              </span>
              <h3 class="fw-bold text-dark mt-2 mb-1">¡Gracias por tu confirmación!</h3>
              <p class="text-muted small">Tu opinión nos ayuda a medir y mejorar la calidad de nuestro servicio de soporte de TI.</p>
            </div>

            <!-- Resumen del caso -->
            <div class="bg-light p-3 rounded-4 mb-4 border text-start">
              <div class="small text-muted mb-1"><strong>Asunto atendido:</strong> <?= htmlspecialchars($ticket['asunto']) ?></div>
              <div class="small text-muted"><strong>Solicitante:</strong> <?= htmlspecialchars($ticket['nombre']) ?> (<?= htmlspecialchars($ticket['email']) ?>)</div>
            </div>

            <?php if (!empty($ticket['calificacion_csat'])): ?>
              <!-- Ya calificado previamente -->
              <div class="alert alert-success border-0 bg-success bg-opacity-10 rounded-4 p-4 text-center mb-4">
                <div class="text-warning fs-3 mb-2">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="bi bi-star<?= $i <= $ticket['calificacion_csat'] ? '-fill' : '' ?>"></i>
                  <?php endfor; ?>
                </div>
                <h5 class="fw-bold text-success mb-1">¡Ya has calificado esta atención!</h5>
                <p class="text-muted small mb-0">Registraste una calificación de <strong><?= (int)$ticket['calificacion_csat'] ?> / 5 estrellas</strong>. ¡Agradecemos tu retroalimentación!</p>
              </div>
            <?php else: ?>
              <!-- Formulario de Calificación CSAT -->
              <form id="formCSAT" class="text-center mb-4">
                <input type="hidden" name="action" value="ticket_feedback">
                <input type="hidden" name="tipo_accion" value="csat">
                <input type="hidden" name="id" value="<?= htmlspecialchars($ticket['id']) ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="mb-3">
                  <label class="form-label fw-bold text-dark d-block mb-2">¿Cómo calificarías la rapidez y calidad de la atención?</label>
                  
                  <div class="star-rating mb-2">
                    <input type="radio" id="star5" name="calificacion_csat" value="5" checked>
                    <label for="star5" title="¡Excelente! 5 estrellas"><i class="bi bi-star-fill"></i></label>

                    <input type="radio" id="star4" name="calificacion_csat" value="4">
                    <label for="star4" title="Buena atención - 4 estrellas"><i class="bi bi-star-fill"></i></label>

                    <input type="radio" id="star3" name="calificacion_csat" value="3">
                    <label for="star3" title="Aceptable - 3 estrellas"><i class="bi bi-star-fill"></i></label>

                    <input type="radio" id="star2" name="calificacion_csat" value="2">
                    <label for="star2" title="Insatisfactoria - 2 estrellas"><i class="bi bi-star-fill"></i></label>

                    <input type="radio" id="star1" name="calificacion_csat" value="1">
                    <label for="star1" title="Deficiente - 1 estrella"><i class="bi bi-star-fill"></i></label>
                  </div>
                  <div id="starLabel" class="small fw-semibold text-warning-emphasis">⭐⭐⭐⭐⭐ ¡Excelente atención!</div>
                </div>

                <div class="mb-4 text-start">
                  <label for="comentario_feedback" class="form-label small fw-semibold text-dark">
                    <i class="bi bi-pencil-square text-primary me-1"></i> Comentario u observaciones (Opcional):
                  </label>
                  <textarea class="form-control rounded-3" id="comentario_feedback" name="comentario_feedback" rows="3" placeholder="Cuéntanos qué te gustó o qué podemos mejorar..."></textarea>
                </div>

                <div class="d-grid">
                  <button type="submit" class="btn btn-primary py-2 rounded-pill fw-semibold shadow-sm" id="btnGuardarCSAT">
                    <i class="bi bi-check2-circle me-1"></i> Enviar Mi Calificación
                  </button>
                </div>
              </form>
            <?php endif; ?>

            <!-- Opción alternativa por si el problema vuelve a fallar -->
            <div class="text-center pt-3 border-top">
              <span class="small text-muted d-block mb-2">¿El problema no quedó solucionado del todo o volvió a presentarse?</span>
              <a href="index.php?route=feedback&id=<?= htmlspecialchars($ticket['id']) ?>&solucionado=no&token=<?= htmlspecialchars($token) ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reabrir este Ticket (Prioridad Alta)
              </a>
            </div>

          <?php endif; ?>

        </div>

      </div>
    </div>
  </div>

  <!-- Pie de página -->
  <footer class="py-3 text-center text-muted small border-top bg-white">
    <div class="container">
      TechCare Soporte TI &copy; <?= date('Y') ?> &bull; Desarrollado por <strong>Jaider Augusto Niño Badillo</strong>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Dinámica de etiquetas de estrellas
    const starLabels = {
      '5': '⭐⭐⭐⭐⭐ ¡Excelente atención!',
      '4': '⭐⭐⭐⭐ Buena atención',
      '3': '⭐⭐⭐ Aceptable',
      '2': '⭐⭐ Regular / Insatisfactoria',
      '1': '⭐ Deficiente'
    };

    document.querySelectorAll('.star-rating input').forEach(input => {
      input.addEventListener('change', (e) => {
        const lbl = document.getElementById('starLabel');
        if (lbl) lbl.textContent = starLabels[e.target.value] || '';
      });
    });

    // Envío del Formulario de Calificación CSAT
    const formCSAT = document.getElementById('formCSAT');
    if (formCSAT) {
      formCSAT.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btnGuardarCSAT');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando calificación...';

        const formData = new FormData(formCSAT);

        try {
          const resp = await fetch('index.php', {
            method: 'POST',
            body: formData
          });
          const data = await resp.json();

          if (data.ok) {
            Swal.fire({
              icon: 'success',
              title: '¡Muchas Gracias!',
              text: data.mensaje || 'Tu calificación ha sido registrada exitosamente.',
              confirmButtonColor: '#1e3a8a',
              confirmButtonText: 'Ir a Mis Solicitudes'
            }).then(() => {
              window.location.href = 'index.php?route=formulario';
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.error || 'No se pudo guardar la calificación.'
            });
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Enviar Mi Calificación';
          }
        } catch (err) {
          Swal.fire({
            icon: 'error',
            title: 'Fallo de Red',
            text: 'No se pudo conectar con el servidor.'
          });
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Enviar Mi Calificación';
        }
      });
    }

    // Envío del Formulario de Motivo de Devolución
    const formDevolver = document.getElementById('formDevolverDetalle');
    if (formDevolver) {
      formDevolver.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btnEnviarMotivo');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registrando detalle...';

        const formData = new FormData(formDevolver);

        try {
          const resp = await fetch('index.php', {
            method: 'POST',
            body: formData
          });
          const data = await resp.json();

          if (data.ok) {
            Swal.fire({
              icon: 'success',
              title: '¡Detalle Recibido!',
              text: 'Tu información ha sido adjuntada al ticket devuelto. El equipo de TI ya fue alertado.',
              confirmButtonColor: '#dc2626',
              confirmButtonText: 'Ver Estado del Ticket'
            }).then(() => {
              window.location.href = 'index.php?route=formulario';
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.error || 'No se pudo registrar la información.'
            });
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Enviar Información al Ingeniero';
          }
        } catch (err) {
          Swal.fire({
            icon: 'error',
            title: 'Fallo de Red',
            text: 'No se pudo conectar con el servidor.'
          });
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Enviar Información al Ingeniero';
        }
      });
    }
  </script>
</body>
</html>
