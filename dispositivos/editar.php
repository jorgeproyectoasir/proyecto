<?php
include '../includes/header.php';
include '../includes/aside.php';
include '../conexion.php';

if (!isset($_GET['id'])) {
    echo "<div class='alerta-error'>ID de dispositivo no especificado.</div>";
    include '../includes/footer.php';
    exit;
}

$id = intval($_GET['id']);

// Procesar actualización si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $tipo = trim($_POST['tipo']);
    $ip = trim($_POST['ip']);
    $estado = $_POST['estado'];
    $responsable = intval($_POST['responsable']);

    $stmt = $conn->prepare("UPDATE dispositivos SET nombre=?, tipo=?, ip=?, estado=?, responsable=? WHERE id=?");
    $stmt->bind_param("ssssii", $nombre, $tipo, $ip, $estado, $responsable, $id);
    $stmt->execute();

    echo "<div class='alerta-exito'>Dispositivo actualizado correctamente.</div>";
    echo "<script>setTimeout(() => { window.location.href = 'listar.php'; }, 2000);</script>";
    include '../includes/footer.php';
    exit;
}

// Obtener datos actuales del dispositivo
$stmt = $conn->prepare("SELECT * FROM dispositivos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<div class='alerta-error'>Dispositivo no encontrado.</div>";
    include '../includes/footer.php';
    exit;
}

$dispositivo = $resultado->fetch_assoc();
?>

<h2 class="titulos">Editar Dispositivo</h2>

<form method="POST" class="formulario">
    <div class="mb-3">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" class="form-control" required value="<?= htmlspecialchars($dispositivo['nombre']) ?>">
    </div>

    <div class="mb-3">
        <label for="tipo">Tipo:</label>
        <input type="text" id="tipo" name="tipo" class="form-control" required value="<?= htmlspecialchars($dispositivo['tipo']) ?>">
    </div>

    <div class="mb-3">
        <label for="ip">IP:</label>
        <input type="text" id="ip" name="ip" class="form-control" required value="<?= htmlspecialchars($dispositivo['ip']) ?>">
    </div>

    <div class="mb-3">
        <label for="estado">Estado:</label>
        <select name="estado" id="estado" class="form-control" required>
            <option value="activo" <?= $dispositivo['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= $dispositivo['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
            <option value="mantenimiento" <?= $dispositivo['estado'] === 'mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="responsable">Responsable (ID usuario):</label>
        <input type="number" id="responsable" name="responsable" class="form-control" required value="<?= htmlspecialchars($dispositivo['responsable']) ?>">
    </div>

    <div class="botones-centrados">
        <button type="submit" class="btn btn-success boton-accion">Guardar Cambios</button>
        <a href="listar.php" class="btn btn-secondary boton-accion">Cancelar</a>
    </div>
</form>

<?php include '../includes/footer.php'; ?>

