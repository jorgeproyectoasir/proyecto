<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


include '../includes/auth.php';
include '../includes/header.php';
require_once '../includes/log.php';
include '../conexion.php';

echo "<h2>Listado de Dispositivos</h2>";

// Eliminar dispositivo (solo admin o técnico)
if (
    isset($_GET['eliminar']) &&
    ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico')
) {
    $id = intval($_GET['eliminar']);
    $conexion->query("DELETE FROM dispositivos WHERE id = $id");
    echo "<p class='text-success'>Dispositivo eliminado correctamente.</p>";
}

// Obtener dispositivos
$sql = "SELECT dispositivos.id, dispositivos.nombre AS dispositivo_nombre, dispositivos.ip, dispositivos.estado,
               usuarios.nombre AS responsable, dispositivos.tipo
        FROM dispositivos
        LEFT JOIN usuarios ON dispositivos.responsable = usuarios.id";

$resultado = $conexion->query($sql);

echo "<table class='table table-bordered'>
        <tr><th>ID</th><th>Nombre</th><th>IP</th><th>Tipo</th><th>Estado</th><th>Responsable</th><th>Acciones</th></tr>";

while ($row = $resultado->fetch_assoc()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['dispositivo_nombre']}</td>
            <td>{$row['ip']}</td>
            <td>{$row['tipo']}</td>
            <td>{$row['estado']}</td>
            <td>{$row['responsable']}</td>
            <td>";

    // Solo admin o técnico pueden eliminar
    if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico') {
        echo "<a href='listar.php?eliminar={$row['id']}' class='btn btn-danger btn-sm eliminar'>Eliminar</a>";
	echo "<a href='editar.php?id={$row['id']}' class='btn btn-warning btn-sm me-2'>Editar</a>";
    }

    echo "</td></tr>";
}

echo "</table>";

// Solo admin o técnico pueden añadir
if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico') {
    echo "<a href='agregar.php' class='btn btn-success'>Agregar nuevo dispositivo</a>";
}

echo "<br><a href='../panel.php' class='btn btn-secondary mt-3'>Volver</a>";

include '../includes/footer.php';
?>

