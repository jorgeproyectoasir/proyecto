<?php
session_start();

require_once '../conexion.php';
require_once '../includes/auth.php';

$mensaje = "";
$clase_alerta = "";

$nombre = $ip = $estado = $tipo = "";
$responsable = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $ip = trim($_POST['ip'] ?? '');
    $estado = trim($_POST['estado'] ?? 'activo'); // valor por defecto
    $tipo = trim($_POST['tipo'] ?? '');
    $responsable = !empty($_POST['responsable']) ? intval($_POST['responsable']) : null;

    if ($nombre === '') {
        $mensaje = "El nombre es obligatorio.";
        $clase_alerta = "alert alert-danger";
    } else {
        $sql = "INSERT INTO dispositivos (nombre, ip, estado, responsable, tipo) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conexion->prepare($sql)) {
            // Como responsable puede ser NULL, bind_param no acepta directamente null,
            // usamos un truco pasando NULL o valor
            if ($responsable === null) {
                $stmt->bind_param('sssis', $nombre, $ip, $estado, $nullVar, $tipo);
                $nullVar = null;
            } else {
                $stmt->bind_param('sssis', $nombre, $ip, $estado, $responsable, $tipo);
            }
            if ($stmt->execute()) {
                $mensaje = "Dispositivo agregado correctamente.";
                $clase_alerta = "alert alert-success";
                $nombre = $ip = $estado = $tipo = "";
                $responsable = null;
            } else {
                $mensaje = "Error al agregar el dispositivo: " . htmlspecialchars($stmt->error);
                $clase_alerta = "alert alert-danger";
            }
            $stmt->close();
        } else {
            $mensaje = "Error en la consulta: " . htmlspecialchars($conexion->error);
            $clase_alerta = "alert alert-danger";
        }
    }
}

?>

<?php include_once '../includes/header.php'; ?>
<?php include_once '../includes/aside.php'; ?>
<style>
 html, body {
        margin: 0;
        padding: 0;
        background-color: #B0D0FF;
    }
</style>
<div class="container mt-4">
    <h2>Agregar nuevo dispositivo</h2>

    <?php if ($mensaje): ?>
        <div class="<?= $clase_alerta ?>" role="alert">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre *</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required value="<?= htmlspecialchars($nombre) ?>">
        </div>

        <div class="mb-3">
            <label for="ip" class="form-label">IP</label>
            <input type="text" name="ip" id="ip" class="form-control" value="<?= htmlspecialchars($ip) ?>">
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-select">
                <option value="activo" <?= $estado === 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= $estado === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                <option value="mantenimiento" <?= $estado === 'mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="responsable" class="form-label">Responsable (ID usuario)</label>
            <input type="number" name="responsable" id="responsable" class="form-control" value="<?= htmlspecialchars($responsable) ?>">
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <input type="text" name="tipo" id="tipo" class="form-control" value="<?= htmlspecialchars($tipo) ?>">
        </div>

        <button type="submit" class="btn btn-primary">Agregar dispositivo</button>
        <a href="listar.php" class="btn btn-secondary ms-2">Volver a la lista</a>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>

