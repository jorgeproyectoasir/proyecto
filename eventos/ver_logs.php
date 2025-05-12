<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

registrar_log($conexion, $_SESSION['id'], "Visualizó los eventos del sistema");

// Cargar logs con nombre del usuario
$sql = "SELECT logs.*, usuarios.nombre FROM logs 
        LEFT JOIN usuarios ON logs.usuario_id = usuarios.id
        ORDER BY fecha DESC";
$resultado = $conexion->query($sql);
?>

<?php include '../includes/header.php'; ?>
<h2>Eventos del sistema</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Fecha</th>
        <th>Usuario</th>
        <th>Acción</th>
    </tr>
    <?php while ($fila = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= $fila['fecha'] ?></td>
            <td><?= htmlspecialchars($fila['nombre']) ?></td>
            <td><?= htmlspecialchars($fila['accion']) ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include '../includes/footer.php'; ?>

