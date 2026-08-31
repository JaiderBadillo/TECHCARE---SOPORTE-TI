<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Usuario - TechCare Soporte TI</title>
  
  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5.3 & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <!-- Estilos Personalizados -->
  <link rel="stylesheet" href="public/css/style.css">
  
  <style>
    body {
      background: radial-gradient(circle at 50% 0%, #1e1b4b 0%, #0f172a 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
    }
    .register-card {
      background: rgba(30, 41, 59, 0.85);
      border: 1px solid rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(16px);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      border-radius: 1.5rem;
    }
  </style>
</head>
<body>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-7 col-xl-6">
      
      <div class="card register-card text-white p-4 p-md-5">
        
        <!-- Header / Logo -->
        <div class="text-center mb-4">
          <div class="bg-primary text-white rounded-4 p-3 d-inline-flex align-items-center justify-content-center shadow mb-3" style="width: 58px; height: 58px;">
            <i class="bi bi-person-plus-fill fs-2"></i>
          </div>
          <h4 class="fw-bold text-white mb-1">Crear Cuenta de Usuario</h4>
          <p class="text-white-50 small mb-0">Regístrese para reportar y consultar el estado de sus incidentes de TI</p>
        </div>

        <!-- Alerta de Respuesta -->
        <div id="registroAlerta" class="alert d-none rounded-3 py-2 px-3 small mb-3" role="alert"></div>

        <!-- Formulario de Registro -->
        <form id="formRegistro" novalidate>
          
          <div class="row g-3">
            
            <!-- Nombre Completo -->
            <div class="col-md-6">
              <label for="regNombre" class="form-label small text-light opacity-75 fw-semibold">Nombre Completo <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white-50"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white" id="regNombre" name="nombre" placeholder="Ej. Carlos Mendoza" required>
              </div>
            </div>

            <!-- Correo Corporativo -->
            <div class="col-md-6">
              <label for="regEmail" class="form-label small text-light opacity-75 fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white-50"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white" id="regEmail" name="email" placeholder="usuario@empresa.com" required>
              </div>
            </div>

            <!-- Empresa / Organización -->
            <div class="col-md-6">
              <label for="regEmpresa" class="form-label small text-light opacity-75 fw-semibold">Empresa / Organización <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white-50"><i class="bi bi-building"></i></span>
                <input type="text" class="form-control bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white" id="regEmpresa" name="empresa" placeholder="Ej. Logística Global S.A.S" required>
              </div>
            </div>

            <!-- Cargo o Rol en la Empresa -->
            <div class="col-md-6">
              <label for="regCargo" class="form-label small text-light opacity-75 fw-semibold">Cargo / Rol en la Empresa <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white-50"><i class="bi bi-briefcase"></i></span>
                <input type="text" class="form-control bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white" id="regCargo" name="cargo_empresa" placeholder="Ej. Líder de Contabilidad" required>
              </div>
            </div>

            <!-- Contraseña -->
            <div class="col-md-6">
              <label for="regPassword" class="form-label small text-light opacity-75 fw-semibold">Contraseña <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white-50"><i class="bi bi-key"></i></span>
                <input type="password" class="form-control bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white" id="regPassword" name="password" placeholder="Mínimo 6 caracteres" required>
              </div>
            </div>

            <!-- Confirmar Contraseña -->
            <div class="col-md-6">
              <label for="regPasswordConfirm" class="form-label small text-light opacity-75 fw-semibold">Confirmar Contraseña <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white-50"><i class="bi bi-shield-check"></i></span>
                <input type="password" class="form-control bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white" id="regPasswordConfirm" name="password_confirm" placeholder="Repita la contraseña" required>
              </div>
            </div>

            <!-- Botón Crear Cuenta -->
            <div class="col-12 mt-4">
              <button type="submit" id="btnRegistro" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <span>Registrarme y Acceder al Portal</span>
              </button>
            </div>

          </div>

        </form>

        <!-- ¿Ya tienes cuenta? -->
        <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-25">
          <span class="text-white-50 small">¿Ya tienes una cuenta registrada? </span>
          <a href="index.php?route=login" class="text-info text-decoration-none small fw-semibold">
            Inicia Sesión aquí
          </a>
        </div>

      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formRegistro');
  const btnRegistro = document.getElementById('btnRegistro');
  const alerta = document.getElementById('registroAlerta');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const nombre = document.getElementById('regNombre').value.trim();
    const email = document.getElementById('regEmail').value.trim();
    const empresa = document.getElementById('regEmpresa').value.trim();
    const cargo = document.getElementById('regCargo').value.trim();
    const password = document.getElementById('regPassword').value.trim();
    const confirm = document.getElementById('regPasswordConfirm').value.trim();

    if (!nombre || !email || !empresa || !cargo || !password) {
      mostrarAlerta('Por favor complete todos los campos obligatorios.', 'danger');
      return;
    }

    if (password.length < 6) {
      mostrarAlerta('La contraseña debe tener al menos 6 caracteres.', 'danger');
      return;
    }

    if (password !== confirm) {
      mostrarAlerta('Las contraseñas no coinciden. Verifíquelas.', 'danger');
      return;
    }

    btnRegistro.disabled = true;
    btnRegistro.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Registrando cuenta...';

    const formData = new FormData(form);

    try {
      const res = await fetch('index.php?action=registro', {
        method: 'POST',
        body: formData
      });
      const json = await res.json();

      if (json.ok) {
        mostrarAlerta(json.mensaje, 'success');
        setTimeout(() => {
          window.location.href = json.redirect || 'index.php?route=formulario';
        }, 1000);
      } else {
        mostrarAlerta(json.error || 'Ocurrió un error al registrar la cuenta.', 'danger');
        btnRegistro.disabled = false;
        btnRegistro.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i><span>Registrarme y Acceder al Portal</span>';
      }
    } catch (err) {
      mostrarAlerta('Error de comunicación con el servidor.', 'danger');
      btnRegistro.disabled = false;
      btnRegistro.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i><span>Registrarme y Acceder al Portal</span>';
    }
  });

  function mostrarAlerta(msg, tipo) {
    alerta.className = `alert alert-${tipo} rounded-3 py-2 px-3 small mb-3 border-0`;
    alerta.textContent = msg;
    alerta.classList.remove('d-none');
    alerta.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
});
</script>

</body>
</html>
