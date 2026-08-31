<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - TechCare Soporte TI</title>
  
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
    .login-card {
      background: rgba(30, 41, 59, 0.85);
      border: 1px solid rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(16px);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      border-radius: 1.5rem;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-5 col-xl-4">
      
      <div class="card login-card text-white p-4 p-md-5">
        
        <!-- Header / Logo -->
        <div class="text-center mb-4">
          <div class="bg-primary text-white rounded-4 p-3 d-inline-flex align-items-center justify-content-center shadow mb-3" style="width: 58px; height: 58px;">
            <i class="bi bi-shield-lock-fill fs-2"></i>
          </div>
          <h4 class="fw-bold text-white mb-1">Iniciar Sesión</h4>
          <p class="text-white-50 small mb-0">TechCare Mesa de Ayuda & Diagnóstico TI</p>
        </div>

        <!-- Alerta de Respuesta -->
        <div id="loginAlerta" class="alert d-none rounded-3 py-2 px-3 small mb-3" role="alert"></div>

        <!-- Formulario -->
        <form id="formLogin" novalidate>
          
          <!-- Correo -->
          <div class="mb-3">
            <label for="loginEmail" class="form-label small text-light opacity-75 fw-semibold">Correo Electrónico</label>
            <div class="input-group">
              <span class="input-group-text bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white-50"><i class="bi bi-envelope"></i></span>
              <input type="email" class="form-control bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white" id="loginEmail" name="email" value="cliente@empresa.com" placeholder="usuario@empresa.com" required>
            </div>
          </div>

          <!-- Contraseña -->
          <div class="mb-3">
            <label for="loginPassword" class="form-label small text-light opacity-75 fw-semibold">Contraseña</label>
            <div class="input-group">
              <span class="input-group-text bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white-50"><i class="bi bi-key"></i></span>
              <input type="password" class="form-control bg-dark bg-opacity-50 border-secondary border-opacity-50 text-white" id="loginPassword" name="password" value="cliente123" placeholder="••••••••" required>
              <button class="btn btn-outline-secondary border-secondary border-opacity-50 text-white-50" type="button" id="btnTogglePassword">
                <i class="bi bi-eye" id="toggleIcon"></i>
              </button>
            </div>
          </div>

          <!-- Credenciales Demo Sugeridas -->
          <div class="card bg-dark bg-opacity-50 border border-secondary border-opacity-25 rounded-3 p-2 mb-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <span class="small text-white-50"><i class="bi bi-magic text-warning me-1"></i> Accesos Demo Rápidos:</span>
            </div>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-info btn-sm flex-fill py-1 small" style="font-size: 0.75rem;" id="btnDemoCliente">
                <i class="bi bi-person me-1"></i>Usuario Cliente
              </button>
              <button type="button" class="btn btn-outline-warning btn-sm flex-fill py-1 small" style="font-size: 0.75rem;" id="btnDemoAdmin">
                <i class="bi bi-shield-check me-1"></i>Admin TI
              </button>
            </div>
          </div>

          <!-- Botón Ingresar -->
          <button type="submit" id="btnLogin" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-box-arrow-in-right"></i>
            <span>Ingresar al Sistema</span>
          </button>
        </form>

        <!-- Registro de nuevo usuario -->
        <div class="text-center mt-4 pt-3 border-top border-secondary border-opacity-25">
          <span class="text-white-50 small">¿No tienes una cuenta de usuario? </span>
          <a href="index.php?route=registro" class="text-info text-decoration-none small fw-semibold d-block mt-1">
            <i class="bi bi-person-plus me-1"></i> Regístrate gratis aquí
          </a>
        </div>

      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formLogin');
  const btnLogin = document.getElementById('btnLogin');
  const alerta = document.getElementById('loginAlerta');
  const btnToggle = document.getElementById('btnTogglePassword');
  const passInput = document.getElementById('loginPassword');
  const emailInput = document.getElementById('loginEmail');
  const toggleIcon = document.getElementById('toggleIcon');

  btnToggle.addEventListener('click', () => {
    if (passInput.type === 'password') {
      passInput.type = 'text';
      toggleIcon.className = 'bi bi-eye-slash';
    } else {
      passInput.type = 'password';
      toggleIcon.className = 'bi bi-eye';
    }
  });

  document.getElementById('btnDemoCliente')?.addEventListener('click', () => {
    emailInput.value = 'cliente@empresa.com';
    passInput.value = 'cliente123';
  });

  document.getElementById('btnDemoAdmin')?.addEventListener('click', () => {
    emailInput.value = 'admin@techcare.com';
    passInput.value = 'admin123';
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = emailInput.value.trim();
    const password = passInput.value.trim();

    if (!email || !password) {
      mostrarAlerta('Por favor ingrese su correo y contraseña.', 'danger');
      return;
    }

    btnLogin.disabled = true;
    btnLogin.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Autenticando...';

    const formData = new FormData(form);

    try {
      const res = await fetch('index.php?action=login', {
        method: 'POST',
        body: formData
      });
      const json = await res.json();

      if (json.ok) {
        mostrarAlerta(json.mensaje, 'success');
        setTimeout(() => {
          window.location.href = json.redirect || 'index.php?route=formulario';
        }, 800);
      } else {
        mostrarAlerta(json.error || 'Credenciales incorrectas.', 'danger');
        btnLogin.disabled = false;
        btnLogin.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i><span>Ingresar al Sistema</span>';
      }
    } catch (err) {
      mostrarAlerta('Error de comunicación con el servidor.', 'danger');
      btnLogin.disabled = false;
      btnLogin.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i><span>Ingresar al Sistema</span>';
    }
  });

  function mostrarAlerta(msg, tipo) {
    alerta.className = `alert alert-${tipo} rounded-3 py-2 px-3 small mb-3 border-0`;
    alerta.textContent = msg;
    alerta.classList.remove('d-none');
  }
});
</script>

</body>
</html>
