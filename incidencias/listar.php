<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../conexion.php';

echo "<h2>Listado de Incidencias</h2>";

if (isset($_GET['eliminado']) && $_GET['eliminado'] == 1) {
    echo "<div class='alert alert-success'>✅ Incidencia eliminada correctamente.</div>";
}

// Obtener incidencias
$sql = "SELECT i.*, u.nombre AS usuario, d.nombre AS dispositivo
        FROM incidencias i
        LEFT JOIN usuarios u ON i.usuario_id = u.id
        LEFT JOIN dispositivos d ON i.dispositivo_id = d.id
        ORDER BY i.fecha DESC";
$resultado = $conexion->query($sql);

// Mostrar tabla
echo "<table class='table table-bordered table-striped'>
<tr>
    <th>ID</th>
    <th>Fecha</th>
    <th>Descripción</th>
    <th>Tipo</th>
    <th>Estado</th>
    <th>Dispositivo</th>
    <th>Usuario</th>
    <th>Acciones</th>
</tr>";

while ($row = $resultado->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['fecha']}</td>
        <td>{$row['descripcion']}</td>
        <td>{$row['tipo']}</td>
        <td>
            <span class='badge bg-" . ($row['estado'] === 'cerrada' ? 'secondary' : 'warning') . "'>
                " . ucfirst($row['estado']) . "
            </span>
        </td>
        <td>{$row['dispositivo']}</td>
        <td>{$row['usuario']}</td>
        <td>";

    // Botón cerrar si está abierta
    if ($row['estado'] === 'abierta' && ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico')) {
        echo "<a href='cerrar.php?id={$row['id']}' class='btn btn-warning btn-sm me-2'>Cerrar</a>";
    }

    // Botón eliminar solo si está cerrada
    if ($row['estado'] === 'cerrada' && ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico')) {
        echo "<a href='eliminar.php?id={$row['id']}' class='btn btn-danger btn-sm eliminar me-2'>Eliminar</a>";
    }

    // Botón comentarios (siempre visible)
    echo "<a href='comentarios.php?id={$row['id']}' class='btn btn-info btn-sm'>Comentarios</a>";

    echo "</td></tr>";
}

echo "</table>";

// Botón para crear
if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico') {
    echo "<a href='crear.php' class='btn btn-success'>Crear nueva incidencia</a>";
}

echo "<br><a href='../panel.php' class='btn btn-secondary mt-3'>Volver al panel</a>";

include '../includes/footer.php';
?>

