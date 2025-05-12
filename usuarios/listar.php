<?php
include '../includes/auth.php';
include '../includes/header.php';
require_once '../includes/log.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "Acceso denegado.";
    include '../includes/footer.php';
    exit();
}

include '../conexion.php';

// Eliminar usuario (opcional, no puede eliminarse a sí mismo)
if (isset($_GET['eliminar']) && $_GET['eliminar'] != $_SESSION['usuario_id']) {
    $id = intval($_GET['eliminar']);
    $conexion->query("DELETE FROM usuarios WHERE id = $id");
    echo "<p class='text-success'>Usuario eliminado.</p>";
}

// Listado de usuarios
$sql = "SELECT usuarios.id, usuarios.nombre, email, roles.nombre AS rol 
        FROM usuarios 
        JOIN roles ON usuarios.rol_id = roles.id";
$resultado = $conexion->query($sql);

echo "<h2>Gestión de Usuarios</h2>";
echo "<table class='table table-striped'>
        <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Acción</th></tr>";

while ($fila = $resultado->fetch_assoc()) {
    echo "<tr>
            <td>{$fila['id']}</td>
            <td>{$fila['nombre']}</td>
            <td>{$fila['email']}</td>
            <td>{$fila['rol']}</td>
            <td>";
    if ($fila['id'] != $_SESSION['usuario_id']) {
        echo "<a href='listar.php?eliminar={$fila['id']}' class='btn btn-danger btn-sm'>Eliminar</a>";
    } else {
        echo "<span class='text-muted'>Tú mismo</span>";
    }
    echo "</td></tr>";
}

echo "</table>";
echo "<a href='../panel.php' class='btn btn-secondary'>Volver</a>";

include '../includes/footer.php';
?>

