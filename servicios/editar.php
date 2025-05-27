<?php
include '../includes/auth.php';
include '../includes/header.php';

if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    echo "Acceso denegado.";
    include '../includes/footer.php';
    exit();
}

include '../conexion.php';

$id = intval($_GET['id'] ?? 0);

// Guardar cambios
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];
    $dispositivo = $_POST['dispositivo'];

    $stmt = $conexion->prepare("UPDATE servicios SET nombre=?, tipo=?, estado=?, dispositivo_id=? WHERE id=?");
    $stmt->bind_param("sssii", $nombre, $tipo, $estado, $dispositivo, $id);
    $stmt->execute();

    header("Location: listar.php?msg=Servicio actualizado correctamente.");
    exit();

    include '../includes/footer.php';
    exit();
}

// Obtener datos actuales
$stmt = $conexion->prepare("SELECT * FROM servicios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$servicio = $stmt->get_result()->fetch_assoc();

// Obtener dispositivos
$dispositivos = $conexion->query("SELECT id, nombre FROM dispositivos");
?>

<h2>Editar servicio</h2>

<form method="POST">
    <div class="mb-3">
        <label>Nombre:</label>
        <input type="text" name="nombre" class="form-control" value="<?= $servicio['nombre'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Tipo:</label>
        <input type="text" name="tipo" class="form-control" value="<?= $servicio['tipo'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Estado:</label>
        <select name="estado" class="form-control">
            <option value="activo" <?= $servicio['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= $servicio['estado'] == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
            <option value="error" <?= $servicio['estado'] == 'error' ? 'selected' : '' ?>>Error</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Dispositivo asociado:</label>
        <select name="dispositivo" class="form-control">
            <?php while ($d = $dispositivos->fetch_assoc()): ?>
                <option value="<?= $d['id'] ?>" <?= $d['id'] == $servicio['dispositivo_id'] ? 'selected' : '' ?>>
                    <?= $d['nombre'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Actualizar</button>
</form>

<a href="listar.php" class="btn btn-secondary mt-3">Cancelar</a>

<?php include '../includes/footer.php'; ?>

