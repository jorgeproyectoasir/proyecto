<?php
include '../includes/auth.php';
include '../includes/header.php';
require_once '../includes/log.php';
session_start();

if ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'tecnico') {
    echo "<p class='alert alert-danger text-center'>❌ Acceso denegado.</p>";
    include '../includes/footer.php';
    exit();
}

include '../conexion.php';

$id = intval($_GET['id'] ?? 0);

// Procesar actualización
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $ip = trim($_POST['ip']);
    $tipo = trim($_POST['tipo']);
    $estado = $_POST['estado'];
    $responsable = $_POST['responsable'];

    // Validación de la IP
    if (!preg_match('/^\d{1,3}(\.\d{1,3}){3}$/', $ip)) {
        $_SESSION['flash'] = [
            'tipo' => 'danger',
            'mensaje' => '❌ IP inválida. Debe tener el formato correcto, como 192.168.0.1'
        ];
        header("Location: editar.php?id=$id");
        exit();
    }

    // Actualizar dispositivo en la base de datos
    $stmt = $conexion->prepare("UPDATE dispositivos SET nombre=?, ip=?, tipo=?, estado=?, responsable=? WHERE id=?");
    $stmt->bind_param("ssssii", $nombre, $ip, $tipo, $estado, $responsable, $id);

    if ($stmt->execute()) {
        registrar_log($conexion, $_SESSION["usuario_id"], "Editó dispositivo ID $id");

        $_SESSION['flash'] = [
            'tipo' => 'success',
            'mensaje' => "✅ Dispositivo actualizado correctamente."
        ];
    } else {
        $_SESSION['flash'] = [
            'tipo' => 'danger',
            'mensaje' => "❌ Error al actualizar dispositivo."
        ];
    }

    $stmt->close();
    header("Location: listar.php");
    exit();
}

// Cargar datos actuales
$stmt = $conexion->prepare("SELECT * FROM dispositivos WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$dispositivo = $stmt->get_result()->fetch_assoc();

$res = $conexion->query("SELECT id, nombre FROM usuarios");
?>

<h2>Editar dispositivo</h2>

<!-- Mostrar mensaje flash si existe -->
<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?= $_SESSION['flash']['tipo'] ?> text-center">
        <?= $_SESSION['flash']['mensaje'] ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label>Nombre:</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($dispositivo['nombre']) ?>" required>
    </div>
    <div class="mb-3">
        <label>IP:</label>
        <input type="text" name="ip" class="form-control" value="<?= htmlspecialchars($dispositivo['ip']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Tipo:</label>
        <input type="text" name="tipo" class="form-control" value="<?= htmlspecialchars($dispositivo['tipo']) ?>" required>
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
                    <?= htmlspecialchars($fila['nombre']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Actualizar</button>
</form>

<a href="listar.php" class="btn btn-secondary mt-3">Cancelar</a>

<?php include '../includes/footer.php'; ?>

