<?php
include '../includes/auth.php';
include '../conexion.php';
require_once '../includes/log.php';

registrar_log($conexion, $_SESSION['id'], "Entró en la lista de servicios");

$resultado = $conexion->query("SELECT * FROM servicios");
?>

<?php include '../includes/header.php'; ?>
<h2>Listado de Servicios</h2>

<a href="agregar.php">➕ Agregar nuevo servicio</a><br><br>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Estado</th>
    </tr>
    <?php while ($fila = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= $fila['id'] ?></td>
            <td><?= htmlspecialchars($fila['nombre']) ?></td>
            <td><?= htmlspecialchars($fila['descripcion']) ?></td>
            <td><?= $fila['estado'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include '../includes/footer.php'; ?>

