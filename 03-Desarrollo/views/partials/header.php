<?php
require_once __DIR__ . '/../../src/Controllers/AuthController.php';
$isAuth = AuthController::isAuthenticated();
$currentUser = AuthController::getUser();
$isAdmin = AuthController::isAdminOrTecnico();
?>
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
            <i class="bi bi-plus-circle me-1"></i> Radicar Solicitud
          </a>
        </li>
        
        <?php if ($isAuth): ?>
          <?php if ($isAdmin): ?>
            <li class="nav-item">
              <a class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active fw-bold text-white' : '' ?> px-3 py-2 rounded-pill" href="index.php?route=dashboard">
                <i class="bi bi-speedometer2 me-1"></i> Panel Directivo & IA
              </a>
            </li>
          <?php endif; ?>
          <li class="nav-item ms-lg-2">
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-secondary bg-opacity-50 text-light border border-secondary px-3 py-2 rounded-pill small" title="Empresa: <?= htmlspecialchars($currentUser['empresa'] ?? '') ?> - Cargo: <?= htmlspecialchars($currentUser['cargo_empresa'] ?? '') ?>">
                <i class="bi bi-person-circle <?= $isAdmin ? 'text-warning' : 'text-info' ?> me-1"></i> 
                <?= htmlspecialchars($currentUser['nombre']) ?> 
                <span class="text-white-50 ms-1">(<?= htmlspecialchars($currentUser['empresa'] ?: $currentUser['rol']) ?>)</span>
              </span>
              <a href="index.php?action=logout" class="btn btn-outline-danger btn-sm rounded-pill px-3" title="Cerrar sesión">
                <i class="bi bi-box-arrow-right me-1"></i> Salir
              </a>
            </div>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link px-3 py-2 rounded-pill" href="index.php?route=registro">
              <i class="bi bi-person-plus me-1"></i> Registro
            </a>
          </li>
          <li class="nav-item ms-lg-2">
            <a class="btn btn-primary btn-sm px-3 py-2 rounded-pill shadow-sm fw-semibold" href="index.php?route=login">
              <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
