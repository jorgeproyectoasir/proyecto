<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../conexion.php';


echo '<div class="contenido-flex">';

// Contenedor principal (izquierda)
echo '<div class="panel-container">';

// Mostrar mensaje flash si existe
if (isset($_SESSION['flash'])) {
    echo "<div class='alert alert-{$_SESSION['flash']['tipo']} alert-dismissible fade show text-center'>
            {$_SESSION['flash']['mensaje']}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Cerrar'></button>
        </div>";
    unset($_SESSION['flash']);
}

echo "<h2>Listado de Incidencias</h2>";

// Obtener incidencias
$sql = "SELECT i.*, u.nombre AS usuario, d.nombre AS dispositivo
        FROM incidencias i
        LEFT JOIN usuarios u ON i.usuario_id = u.id
        LEFT JOIN dispositivos d ON i.dispositivo_id = d.id
        ORDER BY i.fecha DESC";
$resultado = $conexion->query($sql);

// Mostrar tabla
echo "<table class='table-accesos'>
<tr>
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
        <td>{$row['fecha']}</td>
        <td>" . htmlspecialchars($row['descripcion']) . "</td>
        <td>" . htmlspecialchars($row['tipo']) . "</td>
        <td>
            <span class='badge bg-" . ($row['estado'] === 'cerrada' ? 'secondary' : 'warning') . "'>" . ucfirst($row['estado']) . "</span>
        </td>
        <td>" . htmlspecialchars($row['dispositivo'] ?? 'No asignado') . "</td>
        <td>" . htmlspecialchars($row['usuario'] ?? 'No asignado') . "</td>
        <td>";

    if ($row['estado'] === 'abierta' && ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico')) {
        echo "<a href='cerrar.php?id={$row['id']}' class='btn-editar btn-accion me-2'>Cerrar</a>";
    }

    if ($row['estado'] === 'cerrada' && ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico')) {
        echo "<a href='eliminar.php?id={$row['id']}' class='btn btn-danger btn-accion me-2' onclick=\"return confirm('¿Estás seguro de que deseas eliminar esta incidencia? Esta acción no se puede deshacer.')\">Eliminar</a>";
    }

    echo "<a href='comentarios.php?id={$row['id']}' class='btn-ver btn-accion'>Comentarios</a>";

    echo "</td></tr>";
}

echo "</table>";

// Botón para crear
if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'tecnico') {
    echo "<a href='crear.php' class='btn btn-success mt-3'>Crear nueva incidencia</a>";
}

echo "<br><a href='../panel.php' class='btn btn-secondary mt-3'>Volver al panel</a>";

echo '</div>'; // cierre de panel-container

// Aside a la derecha
include_once __DIR__ . '/../includes/aside.php';

echo '</div>'; // cierre de contenido-flex

include '../includes/footer.php';
?>
