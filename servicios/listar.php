<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../conexion.php';
require_once '../includes/log.php';

echo "<h2>Listado de Servicios</h2>";

// Eliminar servicio (solo permitido a administradores)
if (isset($_GET['eliminar']) && $_SESSION['rol'] === 'admin') {
    $id = intval($_GET['eliminar']);
    $stmt = $conexion->prepare("DELETE FROM servicios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    registrar_log($conexion, $_SESSION['usuario_id'], 'eliminar_servicio', "Eliminó el servicio con ID $id");
    echo "<div class='alert alert-success mt-3'>Servicio eliminado correctamente.</div>";
}

// Obtener servicios
$sql = "SELECT * FROM servicios";
$resultado = $conexion->query($sql);

echo "<table class='table table-bordered table-striped mt-3'>
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Descripción</th>
    <th>Tipo</th>
    <th>Estado</th>
    <th>Acciones</th>
</tr>";

while ($row = $resultado->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['nombre']}</td>
        <td>{$row['descripcion']}</td>
        <td>" . ($row['tipo'] ?? '<em>No definido</em>') . "</td>
        <td>" . ucfirst($row['estado']) . "</td>
        <td>";

    // Permitir editar a técnicos y administradores
    if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico') {
        echo "<a href='editar.php?id={$row['id']}' class='btn btn-warning btn-sm me-2'>Editar</a>";
    }

    // Permitir eliminar solo a administradores
    if ($_SESSION['rol'] === 'admin') {
        echo "<a href='listar.php?eliminar={$row['id']}' class='btn btn-danger btn-sm eliminar'>Eliminar</a>";
    }

    echo "</td></tr>";
}

echo "</table>";

// Permitir agregar solo a técnicos y administradores
if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico') {
    echo "<a href='agregar.php' class='btn btn-success'>Agregar nuevo servicio</a>";
}

// Botón volver al panel
echo "<br><a href='../panel.php' class='btn btn-secondary mt-3'>Volver al panel</a>";

include '../includes/footer.php';
?>

