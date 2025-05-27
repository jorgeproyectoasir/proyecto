<?php
include 'includes/auth.php';
include 'includes/header.php';
include 'conexion.php';

$entidad = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$entidad_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!in_array($entidad, ['tarea', 'incidencia']) || $entidad_id <= 0) {
    echo "<p class='alert alert-danger'>Solicitud no válida.</p>";
    include 'includes/footer.php';
    exit();
}

// Verificamos que exista la entidad
$tabla = $entidad === 'tarea' ? 'tareas' : 'incidencias';
$verificar = $conexion->prepare("SELECT id FROM $tabla WHERE id = ?");
$verificar->bind_param("i", $entidad_id);
$verificar->execute();
$resultado = $verificar->get_result();

if ($resultado->num_rows === 0) {
    echo "<p class='alert alert-warning'>No se encontró la $entidad solicitada.</p>";
    include 'includes/footer.php';
    exit();
}

// Cargamos historial
$stmt = $conexion->prepare("
    SELECT h.*, u.nombre 
    FROM historial h 
    LEFT JOIN usuarios u ON h.usuario_id = u.id 
    WHERE h.entidad = ? AND h.entidad_id = ?
    ORDER BY h.fecha DESC
");
$stmt->bind_param("si", $entidad, $entidad_id);
$stmt->execute();
$historial = $stmt->get_result();
?>

<h2>Historial de <?= $entidad ?> #<?= $entidad_id ?></h2>

<?php if ($historial->num_rows > 0): ?>
    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($evento = $historial->fetch_assoc()): ?>
                <tr>
                    <td><?= $evento['fecha'] ?></td>
                    <td><?= htmlspecialchars($evento['nombre'] ?? 'Desconocido') ?></td>
                    <td><?= htmlspecialchars($evento['accion']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No hay eventos registrados aún para esta <?= $entidad ?>.</p>
<?php endif; ?>

<a href="panel.php" class="btn btn-secondary mt-3">Volver al panel</a>

<?php include 'includes/footer.php'; ?>

