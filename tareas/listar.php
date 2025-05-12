<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

registrar_log($conexion, $_SESSION['id'], "Consultó tareas programadas");

$rol = $_SESSION['rol'];

if ($rol === 'admin') {
    $sql = "SELECT tareas.*, usuarios.nombre AS tecnico FROM tareas 
            LEFT JOIN usuarios ON tareas.tecnico_id = usuarios.id 
            ORDER BY programada_para ASC";
    $resultado = $conexion->query($sql);
} else {
    $usuario_id = $_SESSION['id'];
    $sql = "SELECT * FROM tareas WHERE tecnico_id = ? ORDER BY programada_para ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
}
?>

<?php include '../includes/header.php'; ?>
<h2>Tareas programadas</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Título</th>
        <th>Descripción</th>
        <th>Fecha</th>
        <th>Estado</th>
        <?php if ($rol === 'admin'): ?>
            <th>Técnico</th>
        <?php endif; ?>
    </tr>
    <?php while ($fila = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($fila['titulo']) ?></td>
            <td><?= htmlspecialchars($fila['descripcion']) ?></td>
            <td><?= $fila['programada_para'] ?></td>
            <td><?= $fila['estado'] ?></td>
            <?php if ($rol === 'admin'): ?>
                <td><?= htmlspecialchars($fila['tecnico']) ?></td>
            <?php endif; ?>
        </tr>
    <?php endwhile; ?>
</table>

<?php include '../includes/footer.php'; ?>

