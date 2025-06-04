<?php
require_once '../includes/auth.php';
require_once '../conexion.php';

$mensaje = "";

// Obtener dispositivos y usuarios dinámicamente
$dispositivos = $conexion->query("SELECT id, nombre FROM dispositivos ORDER BY nombre");
$usuarios = $conexion->query("SELECT id, nombre FROM usuarios ORDER BY nombre");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = trim($_POST["descripcion"]);
    $estado = $_POST["estado"];
    $dispositivo_id = intval($_POST["dispositivo"]);
    $tipo = $_POST["tipo"];
    $usuario_id = intval($_POST["usuario"]);

    if (!empty($descripcion) && !empty($estado) && !empty($tipo)) {
	$stmt = $conexion->prepare("INSERT INTO incidencias (descripcion, estado, dispositivo_id, tipo, usuario_id, fecha) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssisi", $descripcion, $estado, $dispositivo_id, $tipo, $usuario_id);

        if ($stmt->execute()) {
            $mensaje = '<div class="alert alert-success">Incidencia creada correctamente.</div>';
        } else {
            $mensaje = '<div class="alert alert-danger">Error al crear la incidencia: ' . htmlspecialchars($stmt->error) . '</div>';
        }

        $stmt->close();
    } else {
        $mensaje = '<div class="alert alert-warning">Todos los campos son obligatorios.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Plataforma IT - Crear Incidencia</title>
  <link rel="stylesheet" href="/css/estilo.css?v=1748854650">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="../js/app.js" defer></script>
</head>
<body>

<header>
  <div class="header-container d-flex justify-content-between align-items-center px-4">
    <h1 class="display-5 mb-0" style="font-weight: bold; font-size: 4.5em; margin-top: -10px;">Plataforma IT</h1>
    <div class="text-end fs-5">
      <div><strong>Usuario:</strong> <?= htmlspecialchars($_SESSION["usuario"]) ?></div>
      <div><strong>Rol:</strong> <?= htmlspecialchars($_SESSION["rol"]) ?></div>
      <div><strong>Fecha:</strong> <?= date("d/m/Y H:i") ?></div>
    </div>
  </div>
</header>

<div class="w-100 px-4 mt-4">
<div class="contenido-flex">
<div class="panel-container">
    <h2 class="titulos">Crear nueva incidencia</h2>

    <?= $mensaje ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label>Descripción:</label>
            <input type="text" name="descripcion" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Estado:</label>
            <select name="estado" class="form-control" required>
                <option value="abierta">Abierta</option>
                <option value="cerrada">Cerrada</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Dispositivo:</label>
            <select name="dispositivo" class="form-control" required>
                <?php while($d = $dispositivos->fetch_assoc()): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Tipo:</label>
            <select name="tipo" class="form-control" required>
                <option value="error">Error</option>
                <option value="aviso">Aviso</option>
                <option value="mantenimiento">Mantenimiento</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Usuario:</label>
            <select name="usuario" class="form-control" required>
                <?php while($u = $usuarios->fetch_assoc()): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="botones-centrados">
            <button type="submit" class="btn btn-success boton-accion">Crear</button>
            <a href="listar.php" class="btn btn-secondary boton-accion">Cancelar</a>
        </div>
    </form>
</div>

<aside class="aside-estandar">
    <h3>Acerca de Plataforma IT</h3>
    <p>Esta plataforma permite gestionar incidencias, tareas y dispositivos de manera eficiente.</p>
    <img src="/img/aside.jpg" alt="Nuestra Plataforma" class="img-fluid">
    <h3>Beneficios clave</h3>
    <ul>
        <li>Automatización de procesos IT</li>
        <li>Integración con múltiples sistemas</li>
        <li>Interfaz intuitiva y fácil de usar</li>
    </ul>
</aside>
</div>
</div>

<footer class="mt-5 text-white py-4">
  <div class="container text-center" style="margin-top: 10px;">
    <p class="mb-2">&copy; 2025 Plataforma IT. Todos los derechos reservados.</p>
    <p class="mb-0">Desarrollado por <strong>Jorge Juncá López</strong> | Proyecto ASIR</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

