<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../conexion.php';
require_once '../includes/log.php';

$rol = $_SESSION['rol'];

// Solo admin y técnico pueden entrar
if ($rol !== 'admin' && $rol !== 'tecnico') {
    echo "<div class='alert alert-danger'>Acceso denegado.</div>";
    include '../includes/footer.php';
    exit();
}

// Mostrar últimos 30 logs con más detalles
$sql = "SELECT logs.*, usuarios.nombre AS usuario
        FROM logs
        LEFT JOIN usuarios ON logs.usuario_id = usuarios.id
        ORDER BY fecha DESC
        LIMIT 30";

$resultado = $conexion->query($sql);
?>

<h2>Registro de actividad</h2>

<table class='table table-striped'>
    <tr>
        <th>Fecha</th>
        <th>Usuario</th>
        <th>Acción</th>
        <th>Entidad</th>
        <th>Detalle</th>
        <th>IP</th>
        <th>Navegador</th>
    </tr>
    <?php while ($log = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= $log['fecha'] ?></td>
            <td><?= htmlspecialchars($log['usuario'] ?? 'Desconocido') ?></td>
            <td><?= htmlspecialchars($log['accion']) ?></td>
            <td><?= htmlspecialchars($log['entidad_afectada'] ?? '-') ?></td>
            <td><?= htmlspecialchars($log['detalle_extra'] ?? '-') ?></td>
            <td><?= $log['ip'] ?></td>
            <td><?= substr(htmlspecialchars($log['user_agent']), 0, 40) ?>...</td>
        </tr>
    <?php endwhile; ?>
</table>

<a href='../panel.php' class='btn btn-secondary mt-3'>Volver al panel</a>

<?php include '../includes/footer.php'; ?>

