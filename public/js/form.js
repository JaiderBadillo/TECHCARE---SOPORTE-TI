/**
 * Formulario de Radicación de Tickets
 * Validación y Envío Asíncrono
 */

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formTicket');
  const btnEnviar = document.getElementById('btnEnviar');
  const alerta = document.getElementById('alertaRespuesta');

  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const nombre = document.getElementById('nombre').value.trim();
    const email = document.getElementById('email').value.trim();
    const asunto = document.getElementById('asunto').value.trim();
    const mensaje = document.getElementById('mensaje').value.trim();

    if (!nombre || !email || !asunto || !mensaje) {
      mostrarAlerta('Por favor complete todos los campos obligatorios.', 'danger');
      return;
    }

    // Deshabilitar botón durante el envío
    btnEnviar.disabled = true;
    btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Registrando solicitud...';

    const formData = new FormData(form);

    try {
      const res = await fetch('index.php?action=ticket_guardar', {
        method: 'POST',
        body: formData
      });

      const json = await res.json();

      if (json.ok) {
        mostrarAlerta(`¡Éxito! Solicitud <strong>#${json.id}</strong> registrada correctamente. Redirigiendo al panel...`, 'success');
        form.reset();
        setTimeout(() => {
          window.location.href = 'index.php?route=dashboard';
        }, 1500);
      } else {
        mostrarAlerta(json.error || 'Ocurrió un error al registrar la solicitud.', 'danger');
      }
    } catch (err) {
      mostrarAlerta('Error de conexión con el servidor. Verifique que el servicio esté activo.', 'danger');
    } finally {
      btnEnviar.disabled = false;
      btnEnviar.innerHTML = '<i class="bi bi-send-fill me-2"></i><span>Enviar Solicitud de Soporte</span>';
    }
  });

  function mostrarAlerta(msg, tipo) {
    alerta.className = `alert alert-${tipo} rounded-3 py-3 px-4 mb-4`;
    alerta.innerHTML = msg;
    alerta.classList.remove('d-none');
    alerta.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
});
