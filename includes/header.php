<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Plataforma IT</title>
    <link rel="stylesheet" href="css/estilo.css?v=<?= time() ?>">
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
    <div class="container mt-4">

