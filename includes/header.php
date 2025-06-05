<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Plataforma IT</title>
  <link rel="stylesheet" href="/css/estilo.css?v=<?= time() ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <?php
    $path_prefix = str_contains($_SERVER['PHP_SELF'], '/usuarios/') ||
                   str_contains($_SERVER['PHP_SELF'], '/dispositivos/') ||
                   str_contains($_SERVER['PHP_SELF'], '/servicios/') ||
                   str_contains($_SERVER['PHP_SELF'], '/incidencias/') ||
                   str_contains($_SERVER['PHP_SELF'], '/tareas/') ||
                   str_contains($_SERVER['PHP_SELF'], '/logs/') ||
                   str_contains($_SERVER['PHP_SELF'], '/eventos/')
                   ? '../' : '';
  ?>
  <script src="<?= $path_prefix ?>js/app.js" defer></script>
</head>
<body>

<?php
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }
  $usuario_nombre = $_SESSION['nombre'] ?? null;
  $usuario_rol = $_SESSION['rol'] ?? null;
  $fecha_actual  = date('d/m/Y H:i');
?>

<header>
  <div class="header-container d-flex justify-content-between align-items-center px-4">
<div class="d-flex align-items-center">
  <a href="<?= $path_prefix ?>panel.php">
    <img src="<?= $path_prefix ?>img/seguridad.png" alt="Logo" style="height: 70px; margin-right: 15px;">
  </a>
  <a href="<?= $path_prefix ?>panel.php" style="text-decoration: none; color: inherit;">
    <h1 class="display-5 mb-0" style="font-weight: bold; font-size: 4em; margin-top: -10px;">Plataforma IT</h1>
  </a>
</div>


    <?php if ($usuario_nombre && $usuario_rol): ?>
    <div class="text-end fs-5">
      <div><strong>Usuario:</strong> <?= htmlspecialchars($usuario_nombre) ?></div>
      <div><strong>Rol:</strong> <?= htmlspecialchars($usuario_rol) ?></div>
      <div><strong>Fecha:</strong> <?= $fecha_actual ?></div>
    </div>
    <?php endif; ?>
  </div>
</header>

<!-- === CONTENIDO PRINCIPAL === -->
<div class="w-100 px-4 mt-4">

