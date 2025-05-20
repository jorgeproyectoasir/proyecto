<?php
include '../includes/auth.php';
include '../includes/header.php';
require_once '../includes/log.php';

if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    echo "Acceso denegado.";
    include '../includes/footer.php';
    exit();
}

include '../conexion.php';

$id = intval($_GET['id'] ?? 0);

// Procesar actualización
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $ip = $_POST['ip'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];
    $responsable = $_POST['responsable'];

    $stmt = $conexion->prepare("UPDATE dispositivos SET nombre=?, ip=?, tipo=?, estado=?, responsable=? WHERE id=?");
    $stmt->bind_param("ssssii", $nombre, $ip, $tipo, $estado, $responsable, $id);
    $stmt->execute();

    echo "<p class='text-success'>Dispositivo actualizado.</p>";
    echo "<a href='listar.php' class='btn btn-primary'>Volver</a>";

    include '../includes/footer.php';
    exit();
}

// Cargar datos actuales
$stmt = $conexion->prepare("SELECT * FROM dispositivos WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$dispositivo = $stmt->get_result()->fetch_assoc();

$res = $conexion->query("SELECT id, nombre FROM usuarios");
registrar_log($conexion, $_SESSION["usuario_id"], 'Editó dispositivo ID ' . $id);
?>

<h2>Editar dispositivo</h2>

<form method="POST">
    <div class="mb-3">
        <label>Nombre:</label>
        <input type="text" name="nombre" class="form-control" value="<?= $dispositivo['nombre'] ?>" required>
    </div>
    <div class="mb-3">
        <label>IP:</label>
        <input type="text" name="ip" class="form-control" value="<?= $dispositivo['ip'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Tipo:</label>
        <input type="text" name="tipo" class="form-control" value="<?= $dispositivo['tipo'] ?>" required>
    </div>
    <div class="mb-3">
        <label>Estado:</label>
        <select name="estado" class="form-control">
            <option value="activo" <?= $dispositivo['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= $dispositivo['estado'] == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
            <option value="mantenimiento" <?= $dispositivo['estado'] == 'mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Responsable:</label>
        <select name="responsable" class="form-control">
            <?php while ($fila = $res->fetch_assoc()): ?>
                <option value="<?= $fila['id'] ?>" <?= $fila['id'] == $dispositivo['responsable'] ? 'selected' : '' ?>>
                    <?= $fila['nombre'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Actualizar</button>
</form>

<a href="listar.php" class="btn btn-secondary mt-3">Cancelar</a>

<?php include '../includes/footer.php'; ?>

