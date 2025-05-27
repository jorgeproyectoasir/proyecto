<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre      = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $estado      = $_POST['estado'] ?? 'activo';
    $tipo        = trim($_POST['tipo']);

    if (empty($nombre)) {
        $mensaje = "El nombre es obligatorio.";
    } elseif (empty($tipo)) {
        $mensaje = "El tipo es obligatorio.";
    } else {
        $stmt = $conexion->prepare(
            "INSERT INTO servicios (nombre, descripcion, estado, tipo) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $nombre, $descripcion, $estado, $tipo);
        $stmt->execute();

        registrar_log($conexion, $_SESSION["usuario_id"], "Agregó un nuevo servicio: $nombre");
        header("Location: listar.php?msg=Servicio agregado correctamente.");
        exit();

        $stmt->close();
    }
}
?>

<?php include '../includes/header.php'; ?>

<h2>Agregar Servicio</h2>

<?php if (!empty($mensaje)): ?>
    <div class="alert <?= strpos($mensaje, 'correctamente') !== false ? 'alert-success' : 'alert-danger' ?> mt-3">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<form method="POST" action="">
    <label>Nombre del servicio:</label><br>
    <input type="text" name="nombre" required><br><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion"></textarea><br><br>

    <label>Tipo:</label><br>
    <input type="text" name="tipo" required placeholder="Ej: mantenimiento"><br><br>

    <label>Estado:</label><br>
    <select name="estado">
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
    </select><br><br>

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>

<a href="listar.php" class="btn btn-secondary mt-3">Volver a la lista</a>

<?php include '../includes/footer.php'; ?>

