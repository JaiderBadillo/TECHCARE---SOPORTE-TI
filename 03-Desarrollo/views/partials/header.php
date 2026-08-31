<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'TechCare Soporte TI') ?></title>
  
  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5.3 & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <!-- Estilos Personalizados Glassmorphism -->
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<!-- Barra de Navegación Principal -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm py-3 border-bottom border-secondary border-opacity-25">
  <div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-white fs-5" href="index.php">
      <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
        <i class="bi bi-shield-check fs-5"></i>
      </div>
      <span>TechCare <span class="badge bg-primary bg-opacity-25 text-primary-emphasis border border-primary border-opacity-25 ms-1 fw-semibold small">Soporte TI</span></span>
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-center gap-2">
        <li class="nav-item">
          <a class="nav-link <?= ($activePage ?? '') === 'formulario' ? 'active fw-bold text-white' : '' ?> px-3 py-2 rounded-pill" href="index.php?route=formulario">
            <i class="bi bi-plus-circle me-1"></i> Radicar Ticket
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active fw-bold text-white' : '' ?> px-3 py-2 rounded-pill" href="index.php?route=dashboard">
            <i class="bi bi-speedometer2 me-1"></i> Panel Directivo & IA
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
