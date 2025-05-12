<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../conexion.php';
require_once '../includes/log.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "Acceso denegado.";
    include '../includes/footer.php';
    exit();
}

$sql = "SELECT logs.*, usuarios.nombre AS usuario 
        FROM logs 
        LEFT JOIN usuarios ON logs.usuario_id = usuarios.id 
        ORDER BY fecha DESC";
$resultado = $conexion->query($sql);

echo "<h2>Registro de actividad</h2>";
echo "<table class='table table-striped'>
<tr><th>Fecha</th><th>Usuario</th><th>Acción</th></tr>";

while ($log = $resultado->fetch_assoc()) {
    echo "<tr>
            <td>{$log['fecha']}</td>
            <td>" . htmlspecialchars($log['usuario']) . "</td>
            <td>" . htmlspecialchars($log['accion']) . "</td>
          </tr>";
}
echo "</table>";

include '../includes/footer.php';
?>

