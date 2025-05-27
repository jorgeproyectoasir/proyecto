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

// Mostrar últimos 30 logs
$sql = "SELECT logs.fecha, usuarios.nombre AS usuario, logs.accion
        FROM logs
        LEFT JOIN usuarios ON logs.usuario_id = usuarios.id
        ORDER BY fecha DESC
        LIMIT 30";
$resultado = $conexion->query($sql);
?>

<div class="contenido-flex">
    <div class="panel-container">
        <h2>Registro de actividad</h2>

        <table class="table-accesos">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= $log['fecha'] ?></td>
                        <td><?= htmlspecialchars($log['usuario'] ?? 'Desconocido') ?></td>
                        <td><?= htmlspecialchars($log['accion']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href='../panel.php' class='btn btn-secondary mt-3'>Volver al panel</a>
    </div>

    <?php include_once '../includes/aside.php'; ?>
</div>

<?php include '../includes/footer.php'; ?>
