<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Plataforma IT</title>
  <link rel="stylesheet" href="/css/estilo.css?v=<?= time() ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
  $usuario_nombre = $_SESSION['nombre'] ?? 'Invitado';
  $fecha_actual  = date('d/m/Y H:i');
?>

<header>
  <div class="header-container d-flex justify-content-between align-items-center">
      <h1 class="display-5 mb-0" style="font-weight: bold; font-size: 4.5em; margin-top: -10px; margin-left: -50px;">Plataforma IT</h1>
      <div class="text-end fs-5">
        <div><strong>Usuario:</strong> <?= htmlspecialchars($usuario_nombre) ?></div>
        <div><strong>Rol:</strong> <?= $_SESSION['rol']; ?></div>
	<div><strong>Fecha:</strong> <?= $fecha_actual ?></div>
      </div>
  </div>
</header>


<!-- === CONTENIDO PRINCIPAL === -->
<div class="w-100 px-4 mt-4">

