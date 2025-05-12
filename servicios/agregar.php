<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $estado = $_POST['estado'] ?? 'activo';

    if (!empty($nombre)) {
        $stmt = $conexion->prepare("INSERT INTO servicios (nombre, descripcion, estado) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $descripcion, $estado);
        $stmt->execute();

        registrar_log($conexion, $_SESSION['id'], "Agregó un nuevo servicio: $nombre");
        $mensaje = "Servicio agregado correctamente.";
    } else {
        $mensaje = "El nombre es obligatorio.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<h2>Agregar Servicio</h2>

<?php if (!empty($mensaje)) echo "<p>$mensaje</p>"; ?>

<form method="POST" action="">
    <label>Nombre del servicio:</label><br>
    <input type="text" name="nombre" required><br><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion"></textarea><br><br>

    <label>Estado:</label><br>
    <select name="estado">
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
    </select><br><br>

    <button type="submit">Guardar</button>
</form>

<a href="listar.php">Volver a la lista</a>
<?php include '../includes/footer.php'; ?>

